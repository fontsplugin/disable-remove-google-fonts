<?php
/**
 * Plugin Name: Disable & Remove Google Fonts
 * Plugin URI: https://wordpress.org/plugins/disable-remove-google-fonts/
 * Description: Optimize frontend performance by removing Google Fonts. GDPR-friendly.
 * Author: Fonts Plugin
 * Author URI: https://fontsplugin.com
 * Version: 1.5.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 *
 * @package disable-remove-google-fonts
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ABSPATH . 'wp-admin/includes/plugin.php';

/**
 * Exit early if Fonts Plugin Pro is active.
 */
if ( is_plugin_active( 'fonts-plugin-pro/fonts-plugin-pro.php' ) ) {
	return;
}

if ( ! defined( 'DRGF_PLUGIN_FILE' ) ) {
	define( 'DRGF_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'DRGF_VERSION' ) ) {
	define( 'DRGF_VERSION', '1.5.4' );
}

if ( ! defined( 'DRGF_DIR_PATH' ) ) {
	define( 'DRGF_DIR_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'DRGF_DIR_URL' ) ) {
	define( 'DRGF_DIR_URL', plugin_dir_url( __FILE__ ) );
}

require DRGF_DIR_PATH . 'inc/remove-google-fonts.php';

if ( is_admin() ) {
	require DRGF_DIR_PATH . 'admin/class-drgf-admin.php';
	require DRGF_DIR_PATH . 'admin/class-drgf-notice.php';
}

