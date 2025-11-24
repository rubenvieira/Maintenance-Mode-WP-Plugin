<?php
/**
 * Plugin Name: Maintenance Mode
 * Description: A maintenance mode plugin with a simple on/off toggle in the admin bar.
 * Version: 1.0.0
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
            'title' => $is_on ? 'Maintenance Mode: ON' : 'Maintenance Mode: OFF',
            'href' => esc_url($toggle_url),
            'meta' => array(
                'class' => $is_on ? 'lumnav-mm-on' : 'lumnav-mm-off',
                'title' => $is_on ? 'Click to disable Maintenance Mode' : 'Click to enable Maintenance Mode',
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
        'Maintenance Mode Settings',
        'Maintenance Mode',
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
        <h1>Maintenance Mode Settings</h1>
        <p>Select the style for your maintenance page. Visitors will see this page when maintenance mode is ON.</p>
        <form method="post" action="options.php">
            <?php settings_fields('lumnav_mm_settings_group'); ?>
            <div class="lumnav-mm-style-grid">

                <!-- Simple Style -->
                <label class="lumnav-mm-style-card">
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="simple" <?php checked($current_style, 'simple'); ?> />
                    <div class="preview-container">
                        <div class="preview simple-preview">
                            <div class="simple-box">
                                <h3>We’ll be back soon!</h3>
                                <p>Performing some maintenance...</p>
                            </div>
                        </div>
                        <div class="card-title">Simple</div>
                    </div>
                </label>

                <!-- High-Tech Style -->
                <label class="lumnav-mm-style-card">
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="hightech" <?php checked($current_style, 'hightech'); ?> />
                    <div class="preview-container">
                        <div class="preview hightech-preview">
                            <div class="hightech-box">
                                <h3>SYSTEM MAINTENANCE</h3>
                                <p>&gt; Recalibrating...</p>
                            </div>
                        </div>
                        <div class="card-title">High-Tech</div>
                    </div>
                </label>

                <!-- Artistic Style -->
                <label class="lumnav-mm-style-card">
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="artistic" <?php checked($current_style, 'artistic'); ?> />
                    <div class="preview-container">
                        <div class="preview artistic-preview">
                            <div class="artistic-box">
                                <span><?php echo esc_html(preg_replace('/^www\./', '', wp_parse_url(home_url(), PHP_URL_HOST))); ?></span>
                            </div>
                        </div>
                        <div class="card-title">Artistic (Premium)</div>
                    </div>
                </label>

                <!-- Glassmorphic Gradient Mesh Style -->
                <label class="lumnav-mm-style-card">
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="glassmorphic" <?php checked($current_style, 'glassmorphic'); ?> />
                    <div class="preview-container">
                        <div class="preview glassmorphic-preview">
                            <div class="glass-card">✨ Premium</div>
                        </div>
                        <div class="card-title">Glassmorphic</div>
                    </div>
                </label>

                <!-- Neural Network Particles Style -->
                <label class="lumnav-mm-style-card">
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="neural" <?php checked($current_style, 'neural'); ?> />
                    <div class="preview-container">
                        <div class="preview neural-preview">
                            <div class="neural-dots">● ● ●</div>
                        </div>
                        <div class="card-title">Neural Network</div>
                    </div>
                </label>

                <!-- Liquid Morphing Blobs Style -->
                <label class="lumnav-mm-style-card">
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="liquid" <?php checked($current_style, 'liquid'); ?> />
                    <div class="preview-container">
                        <div class="preview liquid-preview">
                            <div class="liquid-blob"></div>
                        </div>
                        <div class="card-title">Liquid Blobs</div>
                    </div>
                </label>

                <!-- 3D Geometric Torus Style -->
                <label class="lumnav-mm-style-card">
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="torus" <?php checked($current_style, 'torus'); ?> />
                    <div class="preview-container">
                        <div class="preview torus-preview">
                            <div class="torus-icon">◯</div>
                        </div>
                        <div class="card-title">3D Torus</div>
                    </div>
                </label>

                <!-- Kinetic Typography Style -->
                <label class="lumnav-mm-style-card">
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="kinetic" <?php checked($current_style, 'kinetic'); ?> />
                    <div class="preview-container">
                        <div class="preview kinetic-preview">
                            <div class="kinetic-text">BOLD</div>
                        </div>
                        <div class="card-title">Kinetic Type</div>
                    </div>
                </label>

                <!-- Aurora Borealis Style -->
                <label class="lumnav-mm-style-card">
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="aurora" <?php checked($current_style, 'aurora'); ?> />
                    <div class="preview-container">
                        <div class="preview aurora-preview">
                            <div class="aurora-waves">~</div>
                        </div>
                        <div class="card-title">Aurora</div>
                    </div>
                </label>

                <!-- Holographic Interface Style -->
                <label class="lumnav-mm-style-card">
                    <input type="radio" name="<?php echo esc_attr(LUMNAV_MM_STYLE_OPTION); ?>" value="holographic" <?php checked($current_style, 'holographic'); ?> />
                    <div class="preview-container">
                        <div class="preview holographic-preview">
                            <div class="holo-text">HOLO</div>
                        </div>
                        <div class="card-title">Holographic</div>
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
    $status = get_option(LUMNAV_MM_OPTION, 'off');

    if ($status !== 'on' || current_user_can('manage_options') || in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'), true)) {
        return;
    }

    header('HTTP/1.1 503 Service Temporarily Unavailable');
    header('Content-Type: text/html; charset=utf-8');
    header('Retry-After: 3600');

    $style = get_option(LUMNAV_MM_STYLE_OPTION, 'simple');

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
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Under Maintenance</title>
        <style>
            body {
                text-align: center;
                padding: 150px;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                background-color: #f1f1f1;
                color: #444
            }

            h1 {
                font-size: 50px
            }

            p {
                font-size: 18px
            }

            article {
                display: block;
                text-align: left;
                max-width: 650px;
                margin: 0 auto
            }
        </style>
    </head>

    <body>
        <article>
            <h1>We&rsquo;ll be back soon!</h1>
            <div>
                <p>Sorry for the inconvenience but we&rsquo;re performing some maintenance at the moment. We&rsquo;ll be
                    back online shortly!</p>
                <p>&mdash; The Team</p>
            </div>
        </article>
    </body>

    </html>
    <?php
}

/**
 * Renders the high-tech maintenance page.
 */
