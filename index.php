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
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LUMNAV_MM_OPTION', 'lumnav_maintenance_mode_status' );
define( 'LUMNAV_MM_STYLE_OPTION', 'lumnav_maintenance_mode_style' );

/**
 * Adds the maintenance mode toggle to the WordPress admin bar.
 *
 * @param WP_Admin_Bar $wp_admin_bar The admin bar object.
 */
function lumnav_mm_admin_bar_item( $wp_admin_bar ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $status = get_option( LUMNAV_MM_OPTION, 'off' );
    $is_on  = ( $status === 'on' );

    $toggle_url = add_query_arg(
        array(
            'action'   => 'lumnav_toggle_mm',
            '_wpnonce' => wp_create_nonce( 'lumnav_toggle_mm_nonce' ),
        ),
        admin_url( 'index.php' )
    );

    $wp_admin_bar->add_node(
        array(
            'id'    => 'lumnav-maintenance-mode',
            'title' => $is_on ? 'Maintenance Mode: ON' : 'Maintenance Mode: OFF',
            'href'  => esc_url( $toggle_url ),
            'meta'  => array(
                'class' => $is_on ? 'lumnav-mm-on' : 'lumnav-mm-off',
                'title' => $is_on ? 'Click to disable Maintenance Mode' : 'Click to enable Maintenance Mode',
            ),
        )
    );
}
add_action( 'admin_bar_menu', 'lumnav_mm_admin_bar_item', 100 );

/**
 * Handles the logic for toggling the maintenance mode status.
 */
function lumnav_mm_toggle_status() {
    if (
        isset( $_GET['action'] ) &&
        $_GET['action'] === 'lumnav_toggle_mm' &&
        isset( $_GET['_wpnonce'] ) &&
        wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'lumnav_toggle_mm_nonce' ) &&
        current_user_can( 'manage_options' )
    ) {
        $status     = get_option( LUMNAV_MM_OPTION, 'off' );
        $new_status = ( $status === 'on' ) ? 'off' : 'on';
        update_option( LUMNAV_MM_OPTION, $new_status );

        wp_safe_redirect( remove_query_arg( array( 'action', '_wpnonce' ), wp_get_referer() ?: admin_url() ) );
        exit;
    }
}
add_action( 'admin_init', 'lumnav_mm_toggle_status' );

/**
 * Adds a settings page for the plugin.
 */
function lumnav_mm_add_settings_page() {
    add_options_page(
        'Maintenance Mode Settings',
        'Maintenance Mode',
        'manage_options',
        'lumnav-maintenance-mode',
        'lumnav_mm_render_settings_page'
    );
}
add_action( 'admin_menu', 'lumnav_mm_add_settings_page' );

/**
 * Registers the plugin's settings.
 */
function lumnav_mm_register_settings() {
    register_setting( 'lumnav_mm_settings_group', LUMNAV_MM_STYLE_OPTION );
}
add_action( 'admin_init', 'lumnav_mm_register_settings' );

/**
 * Renders the settings page HTML with style previews.
 */
function lumnav_mm_render_settings_page() {
    $current_style = get_option( LUMNAV_MM_STYLE_OPTION, 'simple' );
    ?>
    <div class="wrap">
        <h1>Maintenance Mode Settings</h1>
        <p>Select the style for your maintenance page. Visitors will see this page when maintenance mode is ON.</p>
        <form method="post" action="options.php">
            <?php settings_fields( 'lumnav_mm_settings_group' ); ?>
            <div class="lumnav-mm-style-grid">

                <!-- Simple Style -->
                <label class="lumnav-mm-style-card">
                    <input type="radio" name="<?php echo esc_attr( LUMNAV_MM_STYLE_OPTION ); ?>" value="simple" <?php checked( $current_style, 'simple' ); ?> />
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
                    <input type="radio" name="<?php echo esc_attr( LUMNAV_MM_STYLE_OPTION ); ?>" value="hightech" <?php checked( $current_style, 'hightech' ); ?> />
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
                    <input type="radio" name="<?php echo esc_attr( LUMNAV_MM_STYLE_OPTION ); ?>" value="artistic" <?php checked( $current_style, 'artistic' ); ?> />
                    <div class="preview-container">
                        <div class="preview artistic-preview">
                            <div class="artistic-box">
                                <span><?php echo esc_html( preg_replace( '/^www\./', '', wp_parse_url( home_url(), PHP_URL_HOST ) ) ); ?></span>
                            </div>
                        </div>
                        <div class="card-title">Artistic (Premium)</div>
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
function lumnav_mm_check_status() {
    $status = get_option( LUMNAV_MM_OPTION, 'off' );

    if ( $status !== 'on' || current_user_can( 'manage_options' ) || in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ), true ) ) {
        return;
    }

    header( 'HTTP/1.1 503 Service Temporarily Unavailable' );
    header( 'Content-Type: text/html; charset=utf-8' );
    header( 'Retry-After: 3600' );

    $style = get_option( LUMNAV_MM_STYLE_OPTION, 'simple' );

    if ( $style === 'hightech' ) {
        lumnav_mm_render_hightech_page();
    } elseif ( $style === 'artistic' ) {
        lumnav_mm_render_artistic_page();
    } else {
        lumnav_mm_render_simple_page();
    }

    exit;
}
add_action( 'template_redirect', 'lumnav_mm_check_status' );

