<?php
/* @wordpress-plugin
 * Plugin Name:       WooCommerce New Zealand Post Shipping Method
 * Plugin URI:        https://wpruby.com/plugin/woocommerce-new-zealand-post-shipping-method-pro/
 * Description:       WooCommerce New Zealand Post Shipping Method.
 * Version:           1.1.6
 * WC requires at least: 2.6
 * WC tested up to: 3.5
 * Author:            WPRuby
 * Author URI:        https://wpruby.com
 * Text Domain:       woocommerce-new-zealand-post-shipping-method
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/wsenjer/new-zealand-post-woocommerce-shipping-method
 */

define('NZPOST_URL', plugin_dir_url(__FILE__));

if(nzpost_is_woocommerce_active()){


	add_filter('woocommerce_shipping_methods', 'add_new_zealand_post_post_method');
	function add_new_zealand_post_post_method( $methods ){
		$methods['nzpost'] = 'WC_New_Zealand_Post_Shipping_Method';
		return $methods;
	}

	add_action('woocommerce_shipping_init', 'init_zealand_post');
	function init_zealand_post( ){

		require 'class-newzealand-post.php';
	}

}

function nzpost_is_woocommerce_active(){
	$active_plugins = (array) get_option( 'active_plugins', array() );

	if ( is_multisite() )
		$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );

	return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
}
