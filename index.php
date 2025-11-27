<?php
/**
 * Plugin Name: Landing Pages
 * Description: A landing page plugin with a simple on/off toggle in the admin bar.
 * Version: 1.0.7
 * Author: Lumnav
 * Author URI: https://lumnav.com
 * License: GPL-2.0+
 */

// Prevent direct access to this file
if (!defined('ABSPATH')) {
    exit;
}

define('LUMNAV_MM_OPTION', 'lumnav_maintenance_mode_status');
define('LUMNAV_MM_STYLE_OPTION', 'lumnav_maintenance_mode_style');

// GitHub Auto-Update Configuration
// TODO: Update these values with your actual GitHub repository details
define('LUMNAV_GITHUB_USERNAME', 'Lumnav'); // Change to your GitHub username or organization
define('LUMNAV_GITHUB_REPO', 'Maintenance-Mode-WP-Plugin'); // Change to your repository name

// Initialize GitHub Auto-Updater
require_once plugin_dir_path(__FILE__) . 'GitHub_Updater.php';
if (class_exists('GitHub_Updater')) {
    new GitHub_Updater(__FILE__, LUMNAV_GITHUB_USERNAME, LUMNAV_GITHUB_REPO);
}


/**
 * Adds the maintenance mode toggle to the WordPress admin bar.
 *
 * @param WP_Admin_Bar $wp_admin_bar The admin bar object.
 */
function lumnav_mm_admin_bar_item($wp_admin_bar)
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $status = get_option(LUMNAV_MM_OPTION, 'off');
    $is_on = ($status === 'on');

    $toggle_url = add_query_arg(
        array(
            'action' => 'lumnav_toggle_mm',
            '_wpnonce' => wp_create_nonce('lumnav_toggle_mm_nonce'),
        ),
        admin_url('index.php')
    );

    $wp_admin_bar->add_node(
        array(
            'id' => 'lumnav-maintenance-mode',
            'title' => $is_on ? 'Landing Page: ON' : 'Landing Page: OFF',
            'href' => esc_url($toggle_url),
            'meta' => array(
                'class' => $is_on ? 'lumnav-mm-on' : 'lumnav-mm-off',
                'title' => $is_on ? 'Click to disable Landing Page' : 'Click to enable Landing Page',
            ),
        )
    );
}
add_action('admin_bar_menu', 'lumnav_mm_admin_bar_item', 100);

/**
 * Handles the logic for toggling the maintenance mode status.
 */
function lumnav_mm_toggle_status()
{
    if (
        isset($_GET['action']) &&
        $_GET['action'] === 'lumnav_toggle_mm' &&
        isset($_GET['_wpnonce']) &&
        wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'lumnav_toggle_mm_nonce') &&
        current_user_can('manage_options')
    ) {
        $status = get_option(LUMNAV_MM_OPTION, 'off');
        $new_status = ($status === 'on') ? 'off' : 'on';
        update_option(LUMNAV_MM_OPTION, $new_status);

        wp_safe_redirect(remove_query_arg(array('action', '_wpnonce'), wp_get_referer() ?: admin_url()));
        exit;
    }
}
add_action('admin_init', 'lumnav_mm_toggle_status');

/**
 * Adds a settings page for the plugin.
 */
function lumnav_mm_add_settings_page()
{
    add_options_page(
        'Landing Page Settings',
        'Landing Page',
        'manage_options',
        'lumnav-maintenance-mode',
        'lumnav_mm_render_settings_page'
    );
}
add_action('admin_menu', 'lumnav_mm_add_settings_page');

/**
 * Registers the plugin's settings.
 */
function lumnav_mm_register_settings()
{
    register_setting('lumnav_mm_settings_group', LUMNAV_MM_STYLE_OPTION);
}
add_action('admin_init', 'lumnav_mm_register_settings');

/**
 * Renders the settings page HTML with style previews.
 */
