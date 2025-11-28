<?php
/**
 * GitHub Updater Class
 * 
 * Enables automatic updates for WordPress plugins hosted on GitHub.
 * Checks for new releases and integrates with WordPress's native update system.
 * 
 * @package Landing_Pages
 * @author Lumnav
 * @version 1.0.17
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Lumnav_GitHub_Updater
{
    private $file;
    private $plugin;
    private $basename;
    private $slug;
    private $active;
    private $username;
    private $repository;
    private $authorize_token;
    private $github_response;

    /**
     * Initialize updater
     */
    public function __construct($file, $username, $repository, $authorize_token = '')
    {
        $this->file = $file;

        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $this->plugin = get_plugin_data($this->file);

        // Extra headers
        $extra_headers = get_file_data($this->file, array(
            'TestedUpTo' => 'Tested up to',
            'RequiresWP' => 'Requires at least',
            'RequiresPHP' => 'Requires PHP',
        ));

        foreach ($extra_headers as $key => $value) {
            if (!empty($value)) {
                $this->plugin[$key] = $value;
            }
        }

        $this->basename = plugin_basename($this->file);
        $this->slug = current(explode('/', $this->basename)); // IMPORTANT FIX
        $this->active = is_plugin_active($this->basename);
        $this->username = $username;
        $this->repository = $repository;
        $this->authorize_token = $authorize_token;

        add_filter('pre_set_site_transient_update_plugins', array($this, 'modify_transient'), 10, 1);
        add_filter('plugins_api', array($this, 'plugin_popup'), 10, 3);
        add_filter('upgrader_post_install', array($this, 'after_install'), 10, 3);
    }

    /**
     * GitHub API call
     */
    private function get_repository_info()
    {
        if (!is_null($this->github_response)) {
            return;
        }

        $request_uri = sprintf('https://api.github.com/repos/%s/%s/releases/latest', $this->username, $this->repository);

        $args = array();
        if ($this->authorize_token) {
            $args['headers'] = array(
                'Authorization' => "token {$this->authorize_token}",
            );
        }

        $response = wp_remote_get($request_uri, $args);

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            $this->github_response = false;
            return;
        }

        $this->github_response = json_decode(wp_remote_retrieve_body($response));
    }

    /**
     * Parse readme.txt sections
     */
    private function parse_readme()
    {
        $readme_file = dirname($this->file) . '/readme.txt';

        if (!file_exists($readme_file)) {
            return array();
        }

        $content = file_get_contents($readme_file);
        $sections = array();

        if (preg_match('/== Description ==\s*(.+?)(?=\s*==|$)/s', $content, $m)) {
            $sections['Description'] = trim($m[1]);
        }
        if (preg_match('/== Installation ==\s*(.+?)(?=\s*==|$)/s', $content, $m)) {
            $sections['Installation'] = trim($m[1]);
        }
        if (preg_match('/== Changelog ==\s*(.+?)(?=\s*==|$)/s', $content, $m)) {
            $sections['Changelog'] = trim($m[1]);
        }
        if (preg_match('/== Frequently Asked Questions ==\s*(.+?)(?=\s*==|$)/s', $content, $m)) {
            $sections['FAQ'] = trim($m[1]);
        }

        return $sections;
    }

    private function log($msg)
    {
        error_log("[GitHub Updater] " . $msg);
    }

    /**
     * Inject update info into WP updater
     */
    public function modify_transient($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        $this->get_repository_info();

        if ($this->github_response === false || !isset($this->github_response->tag_name)) {
            return $transient;
        }

        $github_version = ltrim($this->github_response->tag_name, 'v');
        $current_version = $this->plugin['Version'];

        if (version_compare($github_version, $current_version, 'gt')) {

            $plugin = array(
                'slug' => $this->slug,
                'plugin' => $this->basename,
                'new_version' => $github_version,
                'url' => $this->plugin['PluginURI'],
                'package' => $this->github_response->zipball_url,
                'requires' => $this->plugin['RequiresWP'],
                'tested' => $this->plugin['TestedUpTo'],
                'requires_php' => $this->plugin['RequiresPHP'],
            );

            $transient->response[$this->basename] = (object) $plugin;
        }

        return $transient;
    }

    /**
     * Plugin details modal popup (critical for description!)
     */
    public function plugin_popup($result, $action, $args)
    {
        if ($action !== 'plugin_information' || empty($args->slug)) {
            return $result;
        }

        // Must match the slug WordPress requests
        if ($args->slug !== $this->slug) {
            return $result;
        }

        $this->get_repository_info();

        if ($this->github_response === false) {
            return $result;
        }

        $github_version = ltrim($this->github_response->tag_name, 'v');

        $plugin = array(
            'name' => $this->plugin['Name'],
            'slug' => $this->slug,  // ✔ FIXED — MUST MATCH WP REQUEST
            'version' => $github_version,
            'author' => $this->plugin['AuthorName'],
            'author_profile' => $this->plugin['AuthorURI'],
            'last_updated' => $this->github_response->published_at,
            'homepage' => $this->plugin['PluginURI'],
            'short_description' => $this->plugin['Description'],
            'sections' => array_merge(
                $this->parse_readme(),
                array('Updates' => $this->github_response->body)
            ),
            'download_link' => $this->github_response->zipball_url,
            'requires' => $this->plugin['RequiresWP'],
            'tested' => $this->plugin['TestedUpTo'],
            'requires_php' => $this->plugin['RequiresPHP'],
        );

        return (object) $plugin;
    }

    /**
     * Fix folder names + finalize install
     */
    public function after_install($response, $hook_extra, $result)
    {
        global $wp_filesystem;

        $plugin_dir = dirname($this->file);
        $plugin_folder = basename($plugin_dir);

        $destination = $result['destination'];
        $proper_destination = dirname($destination) . '/' . $plugin_folder;

        if ($wp_filesystem->exists($proper_destination)) {
            $wp_filesystem->delete($proper_destination, true);
        }

        $wp_filesystem->move($destination, $proper_destination);
        $result['destination'] = $proper_destination;

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($this->file, true);
        }

        if ($this->active) {
            activate_plugin($this->basename);
        }

        delete_site_transient('update_plugins');
        wp_clean_plugins_cache(true);

        return $result;
    }
}
