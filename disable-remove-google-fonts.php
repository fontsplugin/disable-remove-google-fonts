<?php
/**
 * Plugin Name: Disable/Remove Google Fonts
 * Plugin URI: https://wordpress.org/plugins/disable-remove-google-fonts/
 * Description: Optimize frontend performance by disabling Google Fonts.
 * Author: Fonts Plugin
 * Author URI: https://fontsplugin.com
 * Version: 1.3.2
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 *
 * @package disable-remove-google-fonts
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dequeue Google Fonts based on URL.
 */
function drgf_dequeueu_fonts() {
	global $wp_styles;

	if ( ! ( $wp_styles instanceof WP_Styles ) ) {
		return;
	}

	$allowed = apply_filters(
		'drgf_exceptions',
		[ 'olympus-google-fonts' ]
	);

	foreach ( $wp_styles->registered as $style ) {
		$handle = $style->handle;
		$src    = $style->src;
		$gfonts = strpos( $src, 'fonts.googleapis' );

		if ( false !== $gfonts ) {
			if ( ! array_key_exists( $handle, array_flip( $allowed ) ) ) {
				wp_dequeue_style( $handle );
			}
		}
	}

	// Remove fonts added by the Divi Extra theme
	remove_action( 'wp_footer', 'et_builder_print_font' );

	// Dequeue Google Fonts loaded by Revolution Slider.
	remove_action( 'wp_footer', array( 'RevSliderFront', 'load_google_fonts' ) );

	// Dequeue the Jupiter theme font loader.
	wp_dequeue_script( 'mk-webfontloader' );

}
add_action( 'wp_enqueue_scripts', 'drgf_dequeueu_fonts', 9999 );
add_action( 'wp_print_styles', 'drgf_dequeueu_fonts', 9999 );

/**
 * Dequeue Google Fonts loaded by Elementor.
 */
add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );

/**
 * Dequeue Google Fonts loaded by Beaver Builder.
 */
add_filter(
	'fl_builder_google_fonts_pre_enqueue',
	function( $fonts ) {
		return array();
	}
);

/**
 * Dequeue Google Fonts loaded by JupiterX theme.
 */
add_filter(
	'jupiterx_register_fonts',
	function( $fonts ) {
		return array();
	},
	99999
);

/**
 * Dequeue Google Fonts loaded by the Hustle plugin.
 */
add_filter( 'hustle_load_google_fonts', '__return_false' );

/**
 * Dequeue Google Fonts loaded by the Hustle plugin.
 */
add_filter( 'mailpoet_display_custom_fonts', '__return_false' );

if ( ! function_exists( 'apollo13framework_get_web_fonts_dynamic' ) ) {
	/**
	 * Dequeue Google Fonts loaded by the Apollo13 Themes Framework.
	 */
	function apollo13framework_get_web_fonts_dynamic() {
		return;
	}
}

if ( ! function_exists( 'apollo13framework_get_web_fonts_static' ) ) {
	/**
	 * Dequeue Google Fonts loaded by the Apollo13 Themes Framework.
	 */
	function apollo13framework_get_web_fonts_static() {
		return;
	}
}