function lumnav_mm_render_settings_page()
{
    $current_style = get_option(LUMNAV_MM_STYLE_OPTION, 'simple');
    ?>
    <div class="wrap">
        <h1>Landing Page Settings</h1>
        <p>Select the style for your landing page. Visitors will see this page when the landing page mode is ON.</p>
        <form method="post" action="options.php">
            <?php settings_fields('lumnav_mm_settings_group'); ?>
            <div class="lumnav-mm-style-grid">

                <!-- Simple -->
                <label>
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="simple" <?php checked($current_style, 'simple'); ?>>
                    <div class="preview-container">
                        <div class="preview-iframe-wrapper">
                            <iframe src="<?php echo home_url('?lumnav_mm_preview=true&style=simple'); ?>"
                                scrolling="no"></iframe>
                        </div>
                        <div class="card-title">Simple</div>
                    </div>
                </label>

                <!-- High-Tech -->
                <label>
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="hightech" <?php checked($current_style, 'hightech'); ?>>
                    <div class="preview-container">
                        <div class="preview-iframe-wrapper">
                            <iframe src="<?php echo home_url('?lumnav_mm_preview=true&style=hightech'); ?>"
                                scrolling="no"></iframe>
                        </div>
                        <div class="card-title">High-Tech</div>
                    </div>
                </label>

                <!-- Artistic -->
                <label>
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="artistic" <?php checked($current_style, 'artistic'); ?>>
                    <div class="preview-container">
                        <div class="preview-iframe-wrapper">
                            <iframe src="<?php echo home_url('?lumnav_mm_preview=true&style=artistic'); ?>"
                                scrolling="no"></iframe>
                        </div>
                        <div class="card-title">Artistic</div>
                    </div>
                </label>

                <!-- Glassmorphic -->
                <label>
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="glassmorphic" <?php checked($current_style, 'glassmorphic'); ?>>
                    <div class="preview-container">
                        <div class="preview-iframe-wrapper">
                            <iframe src="<?php echo home_url('?lumnav_mm_preview=true&style=glassmorphic'); ?>"
                                scrolling="no"></iframe>
                        </div>
                        <div class="card-title">Glassmorphic</div>
                    </div>
                </label>

                <!-- Neural Network -->
                <label>
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="neural" <?php checked($current_style, 'neural'); ?>>
                    <div class="preview-container">
                        <div class="preview-iframe-wrapper">
                            <iframe src="<?php echo home_url('?lumnav_mm_preview=true&style=neural'); ?>"
                                scrolling="no"></iframe>
                        </div>
                        <div class="card-title">Neural Network</div>
                    </div>
                </label>

                <!-- Liquid Morphing -->
                <label>
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="liquid" <?php checked($current_style, 'liquid'); ?>>
                    <div class="preview-container">
                        <div class="preview-iframe-wrapper">
                            <iframe src="<?php echo home_url('?lumnav_mm_preview=true&style=liquid'); ?>"
                                scrolling="no"></iframe>
                        </div>
                        <div class="card-title">Liquid Morphing</div>
                    </div>
                </label>

                <!-- 3D Torus -->
                <label>
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="torus" <?php checked($current_style, 'torus'); ?>>
                    <div class="preview-container">
                        <div class="preview-iframe-wrapper">
                            <iframe src="<?php echo home_url('?lumnav_mm_preview=true&style=torus'); ?>"
                                scrolling="no"></iframe>
                        </div>
                        <div class="card-title">3D Torus</div>
                    </div>
                </label>

                <!-- Kinetic Typography -->
                <label>
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="kinetic" <?php checked($current_style, 'kinetic'); ?>>
                    <div class="preview-container">
                        <div class="preview-iframe-wrapper">
                            <iframe src="<?php echo home_url('?lumnav_mm_preview=true&style=kinetic'); ?>"
                                scrolling="no"></iframe>
                        </div>
                        <div class="card-title">Kinetic Type</div>
                    </div>
                </label>

                <!-- Aurora Borealis -->
                <label>
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="aurora" <?php checked($current_style, 'aurora'); ?>>
                    <div class="preview-container">
                        <div class="preview-iframe-wrapper">
                            <iframe src="<?php echo home_url('?lumnav_mm_preview=true&style=aurora'); ?>"
                                scrolling="no"></iframe>
                        </div>
                        <div class="card-title">Aurora Borealis</div>
                    </div>
                </label>

                <!-- Holographic -->
                <label>
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="holographic" <?php checked($current_style, 'holographic'); ?>>
                    <div class="preview-container">
                        <div class="preview-iframe-wrapper">
                            <iframe src="<?php echo home_url('?lumnav_mm_preview=true&style=holographic'); ?>"
                                scrolling="no"></iframe>
                        </div>
                        <div class="card-title">Holographic</div>
                    </div>
                </label>

                <!-- Neon Cyberpunk -->
                <label>
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="neon" <?php checked($current_style, 'neon'); ?>>
                    <div class="preview-container">
                        <div class="preview-iframe-wrapper">
                            <iframe src="<?php echo home_url('?lumnav_mm_preview=true&style=neon'); ?>"
                                scrolling="no"></iframe>
                        </div>
                        <div class="card-title">Neon Cyberpunk</div>
                    </div>
                </label>

                <!-- Minimalist Swiss -->
                <label>
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="swiss" <?php checked($current_style, 'swiss'); ?>>
                    <div class="preview-container">
                        <div class="preview-iframe-wrapper">
                            <iframe src="<?php echo home_url('?lumnav_mm_preview=true&style=swiss'); ?>"
                                scrolling="no"></iframe>
                        </div>
                        <div class="card-title">Minimalist Swiss</div>
                    </div>
                </label>

                <!-- Retro VHS -->
                <label>
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="vhs" <?php checked($current_style, 'vhs'); ?>>
                    <div class="preview-container">
                        <div class="preview-iframe-wrapper">
                            <iframe src="<?php echo home_url('?lumnav_mm_preview=true&style=vhs'); ?>"
                                scrolling="no"></iframe>
                        </div>
                        <div class="card-title">Retro VHS</div>
                    </div>
                </label>

                <!-- Brutalist Design -->
                <label>
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="brutalist" <?php checked($current_style, 'brutalist'); ?>>
                    <div class="preview-container">
                        <div class="preview-iframe-wrapper">
                            <iframe src="<?php echo home_url('?lumnav_mm_preview=true&style=brutalist'); ?>"
                                scrolling="no"></iframe>
                        </div>
                        <div class="card-title">Brutalist Design</div>
                    </div>
                </label>

                <!-- Cosmic Galaxy -->
                <label>
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="galaxy" <?php checked($current_style, 'galaxy'); ?>>
                    <div class="preview-container">
                        <div class="preview-iframe-wrapper">
                            <iframe src="<?php echo home_url('?lumnav_mm_preview=true&style=galaxy'); ?>"
                                scrolling="no"></iframe>
                        </div>
                        <div class="card-title">Cosmic Galaxy</div>
                    </div>
                </label>

                <!-- Matrix Code Rain -->
                <label>
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="matrix" <?php checked($current_style, 'matrix'); ?>>
                    <div class="preview-container">
                        <div class="preview-iframe-wrapper">
                            <iframe src="<?php echo home_url('?lumnav_mm_preview=true&style=matrix'); ?>"
                                scrolling="no"></iframe>
                        </div>
                        <div class="card-title">Matrix Code</div>
                    </div>
                </label>

                <!-- Vaporwave -->
                <label>
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="vaporwave" <?php checked($current_style, 'vaporwave'); ?>>
                    <div class="preview-container">
                        <div class="preview-iframe-wrapper">
                            <iframe src="<?php echo home_url('?lumnav_mm_preview=true&style=vaporwave'); ?>"
                                scrolling="no"></iframe>
                        </div>
                        <div class="card-title">Vaporwave</div>
                    </div>
                </label>

                <!-- Particle Explosion -->
                <label>
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="particles" <?php checked($current_style, 'particles'); ?>>
                    <div class="preview-container">
                        <div class="preview-iframe-wrapper">
                            <iframe src="<?php echo home_url('?lumnav_mm_preview=true&style=particles'); ?>"
                                scrolling="no"></iframe>
                        </div>
                        <div class="card-title">Particle Explosion</div>
                    </div>
                </label>

                <!-- Geometric Patterns -->
                <label>
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="geometric" <?php checked($current_style, 'geometric'); ?>>
                    <div class="preview-container">
                        <div class="preview-iframe-wrapper">
                            <iframe src="<?php echo home_url('?lumnav_mm_preview=true&style=geometric'); ?>"
                                scrolling="no"></iframe>
                        </div>
                        <div class="card-title">Geometric Patterns</div>
                    </div>
                </label>

                <!-- Glitch Art -->
                <label>
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="glitch" <?php checked($current_style, 'glitch'); ?>>
                    <div class="preview-container">
                        <div class="preview-iframe-wrapper">
                            <iframe src="<?php echo home_url('?lumnav_mm_preview=true&style=glitch'); ?>"
                                scrolling="no"></iframe>
                        </div>
                        <div class="card-title">Glitch Art</div>
                    </div>
                </label>

            </div>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Checks if maintenance mode is active and displays the maintenance page.
 */
function lumnav_mm_check_status()
{
    // Check for preview request
    if (isset($_GET['lumnav_mm_preview']) && $_GET['lumnav_mm_preview'] === 'true' && current_user_can('manage_options')) {
        $style = isset($_GET['style']) ? sanitize_key($_GET['style']) : 'simple';
        // Proceed to render logic below with the requested style
    } else {
        // Normal maintenance mode check
        if (!get_option(LUMNAV_MM_OPTION) || current_user_can('manage_options') || in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'), true)) {
            return;
        }
        $style = get_option(LUMNAV_MM_STYLE_OPTION, 'simple');

        header('HTTP/1.1 503 Service Temporarily Unavailable');
        header('Content-Type: text/html; charset=utf-8');
        header('Retry-After: 3600');
    }

    if ($style === 'hightech') {
        lumnav_mm_render_hightech_page();
    } elseif ($style === 'artistic') {
        lumnav_mm_render_artistic_page();
    } elseif ($style === 'glassmorphic') {
        lumnav_mm_render_glassmorphic_page();
    } elseif ($style === 'neural') {
        lumnav_mm_render_neural_page();
    } elseif ($style === 'liquid') {
        lumnav_mm_render_liquid_page();
    } elseif ($style === 'torus') {
        lumnav_mm_render_torus_page();
    } elseif ($style === 'kinetic') {
        lumnav_mm_render_kinetic_page();
    } elseif ($style === 'aurora') {
        lumnav_mm_render_aurora_page();
    } elseif ($style === 'holographic') {
        lumnav_mm_render_holographic_page();
    } elseif ($style === 'neon') {
        lumnav_mm_render_neon_page();
    } elseif ($style === 'swiss') {
        lumnav_mm_render_swiss_page();
    } elseif ($style === 'vhs') {
        lumnav_mm_render_vhs_page();
    } elseif ($style === 'brutalist') {
        lumnav_mm_render_brutalist_page();
    } elseif ($style === 'galaxy') {
        lumnav_mm_render_galaxy_page();
    } elseif ($style === 'matrix') {
        lumnav_mm_render_matrix_page();
    } elseif ($style === 'vaporwave') {
        lumnav_mm_render_vaporwave_page();
    } elseif ($style === 'particles') {
        lumnav_mm_render_particles_page();
    } elseif ($style === 'geometric') {
        lumnav_mm_render_geometric_page();
    } elseif ($style === 'glitch') {
        lumnav_mm_render_glitch_page();
    } else {
        lumnav_mm_render_simple_page();
    }

    exit;
}
add_action('template_redirect', 'lumnav_mm_check_status');

/**
 * Renders the simple maintenance page.
 */
function lumnav_mm_render_simple_page()
{
    $domain = preg_replace('/^www\./', '', wp_parse_url(home_url(), PHP_URL_HOST));
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($domain); ?> - Under Maintenance</title>
        <meta name="description"
            content="<?php echo esc_attr($domain); ?> is currently undergoing scheduled maintenance. We will be back shortly.">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                color: #222;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                padding: 20px
            }

            .container {
                text-align: center;
                max-width: 900px;
                width: 100%;
                background: rgba(255, 255, 255, 0.6);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                padding: 60px 40px;
                border-radius: 30px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.8);
                animation: fadeIn 1s ease-out
            }

            .domain {
                font-size: clamp(2.5rem, 8vw, 5.5rem);
                font-weight: 800;
                color: #111;
                margin-bottom: 30px;
                letter-spacing: -0.03em;
                line-height: 1.1;
                animation: fadeIn 0.8s ease-out
            }

            .tagline {
                font-size: clamp(1.1rem, 2.5vw, 1.5rem);
                color: #555;
                font-weight: 500;
                margin-bottom: 15px;
                letter-spacing: -0.01em;
                animation: fadeIn 1s ease-out 0.2s both
            }

            .message {
                font-size: clamp(0.95rem, 2vw, 1.1rem);
                color: #777;
                max-width: 500px;
                margin: 0 auto;
                line-height: 1.7;
                animation: fadeIn 1s ease-out 0.4s both
            }

            .divider {
                width: 0;
                height: 3px;
                background: linear-gradient(90deg, transparent, #222, transparent);
                margin: 30px auto;
                opacity: 0.2;
                animation: expandWidth 1.2s ease-out 0.3s both
            }

            @keyframes expandWidth {
                0% {
                    width: 0;
                }

                100% {
                    width: 100px;
                }
            }

            @keyframes fadeIn {
                0% {
                    opacity: 0;
                    transform: translateY(10px)
                }

                100% {
                    opacity: 1;
                    transform: translateY(0)
                }
            }

            @media (max-width:600px) {
                .container {
                    padding: 40px 20px;
                }

                .domain {
                    margin-bottom: 20px
                }

                .tagline {
                    margin-bottom: 12px
                }
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div class="domain"><?php echo esc_html($domain); ?></div>
            <div class="divider"></div>
            <div class="tagline">Coming Soon</div>
            <div class="message">This domain is currently under construction. Check back soon!</div>
        </div>
    </body>

    </html>
    <?php
}

/**
 * Renders the high-tech maintenance page.
 */
function lumnav_mm_render_hightech_page()
{
    $domain = preg_replace('/^www\./', '', wp_parse_url(home_url(), PHP_URL_HOST));
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($domain); ?> - System Initializing</title>
        <meta name="description"
            content="<?php echo esc_attr($domain); ?> system initializing. Establishing secure connection...">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box
            }

            body {
                background: #0a0a0a;
                color: #00ff00;
                font-family: 'Share Tech Mono', monospace;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                overflow: hidden;
                text-align: center;
                text-shadow: 0 0 5px #00ff00, 0 0 10px #00ff00;
                position: relative
            }

            .scanline {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(to bottom, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0) 50%, rgba(0, 0, 0, 0.2) 50%, rgba(0, 0, 0, 0.2));
                background-size: 100% 4px;
                animation: scan 10s linear infinite;
                pointer-events: none;
                z-index: 10;
                opacity: 0.3;
            }

            @keyframes scan {
                0% {
                    background-position: 0 0;
                }

                100% {
                    background-position: 0 100%;
                }
            }

            .container {
                position: relative;
                z-index: 2;
                padding: 40px;
                max-width: 95%;
                border: 2px solid #00ff00;
                box-shadow: 0 0 20px rgba(0, 255, 0, 0.2), inset 0 0 40px rgba(0, 255, 0, 0.1);
                background: rgba(0, 10, 0, 0.8);
                backdrop-filter: blur(5px);
                animation: flicker 4s infinite alternate
            }

            .border-art {
                position: absolute;
                top: -12px;
                left: 30px;
                background: #0a0a0a;
                padding: 0 10px;
                font-size: 14px;
                color: #00ff00;
                letter-spacing: 2px
            }

            @keyframes flicker {

                0%,
                100% {
                    opacity: 1;
                    box-shadow: 0 0 20px rgba(0, 255, 0, 0.2), inset 0 0 40px rgba(0, 255, 0, 0.1)
                }

                50% {
                    opacity: .95;
                    box-shadow: 0 0 15px rgba(0, 255, 0, 0.1), inset 0 0 20px rgba(0, 255, 0, 0.05)
                }
            }

            .domain {
                font-size: clamp(2rem, 10vw, 6rem);
                font-weight: 700;
                margin-bottom: 20px;
                text-transform: uppercase;
                letter-spacing: 4px;
                animation: glitch 6s linear infinite, textGlow 2s ease-in-out infinite;
                line-height: 1.1;
                word-break: break-all
            }

            @keyframes glitch {

                2%,
                64% {
                    transform: translate(3px, 0) skew(0)
                }

                4%,
                60% {
                    transform: translate(-3px, 0) skew(0)
                }

                62% {
                    transform: translate(0, 0) skew(5deg)
                }

                65% {
                    transform: translate(0, 0) skew(0)
                }
            }

            @keyframes textGlow {

                0%,
                100% {
                    text-shadow: 0 0 5px #00ff00, 0 0 10px #00ff00
                }

                50% {
                    text-shadow: 0 0 10px #00ff00, 0 0 20px #00ff00, 0 0 30px #00ff00
                }
            }

            .status {
                font-size: clamp(1rem, 3vw, 1.5rem);
                margin-top: 25px;
                letter-spacing: 4px;
                text-transform: uppercase;
                animation: blink 2s step-end infinite;
                text-shadow: 0 0 8px rgba(0, 255, 0, 0.5);
            }

            .prompt {
                display: inline-block;
                margin-right: 8px
            }

            @keyframes blink {

                0%,
                50% {
                    opacity: 1
                }

                51%,
                100% {
                    opacity: .3
                }
            }

            .footer {
                font-size: clamp(0.85rem, 2vw, 1.1rem);
                margin-top: 20px;
                opacity: .7;
                letter-spacing: 2px;
                border-top: 1px solid rgba(0, 255, 0, 0.3);
                padding-top: 15px;
                display: inline-block;
            }

            .typing-effect {
                overflow: hidden;
                border-right: 2px solid #00ff00;
                white-space: nowrap;
                margin: 0 auto;
                animation: typing 3.5s steps(40, end), blink-caret .75s step-end infinite;
            }

            @keyframes typing {
                from {
                    width: 0
                }

                to {
                    width: 100%
                }
            }

            @keyframes blink-caret {

                from,
                to {
                    border-color: transparent
                }

                50% {
                    border-color: #00ff00;
                }
            }

            @media(max-width:768px) {
                .domain {
                    letter-spacing: 2px
                }

                .status {
                    letter-spacing: 2px
                }
            }
        </style>
    </head>

    <body>
        <div class="scanline"></div>
        <div class="container">
            <div class="border-art">[ SYSTEM ]</div>
            <div class="domain"><?php echo esc_html($domain); ?></div>
            <div class="status"><span class="prompt">&gt;</span>SYSTEM INITIALIZING</div>
            <div class="footer">
                <div class="typing-effect">Establishing secure connection...</div>
            </div>
        </div>
    </body>

    </html>
    <?php
}

/**
 * Renders the artistic maintenance page with a 3D background.
 */
