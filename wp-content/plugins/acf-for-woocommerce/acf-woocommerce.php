<?php
/**
 * Plugin Name: ACF for Woocommerce
 * Plugin URI:  http://catsplugins.com
 * Description: A plugin to integrate ACF with WooCommerce
 * Version:     1.0.1
 * Author:      Cat's Plugins
 * Author URI:  http://catsplugins.com
 * License: GNU General Public License, version 3 (GPL-3.0)
 * License URI: http://www.gnu.org/copyleft/gpl.html
 * Text Domain: acf-woo
 * Domain Path: /languages
 */
function activate_acf_for_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-acf-woocommerce-activator.php';
	Acf_Woocommerce_Activator::activate();
}
register_activation_hook( __FILE__, 'activate_acf_for_woocommerce' );
require_once plugin_dir_path(__FILE__) . 'includes/core/class-acf-woo-singleton.php';
class ACF_Woo_Launcher extends ACF_Woo_Singleton {
    protected function __construct() {
        require_once $this->plugin_dir_path('includes/core/class-acf-woo-main.php');
    }

    public function plugin_dir_path($path) {
        return plugin_dir_path(__FILE__) . $path;
    }
    public function plugin_dir_url($path) {
        return plugin_dir_url(__FILE__) . $path;
    }
}
ACF_Woo_Launcher::get_instance();

