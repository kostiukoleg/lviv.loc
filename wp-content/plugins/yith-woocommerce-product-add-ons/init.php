<?php
/**
 * Plugin Name: YITH WooCommerce Product Add-Ons
 * Description: YITH WooCommerce Product Add-Ons
 * Version: 1.1.1
 * Author: YITHEMES
 * Author URI: http://yithemes.com/
 * Text Domain: yith-woocommerce-product-add-ons
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages/
 * WC requires at least: 2.6.0
 * WC tested up to: 3.2.0
 *
 * @author  YITHEMES
 * @package YITH WooCommerce Product Add-Ons
 * @version 1.0.6
 */
/*  Copyright 2016  Your Inspiration Themes  (email : plugins@yithemes.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! function_exists( 'yith_wapo_install_free_woocommerce_admin_notice' ) ) {
    /**
     * Print an admin notice if woocommerce is deactivated
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     * @since  1.0
     * @return void
     * @use admin_notices hooks
     */
    function yith_wapo_install_free_woocommerce_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'YITH WooCommerce Product Add-Ons is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-product-add-ons' ); ?></p>
        </div>
    <?php
    }
}

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

! defined( 'YITH_WAPO_DIR' ) && define( 'YITH_WAPO_DIR', plugin_dir_path( __FILE__ ) );

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WAPO_DIR . 'plugin-fw/init.php' ) ) {
    require_once( YITH_WAPO_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WAPO_DIR );

// This version can't be activate if premium version is active  ________________________________________
if ( defined( 'YITH_WAPO_PREMIUM' ) ) {
    function yith_wapo_install_free_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'You can\'t activate the free version of YITH Woocommerce Product Add-Ons while you are using the premium one.', 'yith-woocommerce-product-add-ons' ); ?></p>
        </div>
        <?php
    }

    add_action( 'admin_notices', 'yith_wapo_install_free_admin_notice' );

    deactivate_plugins( plugin_basename( __FILE__ ) );
    return;
}

/* Advanced Option Constant */
! defined( 'YITH_WAPO' ) && define( 'YITH_WAPO', true );
! defined( 'YITH_WAPO_URL' ) && define( 'YITH_WAPO_URL', plugin_dir_url( __FILE__ ) );
! defined( 'YITH_WAPO_TEMPLATE_PATH' ) && define( 'YITH_WAPO_TEMPLATE_PATH', YITH_WAPO_DIR . 'templates' );
! defined( 'YITH_WAPO_TEMPLATE_ADMIN_PATH' ) && define( 'YITH_WAPO_TEMPLATE_ADMIN_PATH', YITH_WAPO_TEMPLATE_PATH . '/yith_wapo/admin/' );
! defined( 'YITH_WAPO_TEMPLATE_FRONTEND_PATH' ) && define( 'YITH_WAPO_TEMPLATE_FRONTEND_PATH', YITH_WAPO_TEMPLATE_PATH . '/yith_wapo/frontend/' );
! defined( 'YITH_WAPO_ASSETS_URL' ) && define( 'YITH_WAPO_ASSETS_URL', YITH_WAPO_URL . 'assets' );
! defined( 'YITH_WAPO_VERSION' ) && define( 'YITH_WAPO_VERSION', '1.1.1' );
! defined( 'YITH_WAPO_DB_VERSION' ) && define( 'YITH_WAPO_DB_VERSION', '1.0.1' );
! defined( 'YITH_WAPO_FILE' ) && define( 'YITH_WAPO_FILE', __FILE__ );
! defined( 'YITH_WAPO_SLUG' ) && define( 'YITH_WAPO_SLUG', 'yith-woocommerce-advanced-product-options' );
! defined( 'YITH_WAPO_LOCALIZE_SLUG' ) && define( 'YITH_WAPO_LOCALIZE_SLUG', 'yith-woocommerce-product-add-ons' );
! defined( 'YITH_WAPO_SECRET_KEY' ) && define( 'YITH_WAPO_SECRET_KEY', 'yCVBJvwjwXe2Z9vlqoWo' );
! defined( 'YITH_WAPO_INIT' ) && define( 'YITH_WAPO_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_WAPO_FREE_INIT' ) && define( 'YITH_WAPO_FREE_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_WAPO_WPML_CONTEXT' ) && define( 'YITH_WAPO_WPML_CONTEXT', 'YITH WooCommerce Product Add-Ons' );

if ( ! function_exists( 'YITH_WAPO' ) ) {
    /**
     * Unique access to instance of YITH_Vendors class
     *
     * @return YITH_WAPO
     * @since 1.0.0
     */
    function YITH_WAPO() {
        // Load required classes and functions
        require_once( YITH_WAPO_DIR . 'includes/class.yith-wapo.php' );

        return YITH_WAPO::instance();
    }
}

/**
 * Require core files
 *
 * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
 * @since  1.0
 * @return void
 * @use Load plugin core
 */
function yith_wapo_free_init() {

    load_plugin_textdomain( YITH_WAPO_LOCALIZE_SLUG, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    YITH_WAPO();

}

add_action( 'yith_wapo_free_init', 'yith_wapo_free_init' );

function yith_wapo_free_install() {

    require_once( 'includes/class.yith-wapo-group.php' );
    require_once( 'includes/class.yith-wapo-settings.php' );
    require_once( 'includes/class.yith-wapo-type.php' );
    require_once( 'includes/class.yith-wapo-option.php' );

    if ( ! function_exists( 'WC' ) ) {
        add_action( 'admin_notices', 'yith_wapo_install_free_woocommerce_admin_notice' );
    }
    else {
        do_action( 'yith_wapo_free_init' );
    }

}

add_action( 'plugins_loaded', 'yith_wapo_free_install', 12 );