function lumnav_mm_render_artistic_page()
{
    $domain = preg_replace('/^www\./', '', wp_parse_url(home_url(), PHP_URL_HOST));
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($domain); ?> - Coming Soon</title>
        <meta name="description"
            content="<?php echo esc_attr($domain); ?> is coming soon. Something beautiful is on the horizon.">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@900&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box
            }

            body {
                background: #000;
                color: #fff;
                font-family: 'Poppins', sans-serif;
                margin: 0;
                overflow: hidden;
                text-align: center
            }

            #bg-canvas {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 1
            }

            .content-wrapper {
                position: relative;
                z-index: 2;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                height: 100vh;
                padding: 20px
            }

            .grain {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                z-index: 3;
                opacity: 0.05;
                background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
            }

            .container {
                perspective: 1200px
            }

            .domain-text {
                font-size: clamp(3rem, 10vw, 8rem);
                font-weight: 900;
                text-transform: lowercase;
                letter-spacing: -2px;
                background: linear-gradient(135deg, #ff8a00, #e52e71, #9d4edd, #4cc9f0);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                background-size: 300% 300%;
                animation: gradientShift 8s ease infinite, breathe 4s ease-in-out infinite, rotate3d 20s linear infinite;
                line-height: 1;
                margin-bottom: 30px;
                filter: drop-shadow(0 0 20px rgba(255, 138, 0, 0.6)) drop-shadow(0 0 40px rgba(157, 78, 221, 0.4));
                transform-style: preserve-3d
            }

            @keyframes gradientShift {
                0% {
                    background-position: 0% 50%
                }

                50% {
                    background-position: 100% 50%
                }

                100% {
                    background-position: 0% 50%
                }
            }

            @keyframes breathe {

                0%,
                100% {
                    transform: scale(1) rotateY(0deg)
                }

                50% {
                    transform: scale(1.05) rotateY(5deg)
                }
            }

            @keyframes rotate3d {
                0% {
                    transform: rotateY(-2deg) rotateX(2deg)
                }

                50% {
                    transform: rotateY(2deg) rotateX(-2deg)
                }

                100% {
                    transform: rotateY(-2deg) rotateX(2deg)
                }
            }

            p {
                font-size: clamp(1rem, 2.5vw, 1.3rem);
                color: rgba(255, 255, 255, 0.8);
                font-family: sans-serif;
                font-weight: 300;
                max-width: 600px;
                animation: fadeIn 1.5s ease-out 0.5s both;
                text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            }

            @keyframes fadeIn {
                0% {
                    opacity: 0;
                    transform: translateY(20px)
                }

                100% {
                    opacity: 1;
                    transform: translateY(0)
                }
            }

            .glow-ring {
                position: absolute;
                width: 120%;
                height: 120%;
                border-radius: 50%;
                background: radial-gradient(circle, transparent 30%, rgba(157, 78, 221, 0.1) 50%, transparent 70%);
                animation: pulse 3s ease-in-out infinite;
                pointer-events: none
            }

            @keyframes pulse {

                0%,
                100% {
                    transform: scale(0.95);
                    opacity: 0.4
                }

                50% {
                    transform: scale(1.05);
                    opacity: 0.8
                }
            }
        </style>
    </head>

    <body><canvas id="bg-canvas"></canvas>
        <div class="grain"></div>
        <div class="content-wrapper">
            <div class="glow-ring"></div>
            <div class="container">
                <div class="domain-text"><?php echo esc_html($domain); ?></div>
            </div>
            <p>Something beautiful is on the horizon.</p>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
        <script>let scene, camera, renderer, stars, starGeo; let mouseX = 0, mouseY = 0; function init() { scene = new THREE.Scene(); camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 1, 1000); camera.position.z = 1; camera.rotation.x = Math.PI / 2; renderer = new THREE.WebGLRenderer({ canvas: document.getElementById("bg-canvas"), alpha: true }); renderer.setSize(window.innerWidth, window.innerHeight); starGeo = new THREE.BufferGeometry(); const starVertices = []; for (let i = 0; i < 8000; i++) { const x = (Math.random() - .5) * 2000; const y = (Math.random() - .5) * 2000; const z = Math.random() * 2000 - 1000; starVertices.push(x, y, z) } starGeo.setAttribute('position', new THREE.Float32BufferAttribute(starVertices, 3)); let sprite = new THREE.TextureLoader().load('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6gAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAB9SURBVDjLY/j//z8DJRgxQAFjRgwYgKWBAQsgKWMlBwYGAwY2MMZkYGBgYGBgYGBgYGBgYGBg/P//PwYGBgY2ENAwbGDgAIIxQzQyMDAwMNz//x9KjPz//z8DGNQvA0M9+P//P5zj//9/GM4wYgACDAAfoQ/x/5k/XwAAAABJRU5ErkJggg=='); let starMaterial = new THREE.PointsMaterial({ color: 16777215, size: 1, map: sprite, transparent: true }); stars = new THREE.Points(starGeo, starMaterial); scene.add(stars); window.addEventListener("resize", onWindowResize, !1); document.addEventListener('mousemove', onMouseMove, !1); animate() } function onWindowResize() { camera.aspect = window.innerWidth / window.innerHeight; camera.updateProjectionMatrix(); renderer.setSize(window.innerWidth, window.innerHeight) } function onMouseMove(event) { mouseX = event.clientX - window.innerWidth / 2; mouseY = event.clientY - window.innerHeight / 2 } function animate() { starGeo.attributes.position.needsUpdate = !0; stars.rotation.y += 8e-5; stars.rotation.x += 3e-5; if (mouseX !== 0 || mouseY !== 0) { camera.position.x += (-mouseX - camera.position.x) * .00008; camera.position.y += (mouseY - camera.position.y) * .00008; camera.lookAt(scene.position) } renderer.render(scene, camera); requestAnimationFrame(animate) } init();</script>
    </body>

    </html>
    <?php
}

/**
 * Renders the glassmorphic gradient mesh maintenance page.
 */
function lumnav_mm_render_glassmorphic_page()
{
    $domain = preg_replace('/^www\./', '', wp_parse_url(home_url(), PHP_URL_HOST));
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($domain); ?> - Coming Soon</title>
        <meta name="description" content="Future home of <?php echo esc_attr($domain); ?>. We're building something great.">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box
            }

            body {
                font-family: 'Inter', sans-serif;
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                overflow: hidden;
                background: #0f0f23;
                position: relative
            }

            .gradient-bg {
                position: fixed;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: linear-gradient(45deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
                background-size: 400% 400%;
                animation: gradientShift 15s ease infinite;
                filter: blur(80px);
                opacity: .6;
                z-index: 1
            }

            @keyframes gradientShift {
                0% {
                    background-position: 0% 50%
                }

                50% {
                    background-position: 100% 50%
                }

                100% {
                    background-position: 0% 50%
                }
            }

            .particles {
                position: fixed;
                width: 100%;
                height: 100%;
                z-index: 2;
                pointer-events: none
            }

            .particle {
                position: absolute;
                width: 4px;
                height: 4px;
                background: rgba(255, 255, 255, .6);
                border-radius: 50%;
                animation: float 20s infinite ease-in-out
            }

            .particle:nth-child(1) {
                left: 10%;
                top: 20%;
                animation-delay: 0s;
                animation-duration: 18s
            }

            .particle:nth-child(2) {
                left: 80%;
                top: 40%;
                animation-delay: 2s;
                animation-duration: 22s
            }

            .particle:nth-child(3) {
                left: 30%;
                top: 60%;
                animation-delay: 4s;
                animation-duration: 20s
            }

            .particle:nth-child(4) {
                left: 70%;
                top: 80%;
                animation-delay: 1s;
                animation-duration: 19s
            }

            .particle:nth-child(5) {
                left: 50%;
                top: 30%;
                animation-delay: 3s;
                animation-duration: 21s
            }

            .particle:nth-child(6) {
                left: 20%;
                top: 70%;
                animation-delay: 5s;
                animation-duration: 17s
            }

            @keyframes float {

                0%,
                100% {
                    transform: translateY(0) translateX(0)
                }

                25% {
                    transform: translateY(-30px) translateX(20px)
                }

                50% {
                    transform: translateY(-60px) translateX(-20px)
                }

                75% {
                    transform: translateY(-30px) translateX(10px)
                }
            }

            .card {
                position: relative;
                z-index: 3;
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(25px);
                -webkit-backdrop-filter: blur(25px);
                border: 1px solid rgba(255, 255, 255, 0.15);
                border-radius: 30px;
                padding: 60px 80px;
                max-width: 600px;
                width: 90%;
                box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.1);
                animation: cardEntrance 1s ease-out
            }

            @keyframes cardEntrance {
                0% {
                    opacity: 0;
                    transform: translateY(30px) scale(.95)
                }

                100% {
                    opacity: 1;
                    transform: translateY(0) scale(1)
                }
            }

            .domain {
                font-size: clamp(1rem, 2vw, 1.2rem);
                font-weight: 600;
                color: rgba(255, 255, 255, .9);
                margin-bottom: 15px;
                text-transform: lowercase;
                letter-spacing: 1px;
                background: linear-gradient(90deg, #a8edea, #fed6e3);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                animation: fadeInUp .8s ease-out .1s both
            }

            h1 {
                font-size: clamp(2rem, 5vw, 3.5rem);
                font-weight: 700;
                color: #fff;
                margin-bottom: 20px;
                text-shadow: 0 2px 20px rgba(0, 0, 0, .3);
                line-height: 1.2;
                animation: fadeInUp .8s ease-out .2s both
            }

            @keyframes fadeInUp {
                0% {
                    opacity: 0;
                    transform: translateY(20px)
                }

                100% {
                    opacity: 1;
                    transform: translateY(0)
                }
            }

            p {
                font-size: 1.1rem;
                color: rgba(255, 255, 255, .85);
                line-height: 1.6;
                font-weight: 300;
                animation: fadeInUp .8s ease-out .4s both
            }

            .maintenance-icon {
                width: 60px;
                height: 60px;
                margin-bottom: 20px;
                animation: pulse 2s ease-in-out infinite
            }

            .maintenance-icon svg {
                width: 100%;
                height: 100%;
                filter: drop-shadow(0 4px 8px rgba(0, 0, 0, .2))
            }

            @keyframes pulse {

                0%,
                100% {
                    transform: scale(1)
                }

                50% {
                    transform: scale(1.05)
                }
            }

            @media(max-width:768px) {
                .card {
                    padding: 40px 30px
                }

                h1 {
                    font-size: 2rem
                }

                p {
                    font-size: 1rem
                }
            }
        </style>
    </head>

    <body>
        <div class="gradient-bg"></div>
        <div class="particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>
        <div class="card">
            <div class="maintenance-icon"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="white" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" opacity="0.9" />
                    <path d="M2 17L12 22L22 17" stroke="white" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" opacity="0.7" />
                    <path d="M2 12L12 17L22 12" stroke="white" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" opacity="0.8" />
                </svg></div>
            <div class="domain"><?php echo esc_html($domain); ?></div>
            <h1>Future Home</h1>
            <p>We're working hard to build something amazing. Check back soon!</p>
        </div>
    </body>

    </html>
    <?php
}

/**
 * Renders the neural network particles maintenance page.
 */