/**
 * Renders the simple maintenance page.
 */
function lumnav_mm_render_simple_page() {
    ?>
    <!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Under Maintenance</title><style>body{text-align:center;padding:150px;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;background-color:#f1f1f1;color:#444}h1{font-size:50px}p{font-size:18px}article{display:block;text-align:left;max-width:650px;margin:0 auto}</style></head><body><article><h1>We&rsquo;ll be back soon!</h1><div><p>Sorry for the inconvenience but we&rsquo;re performing some maintenance at the moment. We&rsquo;ll be back online shortly!</p><p>&mdash; The Team</p></div></article></body></html>
    <?php
}

/**
 * Renders the high-tech maintenance page.
 */
function lumnav_mm_render_hightech_page() {
    ?>
    <!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>System Upgrade in Progress</title><style>@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap');body{background-color:#0a0a0a;color:#00ff00;font-family:'Orbitron',sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;overflow:hidden;text-align:center;text-shadow:0 0 5px #00ff00,0 0 10px #00ff00}.container{position:relative;z-index:2;padding:20px;border:2px solid #00ff00;box-shadow:0 0 20px #00ff00;background-color:rgba(0,20,0,.5);animation:flicker 3s infinite alternate}h1{font-size:3rem;margin-bottom:1rem;text-transform:uppercase;letter-spacing:4px;animation:glitch 1s linear infinite}p{font-size:1.2rem;margin:0;letter-spacing:2px}.scanline{position:fixed;top:0;left:0;width:100%;height:100%;background:repeating-linear-gradient(0deg,transparent,transparent 2px,rgba(0,255,0,.1) 3px,rgba(0,255,0,.1) 4px);animation:scan 7s linear infinite;pointer-events:none}@keyframes flicker{0%,100%{opacity:1}50%{opacity:.9}}@keyframes scan{0%{transform:translateY(-10%)}100%{transform:translateY(10%)}}@keyframes glitch{2%,64%{transform:translate(2px,0) skew(0)}4%,60%{transform:translate(-2px,0) skew(0)}62%{transform:translate(0,0) skew(5deg)}}</style></head><body><div class="scanline"></div><div class="container"><h1>System Maintenance</h1><p>Recalibrating... We will be back online shortly.</p></div></body></html>
    <?php
}

/**
 * Renders the artistic maintenance page with a 3D background.
 */
function lumnav_mm_render_artistic_page() {
    $domain = preg_replace( '/^www\./', '', wp_parse_url( home_url(), PHP_URL_HOST ) );
    ?>
    <!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Coming Back Soon</title><style>@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@900&display=swap');body{background-color:#000;color:#fff;font-family:'Poppins',sans-serif;margin:0;overflow:hidden;text-align:center}#bg-canvas{position:fixed;top:0;left:0;width:100%;height:100%;z-index:1}.content-wrapper{position:relative;z-index:2;display:flex;flex-direction:column;justify-content:center;align-items:center;height:100vh}.container{perspective:1000px}.domain-text{font-size:clamp(2rem,10vw,8rem);font-weight:900;text-transform:uppercase;letter-spacing:2px;background:linear-gradient(90deg,#ff8a00,#e52e71,#9d4edd);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;text-fill-color:transparent;animation:rotate 15s linear infinite,glow 2s infinite alternate}p{font-size:1.2rem;margin-top:1rem;color:#ccc;font-family:sans-serif;font-weight:300}@keyframes rotate{0%{transform:rotateY(-15deg) rotateX(5deg)}50%{transform:rotateY(15deg) rotateX(-5deg)}100%{transform:rotateY(-15deg) rotateX(5deg)}}@keyframes glow{0%{text-shadow:0 0 10px #e52e71,0 0 20px #e52e71,0 0 30px #e52e71}100%{text-shadow:0 0 20px #9d4edd,0 0 30px #9d4edd,0 0 40px #9d4edd}}</style></head><body><canvas id="bg-canvas"></canvas><div class="content-wrapper"><div class="container"><h1 class="domain-text"><?php echo esc_html( $domain ); ?></h1></div><p>We are currently down for maintenance. Please check back soon.</p></div><script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script><script>let scene,camera,renderer,stars,starGeo;let mouseX=0,mouseY=0;function init(){scene=new THREE.Scene();camera=new THREE.PerspectiveCamera(60,window.innerWidth/window.innerHeight,1,1000);camera.position.z=1;camera.rotation.x=Math.PI/2;renderer=new THREE.WebGLRenderer({canvas:document.getElementById("bg-canvas"),alpha:true});renderer.setSize(window.innerWidth,window.innerHeight);starGeo=new THREE.BufferGeometry();const starVertices=[];for(let i=0;i<6000;i++){const x=(Math.random()-.5)*2000;const y=(Math.random()-.5)*2000;const z=Math.random()*2000-1000;starVertices.push(x,y,z)}starGeo.setAttribute('position',new THREE.Float32BufferAttribute(starVertices,3));let sprite=new THREE.TextureLoader().load('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAB9SURBVDjLY/j//z8DJRgxQAFjRgwYgKWBAQsgKWMlBwYGAwY2MMZkYGBgYGBgYGBgYGBgYGBg/P//PwYGBgY2ENAwbGDgAIIxQzQyMDAwMNz//x9KjPz//z8DGNQvA0M9+P//P5zj//9/GM4wYgACDAAfoQ/x/5k/XwAAAABJRU5ErkJggg==');let starMaterial=new THREE.PointsMaterial({color:16777215,size:.7,map:sprite,transparent:true});stars=new THREE.Points(starGeo,starMaterial);scene.add(stars);window.addEventListener("resize",onWindowResize,!1);document.addEventListener('mousemove',onMouseMove,!1);animate()}function onWindowResize(){camera.aspect=window.innerWidth/window.innerHeight;camera.updateProjectionMatrix();renderer.setSize(window.innerWidth,window.innerHeight)}function onMouseMove(event){mouseX=event.clientX-window.innerWidth/2;mouseY=event.clientY-window.innerHeight/2}function animate(){starGeo.attributes.position.needsUpdate=!0;stars.rotation.y+=5e-5;stars.rotation.x+=2e-5;if(mouseX!==0||mouseY!==0){camera.position.x+=(-mouseX-camera.position.x)*.00005;camera.position.y+=(mouseY-camera.position.y)*.00005;camera.lookAt(scene.position)}renderer.render(scene,camera);requestAnimationFrame(animate)}init();</script></body></html>
    <?php
}

/**
 * Adds styling for the admin bar button and settings page.
 */
function lumnav_mm_admin_and_settings_styles() {
    if ( ! is_user_logged_in() ) {
        return;
    }
    ?>
    <style>
        #wp-admin-bar-lumnav-maintenance-mode.lumnav-mm-on > .ab-item { background-color: #D54E21 !important; }
        #wp-admin-bar-lumnav-maintenance-mode.lumnav-mm-off > .ab-item { background-color: #46B450 !important; }
    </style>
    <?php
    $screen = get_current_screen();
    if ( $screen && $screen->id === 'settings_page_lumnav-maintenance-mode' ) {
        ?>
    <style>
        .lumnav-mm-style-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-top: 20px; }
        .lumnav-mm-style-card { position: relative; display: block; cursor: pointer; }
        .lumnav-mm-style-card input[type="radio"] { position: absolute; opacity: 0; width: 1px; height: 1px; }
        .lumnav-mm-style-card .preview-container { border: 2px solid #ddd; border-radius: 8px; overflow: hidden; transition: all 0.2s ease-in-out; }
        .lumnav-mm-style-card input[type="radio"]:checked + .preview-container { border-color: #2271b1; box-shadow: 0 0 0 1px #2271b1; }
        .lumnav-mm-style-card .preview { height: 150px; display: flex; justify-content: center; align-items: center; font-family: sans-serif; padding: 15px; }
        .lumnav-mm-style-card .card-title { background-color: #f0f0f1; padding: 10px 15px; font-weight: 600; border-top: 1px solid #ddd; }
        /* Simple Preview */
        .simple-preview { background-color: #f1f1f1; color: #444; }
        .simple-preview .simple-box { text-align: center; }
        .simple-preview h3 { font-size: 1.2em; margin: 0 0 5px; }
        .simple-preview p { font-size: 0.9em; margin: 0; }
        /* High-Tech Preview */
        .hightech-preview { background-color: #0a0a0a; color: #00ff00; font-family: 'Courier New', Courier, monospace; border: 1px solid #00ff00; }
        .hightech-preview .hightech-box { text-align: left; }
        .hightech-preview h3 { font-size: 1em; margin: 0 0 5px; }
        .hightech-preview p { font-size: 0.9em; margin: 0; }
        /* Artistic Preview */
        .artistic-preview { background-color: #1a1a1a; }
        .artistic-preview .artistic-box { font-size: 1.5em; font-weight: bold; background: linear-gradient(90deg, #ff8a00, #e52e71); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-fill-color: transparent; }
    </style>
        <?php
    }
}
add_action( 'wp_head', 'lumnav_mm_admin_and_settings_styles' );
add_action( 'admin_head', 'lumnav_mm_admin_and_settings_styles' );