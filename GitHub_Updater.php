<?php
/**
 * GitHub Updater Class
 * 
 * Enables automatic updates for WordPress plugins hosted on GitHub.
 * Checks for new releases and integrates with WordPress's native update system.
 * 
 * @author Lumnav
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class GitHub_Updater
{
    private $file;
    private $plugin;
    private $basename;
    private $active;
    private $username;
    private $repository;
    private $authorize_token;
    private $github_response;

    /**
     * Initialize the updater
     * 
     * @param string $file Plugin file path (__FILE__)
     * @param string $username GitHub username or organization
     * @param string $repository GitHub repository name
     * @param string $authorize_token Optional GitHub personal access token for private repos
     */
    public function __construct($file, $username, $repository, $authorize_token = '')
    {
        $this->file = $file;
        $this->plugin = get_plugin_data($this->file);
        $this->basename = plugin_basename($this->file);
        $this->active = is_plugin_active($this->basename);
        $this->username = $username;
        $this->repository = $repository;
        $this->authorize_token = $authorize_token;

        add_filter('pre_set_site_transient_update_plugins', array($this, 'modify_transient'), 10, 1);
        add_filter('plugins_api', array($this, 'plugin_popup'), 10, 3);
        add_filter('upgrader_post_install', array($this, 'after_install'), 10, 3);
    }

    /**
     * Get information from GitHub API
     */
    private function get_repository_info()
    {
        if (is_null($this->github_response)) {
            $request_uri = sprintf('https://api.github.com/repos/%s/%s/releases/latest', $this->username, $this->repository);

            $args = array();
            if ($this->authorize_token) {
                $args['headers'] = array(
                    'Authorization' => "token {$this->authorize_token}"
                );
            }

            $response = wp_remote_get($request_uri, $args);

            if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
                return false;
            }

            $this->github_response = json_decode(wp_remote_retrieve_body($response));
        }
    }

    /**
     * Modify the plugin transient to include GitHub updates
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

        // Strip 'v' prefix from tag if present (e.g., v1.0.1 -> 1.0.1)
        $github_version = ltrim($this->github_response->tag_name, 'v');
        $current_version = $this->plugin['Version'];

        $out_of_date = version_compare($github_version, $current_version, 'gt');

        if ($out_of_date) {
            $plugin = array(
                'slug' => current(explode('/', $this->basename)),
                'plugin' => $this->basename,
                'new_version' => $github_version,
                'url' => $this->plugin['PluginURI'],
                'package' => $this->github_response->zipball_url,
            );

            $transient->response[$this->basename] = (object) $plugin;
        }

        return $transient;
    }

    /**
     * Provide plugin information for the update popup
     */
    public function plugin_popup($result, $action, $args)
    {
        if ($action !== 'plugin_information') {
            return $result;
        }

        if (!empty($args->slug)) {
            if ($args->slug == current(explode('/', $this->basename))) {
                $this->get_repository_info();

                if ($this->github_response === false || !isset($this->github_response->tag_name)) {
                    return $result;
                }

                // Strip 'v' prefix from tag if present
                $github_version = ltrim($this->github_response->tag_name, 'v');

                $plugin = array(
                    'name' => $this->plugin['Name'],
                    'slug' => $this->basename,
                    'version' => $github_version,
                    'author' => $this->plugin['AuthorName'],
                    'author_profile' => $this->plugin['AuthorURI'],
                    'last_updated' => $this->github_response->published_at,
                    'homepage' => $this->plugin['PluginURI'],
                    'short_description' => $this->plugin['Description'],
                    'sections' => array(
                        'Description' => $this->plugin['Description'],
                        'Updates' => $this->github_response->body,
                    ),
                    'download_link' => $this->github_response->zipball_url,
                    'requires' => '5.0',
                    'tested' => '6.7',
                    'requires_php' => '7.0',
                );

                return (object) $plugin;
            }
        }

        return $result;
    }

    /**
     * Ensure proper folder structure after update
     */
    public function after_install($response, $hook_extra, $result)
    {
        global $wp_filesystem;

        $install_directory = plugin_dir_path($this->file);
        $wp_filesystem->move($result['destination'], $install_directory);
        $result['destination'] = $install_directory;

        if ($this->active) {
            activate_plugin($this->basename);
        }

        return $result;
    }
}