function lumnav_mm_render_neural_page()
{
    $domain = preg_replace('/^www\./', '', wp_parse_url(home_url(), PHP_URL_HOST));
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($domain); ?> - System Initializing</title>
        <meta name="description" content="<?php echo esc_attr($domain); ?> initializing neural network.">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@300;500;700&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box
            }

            body {
                font-family: 'Rajdhani', sans-serif;
                background: #0a0e27;
                color: #fff;
                overflow: hidden;
                height: 100vh
            }

            #canvas {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 1
            }

            .content {
                position: relative;
                z-index: 2;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                height: 100vh;
                text-align: center;
                padding: 20px
            }

            h1 {
                font-size: clamp(2.5rem, 6vw, 4rem);
                font-weight: 700;
                margin-bottom: 20px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                animation: fadeIn 1s ease-out
            }

            p {
                font-size: clamp(1rem, 2vw, 1.3rem);
                color: rgba(255, 255, 255, .7);
                max-width: 600px;
                font-weight: 300;
                letter-spacing: 1px;
                animation: fadeIn 1s ease-out .3s both
            }

            @keyframes fadeIn {
                0% {
                    opacity: 0;
                    transform: translateY(20px)
                }

                100% {
                    opacity: 1;
                    transform: translateY(0)
                }
            }

            .status-indicator {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                margin-top: 30px;
                padding: 12px 24px;
                background: rgba(102, 126, 234, .1);
                border: 1px solid rgba(102, 126, 234, .3);
                border-radius: 50px;
                font-size: 14px;
                font-weight: 500;
                letter-spacing: 1.5px;
                text-transform: uppercase;
                animation: fadeIn 1s ease-out .6s both
            }

            .status-dot {
                width: 8px;
                height: 8px;
                background: #667eea;
                border-radius: 50%;
                animation: pulse 2s ease-in-out infinite
            }

            @keyframes pulse {

                0%,
                100% {
                    opacity: 1;
                    transform: scale(1)
                }

                50% {
                    opacity: .5;
                    transform: scale(1.2)
                }
            }
        </style>
    </head>

    <body><canvas id="canvas"></canvas>
        <div class="content">
            <div
                style="font-size:clamp(1rem, 3vw, 1.4rem);font-weight:600;margin-bottom:10px;color:rgba(102,126,234,0.8);text-transform:lowercase;letter-spacing:2px;">
                <?php echo esc_html($domain); ?>
            </div>
            <h1>Initializing Neural Network</h1>
            <p>Our systems are currently establishing neural pathways. Launch imminent.</p>
            <div class="status-indicator"><span class="status-dot"></span>Initializing</div>
        </div>
        <script>const canvas = document.getElementById('canvas'); const ctx = canvas.getContext('2d'); let w, h, particles = [], mouse = { x: null, y: null, radius: 150 }; function resize() { w = canvas.width = window.innerWidth; h = canvas.height = window.innerHeight; init() } function init() { particles = []; const count = Math.min(100, Math.floor((w * h) / 15000)); for (let i = 0; i < count; i++) { particles.push({ x: Math.random() * w, y: Math.random() * h, vx: (Math.random() - .5) * .5, vy: (Math.random() - .5) * .5, radius: Math.random() * 2 + 1 }) } } class Particle { constructor(obj) { this.x = obj.x; this.y = obj.y; this.vx = obj.vx; this.vy = obj.vy; this.radius = obj.radius } update() { this.x += this.vx; this.y += this.vy; if (this.x < 0 || this.x > w) this.vx *= -1; if (this.y < 0 || this.y > h) this.vy *= -1; if (mouse.x !== null) { const dx = this.x - mouse.x; const dy = this.y - mouse.y; const dist = Math.sqrt(dx * dx + dy * dy); if (dist < mouse.radius) { const angle = Math.atan2(dy, dx); const force = ((mouse.radius - dist) / mouse.radius) * 2; this.vx += Math.cos(angle) * force * .1; this.vy += Math.sin(angle) * force * .1 } } this.vx *= .99; this.vy *= .99 } draw() { ctx.beginPath(); ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2); ctx.fillStyle = 'rgba(102,126,234,.8)'; ctx.fill() } } function connect() { for (let i = 0; i < particles.length; i++) { for (let j = i + 1; j < particles.length; j++) { const dx = particles[i].x - particles[j].x; const dy = particles[i].y - particles[j].y; const dist = Math.sqrt(dx * dx + dy * dy); if (dist < 120) { ctx.beginPath(); ctx.moveTo(particles[i].x, particles[i].y); ctx.lineTo(particles[j].x, particles[j].y); ctx.strokeStyle = `rgba(118,75,162,${1 - dist / 120})`; ctx.lineWidth = .5; ctx.stroke() } } } } function animate() { ctx.clearRect(0, 0, w, h); particles.forEach((p, i) => { const particle = new Particle(p); particle.update(); particle.draw(); particles[i] = particle }); connect(); requestAnimationFrame(animate) } window.addEventListener('resize', resize); canvas.addEventListener('mousemove', e => { mouse.x = e.clientX; mouse.y = e.clientY }); canvas.addEventListener('mouseleave', () => { mouse.x = null; mouse.y = null }); resize(); animate();</script>
    </body>

    </html>
    <?php
}

/**
 * Renders the liquid morphing blobs maintenance page.
 */
function lumnav_mm_render_liquid_page()
{
    $domain = preg_replace('/^www\./', '', wp_parse_url(home_url(), PHP_URL_HOST));
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($domain); ?> - Be Right Back</title>
        <meta name="description"
            content="<?php echo esc_attr($domain); ?> is transforming. Experience something extraordinary when we return.">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;600;800&display=swap"
            rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box
            }

            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                background: #0f0817;
                color: #fff;
                overflow: hidden;
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center
            }

            .blob-container {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 1
            }

            .blob {
                position: absolute;
                filter: blur(60px);
                opacity: .5;
                animation: morph 20s ease-in-out infinite
            }

            .blob1 {
                top: 10%;
                left: 10%;
                width: 400px;
                height: 400px;
                background: linear-gradient(45deg, #667eea, #764ba2);
                animation-delay: 0s
            }

            .blob2 {
                top: 50%;
                right: 10%;
                width: 500px;
                height: 500px;
                background: linear-gradient(135deg, #f093fb, #f5576c);
                animation-delay: 5s
            }

            .blob3 {
                bottom: 10%;
                left: 40%;
                width: 450px;
                height: 450px;
                background: linear-gradient(90deg, #4facfe, #00f2fe);
                animation-delay: 10s
            }

            @keyframes morph {

                0%,
                100% {
                    border-radius: 60% 40% 30% 70%/60% 30% 70% 40%;
                    transform: translate(0, 0) scale(1)
                }

                25% {
                    border-radius: 30% 60% 70% 40%/50% 60% 30% 60%;
                    transform: translate(50px, -50px) scale(1.1)
                }

                50% {
                    border-radius: 40% 60% 60% 40%/70% 30% 70% 30%;
                    transform: translate(-50px, 50px) scale(.9)
                }

                75% {
                    border-radius: 70% 30% 40% 60%/40% 70% 30% 60%;
                    transform: translate(30px, 30px) scale(1.05)
                }
            }

            .content {
                position: relative;
                z-index: 2;
                text-align: center;
                padding: 40px;
                max-width: 700px;
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border-radius: 30px;
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            h1 {
                font-size: clamp(2.5rem, 7vw, 5rem);
                font-weight: 800;
                margin-bottom: 20px;
                background: linear-gradient(135deg, #fff, #e0e0ff);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                animation: slideUp 1s ease-out
            }

            p {
                font-size: clamp(1.1rem, 2.5vw, 1.5rem);
                color: rgba(255, 255, 255, .8);
                font-weight: 300;
                line-height: 1.6;
                animation: slideUp 1s ease-out .2s both
            }

            @keyframes slideUp {
                0% {
                    opacity: 0;
                    transform: translateY(30px)
                }

                100% {
                    opacity: 1;
                    transform: translateY(0)
                }
            }

            .dots {
                display: inline-flex;
                gap: 8px;
                margin-top: 30px
            }

            .dot {
                width: 12px;
                height: 12px;
                background: #667eea;
                border-radius: 50%;
                animation: bounce 1.4s ease-in-out infinite
            }

            .dot:nth-child(2) {
                animation-delay: .2s
            }

            .dot:nth-child(3) {
                animation-delay: .4s
            }

            @keyframes bounce {

                0%,
                80%,
                100% {
                    transform: scale(0)
                }

                40% {
                    transform: scale(1)
                }
            }
        </style>
    </head>

    <body>
        <div class="blob-container">
            <div class="blob blob1"></div>
            <div class="blob blob2"></div>
            <div class="blob blob3"></div>
        </div>
        <div class="content">
            <div
                style="font-size:clamp(1rem, 3vw, 1.5rem);font-weight:700;margin-bottom:12px;background:linear-gradient(135deg,#667eea,#f093fb);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;text-transform:lowercase;letter-spacing:1.5px;">
                <?php echo esc_html($domain); ?>
            </div>
            <h1>Transforming</h1>
            <p>We're reshaping our digital presence. Experience something extraordinary when we return.</p>
            <div class="dots">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
        </div>
    </body>

    </html>
    <?php
}

/**
 * Renders the 3D geometric torus maintenance page.
 */
function lumnav_mm_render_torus_page()
{
    $domain = preg_replace('/^www\./', '', wp_parse_url(home_url(), PHP_URL_HOST));
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($domain); ?> - Maintenance in Progress</title>
        <meta name="description"
            content="<?php echo esc_attr($domain); ?> infrastructure is being enhanced. Please stand by.">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;500;700&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box
            }

            body {
                font-family: 'Space Grotesk', sans-serif;
                background: #000;
                color: #fff;
                overflow: hidden
            }

            #canvas-3d {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 1;
                pointer-events: none;
            }

            .overlay {
                position: relative;
                z-index: 2;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                height: 100vh;
                text-align: center;
                pointer-events: none;
                padding: 20px
            }

            h1 {
                font-size: clamp(2.5rem, 6vw, 4rem);
                font-weight: 700;
                margin-bottom: 15px;
                letter-spacing: 2px;
                text-transform: uppercase;
                background: linear-gradient(90deg, #a8edea, #fed6e3);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                animation: glow 3s ease-in-out infinite
            }

            @keyframes glow {

                0%,
                100% {
                    opacity: 1
                }

                50% {
                    opacity: .7
                }
            }

            p {
                font-size: clamp(1rem, 2vw, 1.2rem);
                color: rgba(255, 255, 255, .6);
                max-width: 500px;
                font-weight: 300
            }
        </style>
    </head>

    <body><canvas id="canvas-3d"></canvas>
        <div class="overlay">
            <div
                style="font-size:clamp(1rem, 3vw, 1.4rem);font-weight:500;margin-bottom:8px;color:rgba(168,237,234,0.9);text-transform:lowercase;letter-spacing:3px;">
                <?php echo esc_html($domain); ?>
            </div>
            <h1>Optimizing Systems</h1>
            <p>Our infrastructure is being enhanced. Please stand by.</p>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
        <script>let scene, camera, renderer, torus, mouseX = 0, mouseY = 0; function init() { scene = new THREE.Scene(); camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, .1, 1000); camera.position.z = 5; renderer = new THREE.WebGLRenderer({ canvas: document.getElementById('canvas-3d'), alpha: true, antialias: true }); renderer.setSize(window.innerWidth, window.innerHeight); renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2)); const geometry = new THREE.TorusGeometry(1.5, .6, 32, 100); const material = new THREE.MeshPhongMaterial({ color: 0x667eea, emissive: 0x442288, shininess: 100, wireframe: false }); torus = new THREE.Mesh(geometry, material); scene.add(torus); const wireGeometry = new THREE.TorusGeometry(1.52, .62, 32, 100); const wireMaterial = new THREE.MeshBasicMaterial({ color: 0xa8edea, wireframe: true, transparent: true, opacity: .3 }); const wireframe = new THREE.Mesh(wireGeometry, wireMaterial); scene.add(wireframe); const light1 = new THREE.PointLight(0x667eea, 2, 100); light1.position.set(5, 5, 5); scene.add(light1); const light2 = new THREE.PointLight(0xf093fb, 2, 100); light2.position.set(-5, -5, 5); scene.add(light2); const ambientLight = new THREE.AmbientLight(0x404040, 1); scene.add(ambientLight); const starGeo = new THREE.BufferGeometry(); const starVerts = []; for (let i = 0; i < 200; i++) { const x = (Math.random() - .5) * 100; const y = (Math.random() - .5) * 100; const z = (Math.random() - .5) * 100; starVerts.push(x, y, z) } starGeo.setAttribute('position', new THREE.Float32BufferAttribute(starVerts, 3)); const starMat = new THREE.PointsMaterial({ color: 0xffffff, size: .1 }); const stars = new THREE.Points(starGeo, starMat); scene.add(stars); window.addEventListener('resize', onResize); document.addEventListener('mousemove', onMouseMove); animate() } function onResize() { camera.aspect = window.innerWidth / window.innerHeight; camera.updateProjectionMatrix(); renderer.setSize(window.innerWidth, window.innerHeight) } function onMouseMove(e) { mouseX = (e.clientX / window.innerWidth) * 2 - 1; mouseY = -(e.clientY / window.innerHeight) * 2 + 1 } function animate() { requestAnimationFrame(animate); torus.rotation.x += .005; torus.rotation.y += .005; camera.position.x += (mouseX * .5 - camera.position.x) * .05; camera.position.y += (mouseY * .5 - camera.position.y) * .05; camera.lookAt(scene.position); renderer.render(scene, camera) } init();</script>
    </body>

    </html>
    <?php
}

/**
 * Renders the kinetic typography maintenance page.
 */
