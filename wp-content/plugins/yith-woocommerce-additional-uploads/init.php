<?php
/*
Plugin Name: YITH WooCommerce Uploads
Plugin URI: http://yithemes.com
Description: A concrete way to customize your orders, load a file with your images, and complete your order according to your needs.
Author: YITHEMES
Text Domain: yith-woocommerce-additional-uploads
Version: 1.2.0
Author URI: http://yithemes.com/
WC requires at least: 3.0.0
WC tested up to: 3.3.x
*/

if ( ! defined ( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! function_exists ( 'is_plugin_active' ) ) {
    require_once ( ABSPATH . 'wp-admin/includes/plugin.php' );
}

function yith_ywau_install_woocommerce_admin_notice () {
    ?>
    <div class="error">
        <p><?php _e ( 'YITH WooCommerce Uploads is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-additional-uploads' ); ?></p>
    </div>
    <?php
}

function yith_ywau_install_free_admin_notice () {
    ?>
    <div class="error">
        <p><?php _e ( 'You can\'t activate the free version of YITH WooCommerce Uploads while you are using the premium one.', 'yith-woocommerce-additional-uploads' ); ?></p>
    </div>
    <?php
}

if ( ! function_exists ( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook ( __FILE__, 'yith_plugin_registration_hook' );

//region    ****    Define constants

if ( ! defined ( 'YITH_YWAU_FREE_INIT' ) ) {
    define ( 'YITH_YWAU_FREE_INIT', plugin_basename ( __FILE__ ) );
}

if ( ! defined ( 'YITH_YWAU_VERSION' ) ) {
    define ( 'YITH_YWAU_VERSION', '1.2.0' );
}

if ( ! defined ( 'YITH_YWAU_FILE' ) ) {
    define ( 'YITH_YWAU_FILE', __FILE__ );
}

if ( ! defined ( 'YITH_YWAU_DIR' ) ) {
    define ( 'YITH_YWAU_DIR', plugin_dir_path ( __FILE__ ) );
}

if ( ! defined ( 'YITH_YWAU_URL' ) ) {
    define ( 'YITH_YWAU_URL', plugins_url ( '/', __FILE__ ) );
}

if ( ! defined ( 'YITH_YWAU_ASSETS_URL' ) ) {
    define ( 'YITH_YWAU_ASSETS_URL', YITH_YWAU_URL . 'assets' );
}

if ( ! defined ( 'YITH_YWAU_TEMPLATES_DIR' ) ) {
    define ( 'YITH_YWAU_TEMPLATES_DIR', YITH_YWAU_DIR . 'templates' );
}

if ( ! defined ( 'YITH_YWAU_ASSETS_IMAGES_URL' ) ) {
    define ( 'YITH_YWAU_ASSETS_IMAGES_URL', YITH_YWAU_ASSETS_URL . '/images/' );
}

$wp_upload_dir = wp_upload_dir ();

if ( ! defined ( 'YITH_YWAU_SAVE_DIR' ) ) {
    define ( 'YITH_YWAU_SAVE_DIR', $wp_upload_dir[ 'basedir' ] . '/yith-additional-uploads/' );
}

if ( ! defined ( 'YITH_YWAU_SAVE_URL' ) ) {
    define ( 'YITH_YWAU_SAVE_URL', $wp_upload_dir[ 'baseurl' ] . '/yith-additional-uploads/' );
}
//endregion

/* Plugin Framework Version Check */
if ( ! function_exists ( 'yit_maybe_plugin_fw_loader' ) && file_exists ( YITH_YWAU_DIR . 'plugin-fw/init.php' ) ) {
    require_once ( YITH_YWAU_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader ( YITH_YWAU_DIR );

function yith_ywau_init () {

    /**
     * Load text domain and start plugin
     */
    load_plugin_textdomain ( 'yith-woocommerce-additional-uploads', false, dirname ( plugin_basename ( __FILE__ ) ) . '/languages/' );

    require_once ( YITH_YWAU_DIR . 'lib/class.yith-woocommerce-additional-uploads.php' );
    require_once ( YITH_YWAU_DIR . 'lib/class.ywau-plugin-fw-loader.php' );
    require_once ( YITH_YWAU_DIR . 'functions.php' );

    YWAU_Plugin_FW_Loader::get_instance ();

    YITH_WooCommerce_Additional_Uploads::get_instance ();
}

add_action ( 'yith_ywau_init', 'yith_ywau_init' );


function yith_ywau_install () {

    if ( ! function_exists ( 'WC' ) ) {
        add_action ( 'admin_notices', 'yith_ywau_install_woocommerce_admin_notice' );
    } elseif ( defined ( 'YITH_YWAU_PREMIUM' ) ) {
        add_action ( 'admin_notices', 'yith_ywau_install_free_admin_notice' );
        deactivate_plugins ( plugin_basename ( __FILE__ ) );
    } else {
        do_action ( 'yith_ywau_init' );
    }
}

add_action ( 'plugins_loaded', 'yith_ywau_install', 11 );