function lumnav_mm_render_hightech_page()
{
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>System Upgrade in Progress</title>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap');

            body {
                background-color: #0a0a0a;
                color: #00ff00;
                font-family: 'Orbitron', sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                overflow: hidden;
                text-align: center;
                text-shadow: 0 0 5px #00ff00, 0 0 10px #00ff00
            }

            .container {
                position: relative;
                z-index: 2;
                padding: 20px;
                border: 2px solid #00ff00;
                box-shadow: 0 0 20px #00ff00;
                background-color: rgba(0, 20, 0, .5);
                animation: flicker 3s infinite alternate
            }

            h1 {
                font-size: 3rem;
                margin-bottom: 1rem;
                text-transform: uppercase;
                letter-spacing: 4px;
                animation: glitch 1s linear infinite
            }

            p {
                font-size: 1.2rem;
                margin: 0;
                letter-spacing: 2px
            }

            .scanline {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0, 255, 0, .1) 3px, rgba(0, 255, 0, .1) 4px);
                animation: scan 7s linear infinite;
                pointer-events: none
            }

            @keyframes flicker {

                0%,
                100% {
                    opacity: 1
                }

                50% {
                    opacity: .9
                }
            }

            @keyframes scan {
                0% {
                    transform: translateY(-10%)
                }

                100% {
                    transform: translateY(10%)
                }
            }

            @keyframes glitch {

                2%,
                64% {
                    transform: translate(2px, 0) skew(0)
                }

                4%,
                60% {
                    transform: translate(-2px, 0) skew(0)
                }

                62% {
                    transform: translate(0, 0) skew(5deg)
                }
            }
        </style>
    </head>

    <body>
        <div class="scanline"></div>
        <div class="container">
            <h1>System Maintenance</h1>
            <p>Recalibrating... We will be back online shortly.</p>
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
        <title>Coming Back Soon</title>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@900&display=swap');

            body {
                background-color: #000;
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
                height: 100vh
            }

            .container {
                perspective: 1000px
            }

            .domain-text {
                font-size: clamp(2rem, 10vw, 8rem);
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: 2px;
                background: linear-gradient(90deg, #ff8a00, #e52e71, #9d4edd);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                text-fill-color: transparent;
                animation: rotate 15s linear infinite, glow 2s infinite alternate
            }

            p {
                font-size: 1.2rem;
                margin-top: 1rem;
                color: #ccc;
                font-family: sans-serif;
                font-weight: 300
            }

            @keyframes rotate {
                0% {
                    transform: rotateY(-15deg) rotateX(5deg)
                }

                50% {
                    transform: rotateY(15deg) rotateX(-5deg)
                }

                100% {
                    transform: rotateY(-15deg) rotateX(5deg)
                }
            }

            @keyframes glow {
                0% {
                    text-shadow: 0 0 10px #e52e71, 0 0 20px #e52e71, 0 0 30px #e52e71
                }

                100% {
                    text-shadow: 0 0 20px #9d4edd, 0 0 30px #9d4edd, 0 0 40px #9d4edd
                }
            }
        </style>
    </head>

    <body><canvas id="bg-canvas"></canvas>
        <div class="content-wrapper">
            <div class="container">
                <h1 class="domain-text"><?php echo esc_html($domain); ?></h1>
            </div>
            <p>We are currently down for maintenance. Please check back soon.</p>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
        <script>let scene, camera, renderer, stars, starGeo; let mouseX = 0, mouseY = 0; function init() { scene = new THREE.Scene(); camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 1, 1000); camera.position.z = 1; camera.rotation.x = Math.PI / 2; renderer = new THREE.WebGLRenderer({ canvas: document.getElementById("bg-canvas"), alpha: true }); renderer.setSize(window.innerWidth, window.innerHeight); starGeo = new THREE.BufferGeometry(); const starVertices = []; for (let i = 0; i < 6000; i++) { const x = (Math.random() - .5) * 2000; const y = (Math.random() - .5) * 2000; const z = Math.random() * 2000 - 1000; starVertices.push(x, y, z) } starGeo.setAttribute('position', new THREE.Float32BufferAttribute(starVertices, 3)); let sprite = new THREE.TextureLoader().load('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAB9SURBVDjLY/j//z8DJRgxQAFjRgwYgKWBAQsgKWMlBwYGAwY2MMZkYGBgYGBgYGBgYGBgYGBg/P//PwYGBgY2ENAwbGDgAIIxQzQyMDAwMNz//x9KjPz//z8DGNQvA0M9+P//P5zj//9/GM4wYgACDAAfoQ/x/5k/XwAAAABJRU5ErkJggg=='); let starMaterial = new THREE.PointsMaterial({ color: 16777215, size: .7, map: sprite, transparent: true }); stars = new THREE.Points(starGeo, starMaterial); scene.add(stars); window.addEventListener("resize", onWindowResize, !1); document.addEventListener('mousemove', onMouseMove, !1); animate() } function onWindowResize() { camera.aspect = window.innerWidth / window.innerHeight; camera.updateProjectionMatrix(); renderer.setSize(window.innerWidth, window.innerHeight) } function onMouseMove(event) { mouseX = event.clientX - window.innerWidth / 2; mouseY = event.clientY - window.innerHeight / 2 } function animate() { starGeo.attributes.position.needsUpdate = !0; stars.rotation.y += 5e-5; stars.rotation.x += 2e-5; if (mouseX !== 0 || mouseY !== 0) { camera.position.x += (-mouseX - camera.position.x) * .00005; camera.position.y += (mouseY - camera.position.y) * .00005; camera.lookAt(scene.position) } renderer.render(scene, camera); requestAnimationFrame(animate) } init();</script>
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
        <title>Under Maintenance</title>
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
                background: rgba(255, 255, 255, .1);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, .2);
                border-radius: 24px;
                padding: 60px 80px;
                max-width: 600px;
                width: 90%;
                box-shadow: 0 8px 32px rgba(0, 0, 0, .3), inset 0 1px 0 rgba(255, 255, 255, .2);
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
            <h1>We'll be back soon!</h1>
            <p>Our site is currently undergoing scheduled maintenance. We're working hard to improve your experience and
                will be back online shortly.</p>
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
        <title>System Maintenance</title>
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
                style="font-size:1.2rem;font-weight:600;margin-bottom:10px;color:rgba(102,126,234,0.8);text-transform:lowercase;letter-spacing:2px;">
                <?php echo esc_html($domain); ?>
            </div>
            <h1>Neural Network Recalibration</h1>
            <p>Our systems are currently optimizing neural pathways. All services will be restored momentarily.</p>
            <div class="status-indicator"><span class="status-dot"></span>Processing</div>
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
        <title>Be Right Back</title>
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
                padding: 20px;
                max-width: 700px
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
                style="font-size:1.1rem;font-weight:700;margin-bottom:12px;background:linear-gradient(135deg,#667eea,#f093fb);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;text-transform:lowercase;letter-spacing:1.5px;">
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
        <title>Maintenance in Progress</title>
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
                z-index: 1
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
                font-size: clamp(2rem, 5vw, 3.5rem);
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
                style="font-size:1rem;font-weight:500;margin-bottom:8px;color:rgba(168,237,234,0.9);text-transform:lowercase;letter-spacing:3px;">
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
        <title>Coming Back Soon</title>
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
                font-size: clamp(4rem, 15vw, 12rem);
                line-height: .9;
                margin-bottom: 30px;
                overflow: hidden
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
                style="font-size:1.3rem;font-weight:700;margin-bottom:20px;color:rgba(255,255,255,0.7);letter-spacing:5px;text-transform:uppercase;">
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
        <title>Maintenance Mode</title>
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
                style="font-size:1.1rem;font-weight:500;margin-bottom:15px;color:rgba(100,255,200,0.8);text-shadow:0 0 10px rgba(100,255,200,0.3);text-transform:lowercase;letter-spacing:2px;">
                <?php echo esc_html($domain); ?>
            </div>
            <h1>Serene Maintenance</h1>
            <p>Like the aurora borealis dancing across the sky, we're crafting something beautiful. Returning soon.</p>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
        <script>let scene, camera, renderer, aurora; function init() {
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
        <title>System Update</title>
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
                z-index: 1
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
                z-index: 3
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
                font-size: clamp(2.5rem, 7vw, 5rem);
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
                style="font-size:1.2rem;font-weight:700;margin-bottom:20px;color:#0ff;text-shadow:0 0 5px #0ff;letter-spacing:5px;text-transform:uppercase;">
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

            .lumnav-mm-style-card .preview {
                height: 150px;
                display: flex;
                justify-content: center;
                align-items: center;
                font-family: sans-serif;
                padding: 15px;
            }

            .lumnav-mm-style-card .card-title {
                background-color: #f0f0f1;
                padding: 10px 15px;
                font-weight: 600;
                border-top: 1px solid #ddd;
            }

            /* Simple Preview */
            .simple-preview {
                background-color: #f1f1f1;
                color: #444;
            }

            .simple-preview .simple-box {
                text-align: center;
            }

            .simple-preview h3 {
                font-size: 1.2em;
                margin: 0 0 5px;
            }

            .simple-preview p {
                font-size: 0.9em;
                margin: 0;
            }

            /* High-Tech Preview */
            .hightech-preview {
                background-color: #0a0a0a;
                color: #00ff00;
                font-family: 'Courier New', Courier, monospace;
                border: 1px solid #00ff00;
            }

            .hightech-preview .hightech-box {
                text-align: left;
            }

            .hightech-preview h3 {
                font-size: 1em;
                margin: 0 0 5px;
            }

            .hightech-preview p {
                font-size: 0.9em;
                margin: 0;
            }

            /* Artistic Preview */
            .artistic-preview {
                background-color: #1a1a1a;
            }

            .artistic-preview .artistic-box {
                font-size: 1.5em;
                font-weight: bold;
                background: linear-gradient(90deg, #ff8a00, #e52e71);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                text-fill-color: transparent;
            }

            /* Glassmorphic Preview */
            .glassmorphic-preview {
                background: linear-gradient(135deg, #667eea, #764ba2, #f093fb);
                background-size: 200% 200%;
                animation: gradientShift 3s ease infinite;
            }

            .glassmorphic-preview .glass-card {
                background: rgba(255, 255, 255, 0.2);
                backdrop-filter: blur(10px);
                padding: 15px 25px;
                border-radius: 15px;
                border: 1px solid rgba(255, 255, 255, 0.3);
                color: #fff;
                font-weight: 600;
            }

            /* Neural Preview */
            .neural-preview {
                background: #0a0e27;
                color: #667eea;
            }

            .neural-preview .neural-dots {
                font-size: 2em;
                letter-spacing: 10px;
                animation: pulse 2s ease-in-out infinite;
            }

            /* Liquid Preview */
            .liquid-preview {
                background: #0f0817;
                overflow: hidden;
                position: relative;
            }

            .liquid-preview .liquid-blob {
                width: 100px;
                height: 100px;
                background: linear-gradient(45deg, #667eea, #f093fb);
                border-radius: 40% 60% 70% 30%/40% 50% 60% 50%;
                filter: blur(20px);
                opacity: 0.6;
                animation: morph 5s ease-in-out infinite;
            }

            /* Torus Preview */
            .torus-preview {
                background: #000;
                color: #a8edea;
            }

            .torus-preview .torus-icon {
                font-size: 4em;
                animation: spin 3s linear infinite;
            }

            /* Kinetic Preview */
            .kinetic-preview {
                background: #1a1a1a;
                color: #fff;
            }

            .kinetic-preview .kinetic-text {
                font-size: 2.5em;
                font-weight: 900;
                letter-spacing: 5px;
                font-family: 'Arial Black', sans-serif;
            }

            /* Aurora Preview */
            .aurora-preview {
                background: linear-gradient(180deg, #000a1f 0%, #001f3f 100%);
                position: relative;
                overflow: hidden;
            }

            .aurora-preview .aurora-waves {
                font-size: 3em;
                color: #00ff88;
                text-shadow: 0 0 20px #00ff88;
                animation: wave 2s ease-in-out infinite;
            }

            /* Holographic Preview */
            .holographic-preview {
                background: #000;
                background-image: repeating-linear-gradient(0deg, transparent, transparent 20px, rgba(0, 255, 255, 0.03) 20px, rgba(0, 255, 255, 0.03) 21px);
            }

            .holographic-preview .holo-text {
                font-size: 2em;
                font-weight: 900;
                color: #0ff;
                text-shadow: 0 0 10px #0ff, 0 0 20px #0ff;
                letter-spacing: 5px;
                font-family: 'Courier New', monospace;
            }

            @keyframes gradientShift {
                0% {
                    background-position: 0% 50%;
                }

                50% {
                    background-position: 100% 50%;
                }

                100% {
                    background-position: 0% 50%;
                }
            }

            @keyframes pulse {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.5;
                }
            }

            @keyframes morph {

                0%,
                100% {
                    border-radius: 40% 60% 70% 30%/40% 50% 60% 50%;
                }

                50% {
                    border-radius: 70% 30% 40% 60%/50% 60% 40% 50%;
                }
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }

            @keyframes wave {

                0%,
                100% {
                    transform: translateY(0);
                }

                50% {
                    transform: translateY(-10px);
                }
            }

            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-fill-color: transparent;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'lumnav_mm_admin_and_settings_styles');
add_action('admin_head', 'lumnav_mm_admin_and_settings_styles');