function lumnav_mm_render_kinetic_page()
{
    $domain = preg_replace('/^www\./', '', wp_parse_url(home_url(), PHP_URL_HOST));
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($domain); ?> - Coming Back Soon</title>
        <meta name="description"
            content="<?php echo esc_attr($domain); ?> is upgrading. Pushing boundaries, elevating experiences.">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Roboto:wght@300&display=swap"
            rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box
            }

            body {
                font-family: 'Roboto', sans-serif;
                background: #1a1a1a;
                color: #fff;
                overflow: hidden;
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                position: relative
            }

            .bg-texture {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255, 255, 255, .02) 2px, rgba(255, 255, 255, .02) 4px);
                z-index: 1;
                pointer-events: none
            }

            .content {
                position: relative;
                z-index: 2;
                text-align: center;
                padding: 20px
            }

            .title {
                font-family: 'Bebas Neue', cursive;
                font-size: clamp(4rem, 12vw, 10rem);
                line-height: .9;
                margin-bottom: 30px;
                overflow: hidden;
                word-break: break-word;
            }

            .title .word {
                display: inline-block;
                position: relative
            }

            .title .letter {
                display: inline-block;
                animation: revealLetter 1s cubic-bezier(.77, 0, .175, 1) both;
                position: relative
            }

            .title .letter:nth-child(1) {
                animation-delay: .1s
            }

            .title .letter:nth-child(2) {
                animation-delay: .2s
            }

            .title .letter:nth-child(3) {
                animation-delay: .3s
            }

            .title .letter:nth-child(4) {
                animation-delay: .4s
            }

            .title .letter:nth-child(5) {
                animation-delay: .5s
            }

            .title .letter:nth-child(6) {
                animation-delay: .6s
            }

            .title .letter:nth-child(7) {
                animation-delay: .7s
            }

            .title .letter:nth-child(8) {
                animation-delay: .8s
            }

            .title .letter:nth-child(9) {
                animation-delay: .9s
            }

            .title .letter:nth-child(10) {
                animation-delay: 1s
            }

            @keyframes revealLetter {
                0% {
                    transform: translateY(100%) rotateX(-90deg);
                    opacity: 0
                }

                100% {
                    transform: translateY(0) rotateX(0);
                    opacity: 1
                }
            }

            .subtitle {
                font-size: clamp(1rem, 2.5vw, 1.3rem);
                font-weight: 300;
                color: rgba(255, 255, 255, .7);
                max-width: 500px;
                margin: 0 auto;
                letter-spacing: 2px;
                animation: fadeIn 1.5s ease-out 1s both
            }

            @keyframes fadeIn {
                0% {
                    opacity: 0
                }

                100% {
                    opacity: 1
                }
            }

            .line {
                width: 100px;
                height: 2px;
                background: linear-gradient(90deg, transparent, #fff, transparent);
                margin: 40px auto;
                animation: expandLine 1.5s ease-out .8s both
            }

            @keyframes expandLine {
                0% {
                    width: 0
                }

                100% {
                    width: 100px
                }
            }
        </style>
    </head>

    <body>
        <div class="bg-texture"></div>
        <div class="content">
            <div
                style="font-size:clamp(1.2rem, 4vw, 2rem);font-weight:700;margin-bottom:20px;color:rgba(255,255,255,0.7);letter-spacing:5px;text-transform:uppercase;">
                <?php echo esc_html($domain); ?>
            </div>
            <div class="title">
                <div class="word"><span class="letter">U</span><span class="letter">P</span><span
                        class="letter">G</span><span class="letter">R</span><span class="letter">A</span><span
                        class="letter">D</span><span class="letter">I</span><span class="letter">N</span><span
                        class="letter">G</span></div>
            </div>
            <div class="line"></div>
            <p class="subtitle">Pushing boundaries, elevating experiences</p>
        </div>
    </body>

    </html>
    <?php
}

/**
 * Renders the aurora borealis maintenance page.
 */
function lumnav_mm_render_aurora_page()
{
    $domain = preg_replace('/^www\./', '', wp_parse_url(home_url(), PHP_URL_HOST));
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($domain); ?> - Maintenance Mode</title>
        <meta name="description"
            content="<?php echo esc_attr($domain); ?> is crafting something beautiful. Returning soon.">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box
            }

            body {
                font-family: 'Poppins', sans-serif;
                background: #000a1f;
                color: #fff;
                overflow: hidden;
                min-height: 100vh
            }

            #canvas-aurora {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 1;
                pointer-events: none;
            }

            .content {
                position: relative;
                z-index: 2;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                height: 100vh;
                text-align: center;
                padding: 20px
            }

            h1 {
                font-size: clamp(2.5rem, 6vw, 4rem);
                font-weight: 600;
                margin-bottom: 20px;
                text-shadow: 0 0 20px rgba(100, 255, 200, .5);
                animation: fadeIn 2s ease-out
            }

            p {
                font-size: clamp(1rem, 2vw, 1.2rem);
                color: rgba(255, 255, 255, .8);
                max-width: 600px;
                font-weight: 300;
                line-height: 1.8;
                animation: fadeIn 2s ease-out .5s both
            }

            @keyframes fadeIn {
                0% {
                    opacity: 0;
                    transform: translateY(20px)
                }

                100% {
                    opacity: 1;
                    transform: translateY(0)
                }
            }
        </style>
    </head>

    <body><canvas id="canvas-aurora"></canvas>
        <div class="content">
            <div
                style="font-size:clamp(1rem, 3vw, 1.4rem);font-weight:500;margin-bottom:15px;color:rgba(100,255,200,0.8);text-shadow:0 0 10px rgba(100,255,200,0.3);text-transform:lowercase;letter-spacing:2px;">
                <?php echo esc_html($domain); ?>
            </div>
            <h1>Serene Maintenance</h1>
            <p>Like the aurora borealis dancing across the sky, we're crafting something beautiful. Returning soon.</p>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
        <script>let scene, camera, renderer, aurora; fun                               cti                        on init() {
                scene = new THREE.Scene(); camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, .1, 1000); camera.position.z = 2; renderer = new THREE.WebGLRenderer({ canvas: document.getElementById('canvas-aurora'), alpha: true }); renderer.setSize(window.innerWidth, window.innerHeight); const starGeo = new THREE.BufferGeometry(); const starVerts = []; for (let i = 0; i < 1000; i++) { const x = (Math.random() - .5) * 100; const y = (Math.random() - .5) * 100; const z = (Math.random() - .5) * 100; starVerts.push(x, y, z) } starGeo.setAttribute('position', new THREE.Float32BufferAttribute(starVerts, 3)); const starMat = new THREE.PointsMaterial({ color: 0xffffff, size: .05, transparent: true, opacity: .8 }); const stars = new THREE.Points(starGeo, starMat); scene.add(stars); const shaderMaterial = new THREE.ShaderMaterial({
                    uniforms: { time: { value: 0 }, color1: { value: new THREE.Color(0x00ff88) }, color2: { value: new THREE.Color(0x0088ff) }, color3: { value: new THREE.Color(0xff00ff) } }, vertexShader: `
            varying vec2 vUv;
            void main() {
              vUv = uv;
              gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
            }
          `, fragmentShader: `
            uniform float time;
            uniform vec3 color1;
            uniform vec3 color2;
            uniform vec3 color3;
            varying vec2 vUv;
            void main() {
              vec2 uv = vUv;
              float wave1 = sin(uv.x * 3.0 + time * 0.5) * 0.5 + 0.5;
              float wave2 = sin(uv.x * 5.0 - time * 0.3 + uv.y * 2.0) * 0.5 + 0.5;
              float wave3 = sin(uv.x * 2.0 + time * 0.7 - uv.y * 3.0) * 0.5 + 0.5;
              vec3 color = mix(color1, color2, wave1);
              color = mix(color, color3, wave2);
              float alpha = wave1 * wave2 * wave3 * (1.0 - uv.y * 0.5);
              gl_FragColor = vec4(color, alpha * 0.6);
            }
          `, transparent: true, side: THREE.DoubleSide
                }); const geometry = new THREE.PlaneGeometry(10, 5, 32, 32); aurora = new THREE.Mesh(geometry, shaderMaterial); aurora.position.y = 1; scene.add(aurora); window.addEventListener('resize', onResize); animate()
            } function onResize() { camera.aspect = window.innerWidth / window.innerHeight; camera.updateProjectionMatrix(); renderer.setSize(window.innerWidth, window.innerHeight) } function animate() { requestAnimationFrame(animate); aurora.material.uniforms.time.value += .01; renderer.render(scene, camera) } init();</script>
    </body>

    </html>
    <?php
}

/**
 * Renders the holographic interface maintenance page.
 */
function lumnav_mm_render_holographic_page()
{
    $domain = preg_replace('/^www\./', '', wp_parse_url(home_url(), PHP_URL_HOST));
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($domain); ?> - System Update</title>
        <meta name="description"
            content="<?php echo esc_attr($domain); ?> holographic interface recalibration in progress.">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box
            }

            body {
                font-family: 'Orbitron', sans-serif;
                background: #000;
                color: #0ff;
                overflow: hidden;
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                position: relative
            }

            .hex-grid {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-image: repeating-linear-gradient(0deg, transparent, transparent 40px, rgba(0, 255, 255, .03) 40px, rgba(0, 255, 255, .03) 41px), repeating-linear-gradient(90deg, transparent, transparent 40px, rgba(0, 255, 255, .03) 40px, rgba(0, 255, 255, .03) 41px);
                z-index: 1;
                pointer-events: none;
            }

            .scanline {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 2px;
                background: linear-gradient(90deg, transparent, #0ff, transparent);
                box-shadow: 0 0 10px #0ff;
                animation: scan 4s linear infinite;
                z-index: 3;
                pointer-events: none;
            }

            @keyframes scan {
                0% {
                    transform: translateY(0)
                }

                100% {
                    transform: translateY(100vh)
                }
            }

            .content {
                position: relative;
                z-index: 2;
                text-align: center;
                padding: 20px
            }

            h1 {
                font-size: clamp(2rem, 6vw, 4.5rem);
                font-weight: 900;
                margin-bottom: 30px;
                text-transform: uppercase;
                letter-spacing: 8px;
                background: linear-gradient(90deg, #0ff, #f0f, #0ff);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                background-size: 200% 100%;
                animation: shimmer 3s linear infinite, glitch 5s steps(2) infinite;
                position: relative
            }

            @keyframes shimmer {
                0% {
                    background-position: 0% 50%
                }

                100% {
                    background-position: 200% 50%
                }
            }

            @keyframes glitch {

                0%,
                90%,
                100% {
                    text-shadow: 0 0 10px #0ff, 0 0 20px #0ff, 0 0 30px #0ff
                }

                92% {
                    text-shadow: 2px 0 10px #f0f, -2px 0 10px #0ff, 0 0 30px #0ff
                }

                94% {
                    text-shadow: -2px 0 10px #0ff, 2px 0 10px #f0f, 0 0 30px #0ff
                }

                96% {
                    text-shadow: 0 0 10px #0ff, 0 0 20px #0ff, 0 0 30px #0ff
                }
            }

            p {
                font-size: clamp(1rem, 2vw, 1.2rem);
                color: #0ff;
                font-weight: 400;
                letter-spacing: 3px;
                text-shadow: 0 0 5px #0ff;
                opacity: .8;
                animation: pulse 2s ease-in-out infinite
            }

            @keyframes pulse {

                0%,
                100% {
                    opacity: .8
                }

                50% {
                    opacity: .4
                }
            }

            .status {
                display: inline-block;
                margin-top: 40px;
                padding: 15px 30px;
                border: 2px solid #0ff;
                box-shadow: 0 0 10px #0ff, inset 0 0 10px rgba(0, 255, 255, .1);
                font-size: 14px;
                letter-spacing: 3px;
                text-transform: uppercase;
                animation: borderPulse 2s ease-in-out infinite
            }

            @keyframes borderPulse {

                0%,
                100% {
                    border-color: #0ff;
                    box-shadow: 0 0 10px #0ff, inset 0 0 10px rgba(0, 255, 255, .1)
                }

                50% {
                    border-color: #f0f;
                    box-shadow: 0 0 10px #f0f, inset 0 0 10px rgba(255, 0, 255, .1)
                }
            }
        </style>
    </head>

    <body>
        <div class="hex-grid"></div>
        <div class="scanline"></div>
        <div class="content">
            <div
                style="font-size:clamp(1rem, 3vw, 1.4rem);font-weight:700;margin-bottom:20px;color:#0ff;text-shadow:0 0 5px #0ff;letter-spacing:5px;text-transform:uppercase;">
                <?php echo esc_html($domain); ?>
            </div>
            <h1>MAINTENANCE</h1>
            <p>HOLOGRAPHIC INTERFACE RECALIBRATION IN PROGRESS</p>
            <div class="status">SYSTEM UPDATE</div>
        </div>
    </body>

    </html>
    <?php
}

/**
 * Renders the neon cyberpunk maintenance page.
 */
function lumnav_mm_render_neon_page()
{
    $domain = preg_replace('/^www\./', '', wp_parse_url(home_url(), PHP_URL_HOST));
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($domain); ?> - Coming Soon</title>
        <meta name="description" content="<?php echo esc_attr($domain); ?> is coming soon. We'll be back shortly.">
        <link href="https://fonts.googleapis.com/css2?family=Teko:wght@700&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box
            }

            body {
                font-family: 'Teko', sans-serif;
                background: #0a0a0f;
                color: #fff;
                overflow: hidden;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                position: relative
            }

            .cityscape {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 200px;
                background: linear-gradient(to bottom, transparent, #0a0a0f 80%), repeating-linear-gradient(90deg, #1a1a2e 0px, #1a1a2e 50px, #16162a 50px, #16162a 100px);
                z-index: 1;
                pointer-events: none;
            }

            .neon-container {
                position: relative;
                z-index: 2;
                text-align: center;
                padding: 20px
            }

            .domain {
                font-size: clamp(3rem, 12vw, 8rem);
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 8px;
                line-height: 1;
                margin-bottom: 30px;
                color: #fff;
                text-shadow: 0 0 10px #ff006e, 0 0 20px #ff006e, 0 0 40px #ff006e, 0 0 80px #ff006e, 0 0 120px #ff006e;
                animation: flicker 3s infinite, neonPulse 2s ease-in-out infinite
            }

            @keyframes flicker {

                0%,
                18%,
                22%,
                25%,
                53%,
                57%,
                100% {
                    text-shadow: 0 0 10px #ff006e, 0 0 20px #ff006e, 0 0 40px #ff006e, 0 0 80px #ff006e, 0 0 120px #ff006e
                }

                20%,
                24%,
                55% {
                    text-shadow: none
                }
            }

            @keyframes neonPulse {

                0%,
                100% {
                    text-shadow: 0 0 10px #ff006e, 0 0 20px #ff006e, 0 0 40px #ff006e, 0 0 80px #ff006e, 0 0 120px #ff006e
                }

                50% {
                    text-shadow: 0 0 5px #ff006e, 0 0 10px #ff006e, 0 0 20px #ff006e, 0 0 40px #ff006e, 0 0 60px #ff006e
                }
            }

            .subtitle {
                font-size: clamp(1.2rem, 4vw, 2rem);
                color: #00f5ff;
                text-shadow: 0 0 10px #00f5ff, 0 0 20px #00f5ff;
                letter-spacing: 4px;
                margin-bottom: 15px;
                animation: flicker 4s infinite
            }

            .message {
                font-size: clamp(0.9rem, 2.5vw, 1.2rem);
                color: rgba(255, 255, 255, 0.8);
                max-width: 600px;
                margin: 0 auto;
                letter-spacing: 2px
            }

            .neon-border {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 90%;
                height: 80%;
                border: 3px solid #ff006e;
                box-shadow: 0 0 20px #ff006e, inset 0 0 20px #ff006e;
                animation: borderPulse 3s ease-in-out infinite;
                pointer-events: none
            }

            @keyframes borderPulse {

                0%,
                100% {
                    opacity: .3
                }

                50% {
                    opacity: .7
                }
            }

            @media(max-width:768px) {
                .domain {
                    letter-spacing: 4px
                }

                .subtitle,
                .message {
                    letter-spacing: 2px
                }
            }
        </style>
    </head>

    <body>
        <div class="cityscape"></div>
        <div class="content">
            <div class="domain" data-text="<?php echo esc_attr($domain); ?>"><?php echo esc_html($domain); ?></div>
            <div class="status">COMING SOON</div>
            <div class="message">WE ARE CURRENTLY BUILDING SOMETHING AMAZING</div>
        </div>
    </body>

    </html>
    <?php
}

/**
 * Renders the minimalist Swiss design maintenance page.
 */
function lumnav_mm_render_swiss_page()
{
    $domain = preg_replace('/^www\./', '', wp_parse_url(home_url(), PHP_URL_HOST));
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($domain); ?> - In Development</title>
        <meta name="description"
            content="<?php echo esc_attr($domain); ?> is in development. Our service will resume shortly.">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box
            }

            body {
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                background: #fff;
                color: #000;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                padding: 40px
            }

            .container {
                max-width: 1000px;
                width: 100%;
                position: relative
            }

            .red-line {
                position: absolute;
                top: 0;
                left: 0;
                width: 100px;
                height: 3px;
                background: #ff0000
            }

            .domain {
                font-size: clamp(3.5rem, 10vw, 9rem);
                font-weight: 700;
                line-height: 0.9;
                letter-spacing: -0.04em;
                margin: 60px 0 40px 0;
                text-transform: lowercase;
                word-break: break-word;
                animation: slideInLeft 1s ease-out;
            }

            @keyframes slideInLeft {
                from {
                    transform: translateX(-50px);
                    opacity: 0;
                }

                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            .grid-numbers {
                position: absolute;
                right: 0;
                top: 60px;
                font-size: 14px;
                color: #888;
                letter-spacing: 2px;
                font-weight: 300
            }

            .message-block {
                max-width: 400px;
                margin-top: 60px;
                border-left: 2px solid #000;
                padding-left: 20px
            }

            .status {
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 3px;
                font-weight: 300;
                margin-bottom: 15px;
                color: #666
            }

            .detail {
                font-size: 16px;
                line-height: 1.6;
                font-weight: 300
            }

            .footer {
                position: absolute;
                bottom: 0;
                right: 0;
                font-size: 11px;
                color: #ccc;
                letter-spacing: 1px
            }

            @media(max-width:768px) {
                .domain {
                    margin: 40px 0 30px 0
                }

                .grid-numbers {
                    display: none
                }

                .message-block {
                    margin-top: 40px
                }
            }
        </style>
    </head>

    <body>
        <div class="grid-bg"></div>
        <div class="content">
            <div class="domain"><?php echo esc_html($domain); ?></div>
            <div class="status">Status / In Development</div>
            <div class="message">We are currently updating our platform. Please check back later.</div>
        </div>
    </body>

    </html>
    <?php
}

/**
 * Renders the retro VHS maintenance page.
 */
function lumnav_mm_render_vhs_page()
{
    $domain = preg_replace('/^www\./', '', wp_parse_url(home_url(), PHP_URL_HOST));
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($domain); ?> - Loading</title>
        <meta name="description" content="<?php echo esc_attr($domain); ?> initializing... please stand by.">
        <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box
            }

            body {
                font-family: 'VT323', monospace;
                background: #000;
                color: #fff;
                overflow: hidden;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                position: relative
            }

            .static {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAYAAACp8Z5+AAAAG0lEQVQYV2NkYGD4z8DAwMgABXAGNgGwSgwVAFbmAgXQdISfAAAAAElFTkSuQmCC');
                opacity: .05;
                z-index: 1;
                pointer-events: none;
                animation: noise .1s infinite
            }

            .scanlines {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255, 255, 255, .03) 2px, rgba(255, 255, 255, .03) 4px);
                z-index: 2;
                pointer-events: none
            }

            @keyframes noise {
                0% {
                    transform: translate(0, 0)
                }

                10% {
                    transform: translate(-5%, -5%)
                }

                20% {
                    transform: translate(-10%, 5%)
                }

                30% {
                    transform: translate(5%, -10%)
                }

                40% {
                    transform: translate(-5%, 15%)
                }

                50% {
                    transform: translate(-10%, 5%)
                }

                60% {
                    transform: translate(15%, 0)
                }

                70% {
                    transform: translate(0, 10%)
                }

                80% {
                    transform: translate(-15%, 0)
                }

                90% {
                    transform: translate(10%, 5%)
                }

                100% {
                    transform: translate(5%, 0)
                }
            }

            .content {
                position: relative;
                z-index: 3;
                text-align: center;
                padding: 20px
            }

            .vhs-border {
                border: 4px solid #fff;
                padding: 40px 60px;
                box-shadow: inset 0 0 50px rgba(255, 255, 255, 0.1);
                background: rgba(0, 0, 0, 0.7)
            }

            .domain {
                font-size: clamp(2.5rem, 8vw, 6rem);
                letter-spacing: 8px;
                line-height: 1.2;
                margin-bottom: 25px;
                color: #ffd700;
                text-shadow: 2px 2px 0 #ff00ff, 4px 4px 0 #00ffff;
                animation: glitchText 5s infinite
            }

            @keyframes glitchText {

                0%,
                90%,
                100% {
                    text-shadow: 2px 2px 0 #ff00ff, 4px 4px 0 #00ffff
                }

                92% {
                    text-shadow: -2px 2px 0 #ff00ff, -4px 4px 0 #00ffff
                }

                94% {
                    text-shadow: 2px -2px 0 #ff00ff, 4px -4px 0 #00ffff
                }
            }

            .status-bar {
                font-size: clamp(1.5rem, 3vw, 2rem);
                margin-bottom: 15px;
                letter-spacing: 4px;
                color: #0f0
            }

            .indicator {
                display: inline-block;
                width: 15px;
                height: 15px;
                background: #f00;
                border-radius: 50%;
                margin-right: 10px;
                animation: blink 1s infinite
            }

            @keyframes blink {

                0%,
                50% {
                    opacity: 1
                }

                51%,
                100% {
                    opacity: 0
                }
            }

            .message {
                font-size: clamp(1.1rem, 2.5vw, 1.5rem);
                color: rgba(255, 255, 255, 0.8);
                letter-spacing: 2px
            }

            .timestamp {
                position: absolute;
                top: 20px;
                right: 20px;
                font-size: 24px;
                color: rgba(255, 255, 255, 0.5)
            }

            @media(max-width:768px) {
                .vhs-border {
                    padding: 30px 20px
                }

                .domain {
                    letter-spacing: 4px
                }

                .status-bar,
                .message {
                    letter-spacing: 2px
                }
            }
        </style>
    </head>

    <body>
        <div class="static-noise"></div>
        <div class="scanlines"></div>
        <div class="content">
            <div class="domain"><?php echo esc_html($domain); ?></div>
            <div class="status">LOADING</div>
            <div class="message">INITIALIZING...</div>
        </div>
    </body>

    </html>
    <?php
}

/**
 * Renders the brutalist design maintenance page.
 */
function lumnav_mm_render_brutalist_page()
{
    $domain = preg_replace('/^www\./', '', wp_parse_url(home_url(), PHP_URL_HOST));
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($domain); ?> - Loading</title>
        <meta name="description" content="<?php echo esc_attr($domain); ?> is loading. Services temporarily unavailable.">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box
            }

            body {
                font-family: Arial, Helvetica, sans-serif;
                background: #fff;
                color: #000;
                padding: 40px;
                min-height: 100vh;
                display: flex;
                align-items: center
            }

            .container {
                max-width: 100%
            }

            .domain {
                font-size: clamp(4rem, 12vw, 10rem);
                font-weight: 900;
                line-height: 0.85;
                letter-spacing: -0.05em;
                text-transform: uppercase;
                margin-bottom: 40px;
                text-shadow: 8px 8px 0 #000;
                word-break: break-word
            }

            .status-block {
                background: #000;
                color: #fff;
                padding: 30px;
                max-width: 600px;
                margin-bottom: 30px
            }

            .status {
                font-size: clamp(1.5rem, 4vw, 2.5rem);
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: 2px
            }

            .detail {
                font-size: clamp(1rem, 2.5vw, 1.3rem);
                margin-top: 15px;
                line-height: 1.4;
                font-weight: 400
            }

            .timestamp {
                font-size: clamp(2rem, 5vw, 4rem);
                font-weight: 900;
                margin-top: 40px
            }

            @media(max-width:768px) {
                body {
                    padding: 20px
                }

                .domain {
                    margin-bottom: 30px
                }
            }
        </style>
    </head>

    <body>
        <div class="content">
            <div class="domain"><?php echo esc_html($domain); ?></div>
            <div class="status">LOADING</div>
            <div class="message">SYSTEM UPGRADE IN PROGRESS</div>
        </div>
    </body>

    </html>
    <?php
}

/**
 * Renders the cosmic galaxy maintenance page.
 */
function lumnav_mm_render_galaxy_page()
{
    $domain = preg_replace('/^www\./', '', wp_parse_url(home_url(), PHP_URL_HOST));
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($domain); ?> - A New Universe</title>
        <meta name="description" content="<?php echo esc_attr($domain); ?> is exploring new frontiers. Launching soon.">
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@700&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box
            }

            body {
                font-family: 'Space Grotesk', sans-serif;
                background: #000;
                overflow: hidden;
                position: relative;
                min-height: 100vh
            }

            .stars {
                position: fixed;
                width: 100%;
                height: 100%;
                z-index: 1;
                pointer-events: none;
            }

            .star {
                position: absolute;
                width: 2px;
                height: 2px;
                background: #fff;
                border-radius: 50%;
                animation: twinkle 4s infinite
            }

            .star:nth-child(1) {
                top: 20%;
                left: 10%;
                animation-delay: 0s
            }

            .star:nth-child(2) {
                top: 40%;
                left: 80%;
                animation-delay: 1s
            }

            .star:nth-child(3) {
                top: 70%;
                left: 30%;
                animation-delay: 2s
            }

            .star:nth-child(4) {
                top: 15%;
                left: 60%;
                animation-delay: 0.5s
            }

            .star:nth-child(5) {
                top: 85%;
                left: 70%;
                animation-delay: 1.5s
            }

            .star:nth-child(6) {
                top: 50%;
                left: 20%;
                animation-delay: 2.5s
            }

            @keyframes twinkle {

                0%,
                100% {
                    opacity: 1
                }

                50% {
                    opacity: 0.3
                }
            }

            .content {
                position: relative;
                z-index: 2;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                text-align: center;
                padding: 20px
            }

            .domain {
                font-size: clamp(2.5rem, 8vw, 6rem);
                font-weight: 700;
                background: linear-gradient(135deg, #667eea, #764ba2, #f093fb, #4facfe);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                background-size: 300% 300%;
                animation: galaxyShift 8s ease infinite;
                margin-bottom: 30px;
                text-transform: lowercase;
                letter-spacing: -1px
            }

            @keyframes galaxyShift {
                0% {
                    background-position: 0% 50%
                }

                50% {
                    background-position: 100% 50%
                }

                100% {
                    background-position: 0% 50%
                }
            }

            .message {
                font-size: clamp(1.1rem, 2.5vw, 1.4rem);
                color: rgba(255, 255, 255, 0.8);
                max-width: 600px;
                letter-spacing: 1px
            }

            .nebula {
                position: fixed;
                width: 400px;
                height: 400px;
                background: radial-gradient(circle, rgba(102, 126, 234, 0.3), transparent);
                border-radius: 50%;
                filter: blur(60px);
                animation: float 20s infinite ease-in-out
            }

            .nebula:nth-child(1) {
                top: 10%;
                left: 20%
            }

            .nebula:nth-child(2) {
                bottom: 20%;
                right: 10%;
                animation-delay: 5s
            }

            @keyframes float {

                0%,
                100% {
                    transform: translate(0, 0)
                }

                50% {
                    transform: translate(50px, 50px)
                }
            }

            @media(max-width:768px) {
                .domain {
                    letter-spacing: 0
                }
            }
        </style>
    </head>

    <body>
        <div class="nebula"></div>
        <div class="nebula"></div>
        <div class="stars">
            <div class="star"></div>
            <div class="star"></div>
            <div class="star"></div>
            <div class="star"></div>
            <div class="star"></div>
            <div class="star"></div>
        </div>
        <div class="content">
            <div class="domain"><?php echo esc_html($domain); ?></div>
            <div class="message">Exploring new frontiers. Back in a moment.</div>
        </div>
    </body>

    </html>
    <?php
}

/**
 * Renders the matrix code rain maintenance page.
 */
function lumnav_mm_render_matrix_page()
{
    $domain = preg_replace('/^www\./', '', wp_parse_url(home_url(), PHP_URL_HOST));
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($domain); ?> - System Boot</title>
        <meta name="description" content="<?php echo esc_attr($domain); ?> system boot sequence initiated.">
        <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:wght@700&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box
            }

            body {
                font-family: 'Courier Prime', monospace;
                background: #000;
                color: #0f0;
                overflow: hidden;
                position: relative;
                min-height: 100vh
            }

            #matrix {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 1;
                opacity: 0.3;
                pointer-events: none;
            }

            .content {
                position: relative;
                z-index: 2;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                text-align: center;
                padding: 20px
            }

            .domain {
                font-size: clamp(2.5rem, 8vw, 6rem);
                font-weight: 700;
                color: #0f0;
                text-shadow: 0 0 10px #0f0, 0 0 20px #0f0;
                margin-bottom: 30px;
                letter-spacing: 4px;
                animation: glitchMatrix 3s infinite;
                text-transform: uppercase
            }

            @keyframes glitchMatrix {

                0%,
                90%,
                100% {
                    transform: translate(0)
                }

                92% {
                    transform: translate(-2px, 2px)
                }

                94% {
                    transform: translate(2px, -2px)
                }

                96% {
                    transform: translate(-2px, -2px)
                }
            }

            .status {
                font-size: clamp(1.2rem, 3vw, 1.8rem);
                letter-spacing: 3px;
                color: #0f0;
                margin-bottom: 15px
            }

            .message {
                font-size: clamp(0.9rem, 2vw, 1.1rem);
                color: rgba(0, 255, 0, 0.7);
                letter-spacing: 2px;
                max-width: 500px
            }

            .cursor {
                display: inline-block;
                width: 10px;
                height: 20px;
                background: #0f0;
                animation: blink 1s step-end infinite;
                margin-left: 5px
            }

            @keyframes blink {

                0%,
                50% {
                    opacity: 1
                }

                51%,
                100% {
                    opacity: 0
                }
            }
        </style>
    </head>

    <body><canvas id="matrix"></canvas>
        <div class="content">
            <div class="domain"><?php echo esc_html($domain); ?></div>
            <div class="status">SYSTEM BOOT<span class="cursor"></span></div>
            <div class="message">Initializing matrix parameters...</div>
        </div>
        <script>const canvas = document.getElementById('matrix'); const ctx = canvas.getContext('2d'); canvas.width = window.innerWidth; canvas.height = window.innerHeight; const chars = '01'; const fontSize = 14; const columns = canvas.width / fontSize; const drops = []; for (let i = 0; i < columns; i++) { drops[i] = 1 } function draw() { ctx.fillStyle = 'rgba(0,0,0,0.05)'; ctx.fillRect(0, 0, canvas.width, canvas.height); ctx.fillStyle = '#0F0'; ctx.font = fontSize + 'px monospace'; for (let i = 0; i < drops.length; i++) { const text = chars[Math.floor(Math.random() * chars.length)]; ctx.fillText(text, i * fontSize, drops[i] * fontSize); if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) { drops[i] = 0 } drops[i]++ } } setInterval(draw, 33);</script>
    </body>

    </html>
    <?php
}

/**
 * Renders the vaporwave aesthetic maintenance page.
 */
function lumnav_mm_render_vaporwave_page()
{
    $domain = preg_replace('/^www\./', '', wp_parse_url(home_url(), PHP_URL_HOST));
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($domain); ?> - Coming Soon</title>
        <meta name="description" content="<?php echo esc_attr($domain); ?> is coming soon.">
        <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@700&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box
            }

            body {
                font-family: 'Rajdhani', sans-serif;
                background: linear-gradient(180deg, #ff6ec7 0%, #7873f5 50%, #4facfe 100%);
                min-height: 100vh;
                overflow: hidden;
                position: relative;
                display: flex;
                justify-content: center;
                align-items: center
            }

            .grid {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 50%;
                background: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.3) 100%), repeating-linear-gradient(90deg, rgba(255, 255, 255, 0.1) 0px, rgba(255, 255, 255, 0.1) 2px, transparent 2px, transparent 50px), repeating-linear-gradient(0deg, rgba(255, 255, 255, 0.1) 0px, rgba(255, 255, 255, 0.1) 2px, transparent 2px, transparent 50px);
                transform: perspective(500px) rotateX(60deg);
                transform-origin: bottom;
                z-index: 1;
                pointer-events: none;
            }

            .content {
                position: relative;
                z-index: 2;
                text-align: center;
                padding: 20px
            }

            .domain {
                font-size: clamp(2.5rem, 8vw, 6rem);
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 15px;
                background: linear-gradient(90deg, #ff6ec7, #7873f5, #4facfe, #00f5ff);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                text-shadow: 3px 3px 0 rgba(0, 0, 0, 0.3);
                margin-bottom: 20px;
                animation: vaporShift 6s ease infinite;
                background-size: 200% 200%
            }

            @keyframes vaporShift {
                0% {
                    background-position: 0% 50%
                }

                50% {
                    background-position: 100% 50%
                }

                100% {
                    background-position: 0% 50%
                }
            }

            .tagline {
                font-size: clamp(1.3rem, 3.5vw, 2rem);
                color: #fff;
                letter-spacing: 8px;
                text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.5);
                text-transform: uppercase
            }

            .sun {
                position: fixed;
                top: 30%;
                left: 50%;
                transform: translateX(-50%);
                width: 200px;
                height: 200px;
                background: linear-gradient(180deg, #ff6ec7, #ffd700);
                border-radius: 50%;
                box-shadow: 0 0 60px rgba(255, 110, 199, 0.6);
                z-index: 0;
                Animation: pulse 4s ease-in-out infinite;
                pointer-events: none;
            }

            @keyframes pulse {

                0%,
                100% {
                    transform: translateX(-50%) scale(1)
                }

                50% {
                    transform: translateX(-50%) scale(1.1)
                }
            }

            @media(max-width:768px) {
                .domain {
                    letter-spacing: 8px
                }

                .tagline {
                    letter-spacing: 4px
                }
            }
        </style>
    </head>

    <body>
        <div class="sun"></div>
        <div class="grid"></div>
        <div class="content">
            <div class="domain"><?php echo esc_html($domain); ?></div>
            <div class="tagline">Coming Soon</div>
        </div>
    </body>

    </html>
    <?php
}

/**
 * Renders the particle explosion maintenance page.
 */
function lumnav_mm_render_particles_page()
{
    $domain = preg_replace('/^www\./', '', wp_parse_url(home_url(), PHP_URL_HOST));
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($domain); ?> - Initializing</title>
        <meta name="description" content="<?php echo esc_attr($domain); ?> initializing systems...">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box
            }

            body {
                font-family: 'Montserrat', sans-serif;
                background: #1a1a2e;
                overflow: hidden;
                min-height: 100vh
            }

            #particles {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 1;
                pointer-events: none;
            }

            .content {
                position: relative;
                z-index: 2;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                text-align: center;
                padding: 20px
            }

            .domain {
                font-size: clamp(2.5rem, 8vw, 6rem);
                font-weight: 900;
                background: linear-gradient(135deg, #667eea, #764ba2, #f093fb);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                margin-bottom: 25px;
                text-transform: uppercase;
                letter-spacing: 3px;
                animation: fadeIn 2s ease-out
            }

            @keyframes fadeIn {
                0% {
                    opacity: 0;
                    transform: scale(0.8)
                }

                100% {
                    opacity: 1;
                    transform: scale(1)
                }
            }

            .message {
                font-size: clamp(1.1rem, 2.5vw, 1.4rem);
                color: rgba(255, 255, 255, 0.7);
                max-width: 600px;
                margin-bottom: 30px
            }

            .loader {
                display: flex;
                gap: 10px
            }

            .dot {
                width: 15px;
                height: 15px;
                background: linear-gradient(135deg, #667eea, #764ba2);
                border-radius: 50%;
                animation: bounce 1.4s infinite ease-in-out
            }

            .dot:nth-child(1) {
                animation-delay: -0.32s
            }

            .dot:nth-child(2) {
                animation-delay: -0.16s
            }

            @keyframes bounce {

                0%,
                80%,
                100% {
                    transform: scale(0)
                }

                40% {
                    transform: scale(1)
                }
            }

            @media(max-width:768px) {
                .domain {
                    letter-spacing: 2px
                }
            }
        </style>
    </head>

    <body><canvas id="particles"></canvas>
        <div class="content">
            <div class="domain"><?php echo esc_html($domain); ?></div>
            <div class="message">Initializing systems...</div>
            <div class="loader">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
        </div>
        <script>const canvas = document.getElementById('particles'); const ctx = canvas.getContext('2d'); canvas.width = window.innerWidth; canvas.height = window.innerHeight; const particles = []; class Particle { constructor() { this.x = canvas.width / 2; this.y = canvas.height / 2; this.vx = (Math.random() - 0.5) * 5; this.vy = (Math.random() - 0.5) * 5; this.size = Math.random() * 3 + 1; this.color = `hsl(${Math.random() * 60 + 240},70%,60%)` } update() { this.x += this.vx; this.y += this.vy; this.vy += 0.1 } draw() { ctx.fillStyle = this.color; ctx.beginPath(); ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2); ctx.fill() } } function animate() { ctx.fillStyle = 'rgba(26,26,46,0.1)'; ctx.fillRect(0, 0, canvas.width, canvas.height); if (Math.random() < 0.3) { particles.push(new Particle()) } particles.forEach((p, i) => { p.update(); p.draw(); if (p.y > canvas.height) { particles.splice(i, 1) } }); requestAnimationFrame(animate) } animate();</script>
    </body>

    </html>
    <?php
}

/**
 * Renders the geometric patterns maintenance page.
 */
function lumnav_mm_render_geometric_page()
{
    $domain = preg_replace('/^www\./', '', wp_parse_url(home_url(), PHP_URL_HOST));
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($domain); ?> - Constructing</title>
        <meta name="description" content="<?php echo esc_attr($domain); ?> constructing architecture...">
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@800&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box
            }

            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                background: #f5f5f5;
                min-height: 100vh;
                overflow: hidden;
                position: relative
            }

            .pattern {
                position: fixed;
                width: 100%;
                height: 100%;
                z-index: 1;
                pointer-events: none;
            }

            .pattern svg {
                width: 100%;
                height: 100%;
                opacity: 0.1
            }

            .content {
                position: relative;
                z-index: 2;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                text-align: center;
                padding: 20px
            }

            .domain {
                font-size: clamp(2.5rem, 8vw, 6rem);
                font-weight: 800;
                color: #2d2d2d;
                margin-bottom: 25px;
                text-transform: lowercase;
                letter-spacing: -1px;
                position: relative
            }

            .domain::after {
                content: '';
                position: absolute;
                bottom: -10px;
                left: 50%;
                transform: translateX(-50%);
                width: 80px;
                height: 4px;
                background: linear-gradient(90deg, #667eea, #764ba2)
            }

            .message {
                font-size: clamp(1rem, 2.5vw, 1.3rem);
                color: #666;
                max-width: 500px;
                margin-top: 30px
            }

            .shapes {
                position: fixed;
                width: 100%;
                height: 100%;
                z-index: 0;
                pointer-events: none;
            }

            .shape {
                position: absolute;
                border: 3px solid rgba(102, 126, 234, 0.3);
                animation: rotate 20s linear infinite
            }

            .shape:nth-child(1) {
                width: 200px;
                height: 200px;
                top: 10%;
                left: 10%;
                border-radius: 50%
            }

            .shape:nth-child(2) {
                width: 150px;
                height: 150px;
                bottom: 20%;
                right: 15%;
                transform: rotate(45deg)
            }

            .shape:nth-child(3) {
                width: 100px;
                height: 100px;
                top: 60%;
                left: 70%;
                border-radius: 50%;
                animation-delay: 5s
            }

            @keyframes rotate {
                0% {
                    transform: rotate(0deg)
                }

                100% {
                    transform: rotate(360deg)
                }
            }

            @media(max-width:768px) {
                .domain {
                    letter-spacing: 0
                }
            }
        </style>
    </head>

    <body>
        <div class="shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
        <div class="pattern"><svg>
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#667eea" stroke-width="1" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg></div>
        <div class="content">
            <div class="domain"><?php echo esc_html($domain); ?></div>
            <div class="message">Constructing architecture...</div>
        </div>
    </body>

    </html>
    <?php
}

/**
 * Renders the glitch art maintenance page.
 */
function lumnav_mm_render_glitch_page()
{
    $domain = preg_replace('/^www\./', '', wp_parse_url(home_url(), PHP_URL_HOST));
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($domain); ?> - Coming Soon</title>
        <meta name="description" content="<?php echo esc_attr($domain); ?> is coming soon.">
        <link href="https://fonts.googleapis.com/css2?family=Rubik+Glitch&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box
            }

            body {
                font-family: 'Rubik Glitch', cursive;
                background: #000;
                color: #fff;
                min-height: 100vh;
                overflow: hidden;
                display: flex;
                justify-content: center;
                align-items: center;
                position: relative
            }

            .noise {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAYAAACp8Z5+AAAAG0lEQVQYV2NkYGD4z8DAwMgABXAGNgGwSgwVAFbmAgXQdISfAAAAAElFTkSuQmCC');
                opacity: 0.03;
                animation: noise 0.2s infinite;
                z-index: 1;
                pointer-events: none
            }

            @keyframes noise {
                0% {
                    transform: translate(0, 0)
                }

                10% {
                    transform: translate(-5%, -5%)
                }

                20% {
                    transform: translate(-10%, 5%)
                }

                30% {
                    transform: translate(5%, -10%)
                }

                40% {
                    transform: translate(-5%, 15%)
                }

                50% {
                    transform: translate(-10%, 5%)
                }

                60% {
                    transform: translate(15%, 0)
                }

                70% {
                    transform: translate(0, 10%)
                }

                80% {
                    transform: translate(-15%, 0)
                }

                90% {
                    transform: translate(10%, 5%)
                }

                100% {
                    transform: translate(5%, 0)
                }
            }

            .content {
                position: relative;
                z-index: 2;
                text-align: center;
                padding: 20px
            }

            .domain {
                font-size: clamp(2.5rem, 8vw, 6rem);
                font-weight: 700;
                margin-bottom: 30px;
                text-transform: uppercase;
                letter-spacing: 5px;
                position: relative;
                animation: glitchAnim 5s infinite
            }

            @keyframes glitchAnim {

                0%,
                90%,
                100% {
                    transform: translate(0);
                    color: #fff
                }

                91% {
                    transform: translate(-5px, 2px);
                    color: #f0f
                }

                92% {
                    transform: translate(5px, -2px);
                    color: #0ff
                }

                93% {
                    transform: translate(-2px, -5px);
                    color: #f0f
                }

                94% {
                    transform: translate(2px, 5px);
                    color: #0ff
                }

                95% {
                    transform: translate(0);
                    color: #fff
                }
            }

            .domain::before,
            .domain::after {
                content: attr(data-text);
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%
            }

            .domain::before {
                left: 2px;
                text-shadow: -2px 0 #f0f;
                clip: rect(24px, 550px, 90px, 0);
                animation: glitchBefore 3s infinite linear alternate-reverse
            }

            @keyframes glitchBefore {
                0% {
                    clip: rect(65px, 9999px, 120px, 0)
                }

                5% {
                    clip: rect(30px, 9999px, 90px, 0)
                }

                10% {
                    clip: rect(85px, 9999px, 140px, 0)
                }

                15% {
                    clip: rect(20px, 9999px, 60px, 0)
                }

                20% {
                    clip: rect(95px, 9999px, 150px, 0)
                }

                25% {
                    clip: rect(10px, 9999px, 50px, 0)
                }

                30% {
                    clip: rect(75px, 9999px, 130px, 0)
                }

                100% {
                    clip: rect(45px, 9999px, 100px, 0)
                }
            }

            .domain::after {
                left: -2px;
                text-shadow: 2px 0 #0ff;
                clip: rect(85px, 550px, 140px, 0);
                animation: glitchAfter 2.5s infinite linear alternate-reverse
            }

            @keyframes glitchAfter {
                0% {
                    clip: rect(40px, 9999px, 95px, 0)
                }

                5% {
                    clip: rect(70px, 9999px, 125px, 0)
                }

                10% {
                    clip: rect(25px, 9999px, 75px, 0)
                }

                15% {
                    clip: rect(90px, 9999px, 145px, 0)
                }

                20% {
                    clip: rect(15px, 9999px, 55px, 0)
                }

                100% {
                    clip: rect(55px, 9999px, 110px, 0)
                }
            }

            .status {
                font-size: clamp(1rem, 2.5vw, 1.3rem);
                color: #f0f;
                letter-spacing: 3px;
                margin-bottom: 10px;
                font-family: monospace
            }

            .message {
                font-size: clamp(0.9rem, 2vw, 1.1rem);
                color: rgba(255, 255, 255, 0.6);
                letter-spacing: 1px;
                font-family: monospace
            }

            @media(max-width:768px) {
                .domain {
                    letter-spacing: 3px
                }
            }
        </style>
    </head>

    <body>
        <div class="noise"></div>
        <div class="content">
            <div class="domain" data-text="<?php echo esc_attr($domain); ?>"><?php echo esc_html($domain); ?></div>
            <div class="status">COMING SOON</div>
            <div class="message">DATA INCOMING...</div>
        </div>
    </body>

    </html>
    <?php
}


/**
 * Adds styling for the admin bar button and settings page.
 */
function lumnav_mm_admin_and_settings_styles()
{
    if (!is_user_logged_in()) {
        return;
    }
    ?>
    <style>
        #wp-admin-bar-lumnav-maintenance-mode.lumnav-mm-on>.ab-item {
            background-color: #D54E21 !important;
        }

        #wp-admin-bar-lumnav-maintenance-mode.lumnav-mm-off>.ab-item {
            background-color: #46B450 !important;
        }
    </style>
    <?php
    if (function_exists('get_current_screen')) {
        $screen = get_current_screen();
        if ($screen && $screen->id === 'settings_page_lumnav-maintenance-mode') {
            ?>
            <style>
                .lumnav-mm-style-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                    gap: 20px;
                    margin-top: 20px;
                }

                .lumnav-mm-style-card {
                    position: relative;
                    display: block;
                    cursor: pointer;
                }

                .lumnav-mm-style-card input[type="radio"] {
                    position: absolute;
                    opacity: 0;
                    width: 1px;
                    height: 1px;
                }

                .lumnav-mm-style-card .preview-container {
                    border: 2px solid #ddd;
                    border-radius: 8px;
                    overflow: hidden;
                    transition: all 0.2s ease-in-out;
                }

                .lumnav-mm-style-card input[type="radio"]:checked+.preview-container {
                    border-color: #2271b1;
                    box-shadow: 0 0 0 1px #2271b1;
                }

                .lumnav-mm-style-card .preview-iframe-wrapper {
                    height: 200px;
                    /* Taller for better preview */
                    width: 100%;
                    position: relative;
                    overflow: hidden;
                    background-color: #f0f0f1;
                }

                .lumnav-mm-style-card .preview-iframe-wrapper iframe {
                    width: 400%;
                    height: 400%;
                    border: 0;
                    transform: scale(0.25);
                    transform-origin: 0 0;
                    position: absolute;
                    top: 0;
                    left: 0;
                    pointer-events: none;
                    /* Prevent interaction with preview */
                    background-color: #fff;
                }

                /* Responsive grid adjustments */
                @media (min-width: 1400px) {
                    .lumnav-mm-style-grid {
                        grid-template-columns: repeat(4, 1fr);
                    }
                }
            </style>
            <?php
        }
    }
}
add_action('wp_head', 'lumnav_mm_admin_and_settings_styles');
add_action('admin_head', 'lumnav_mm_admin_and_settings_styles');