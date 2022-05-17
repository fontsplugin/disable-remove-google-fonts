<?php
/**
 * Plugin Name: Disable & Remove Google Fonts
 * Plugin URI: https://wordpress.org/plugins/disable-remove-google-fonts/
 * Description: Optimize frontend performance by disabling Google Fonts. GDPR-friendly.
 * Author: Fonts Plugin
 * Author URI: https://fontsplugin.com
 * Version: 1.3.9
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

	// Remove fonts added by the Divi Extra theme
	remove_action( 'wp_footer', 'et_builder_print_font' );

	// Dequeue Google Fonts loaded by Revolution Slider.
	remove_action( 'wp_footer', array( 'RevSliderFront', 'load_google_fonts' ) );

	// Dequeue the Jupiter theme font loader.
	wp_dequeue_script( 'mk-webfontloader' );

	// Remove the aThemes resource hints.
	remove_action( 'wp_head', 'sydney_preconnect_google_fonts' );
	remove_action( 'wp_head', 'botiga_preconnect_google_fonts' );

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

		if ( strpos( $src, 'fonts.googleapis' ) !== false ) {
			if ( ! array_key_exists( $handle, array_flip( $allowed ) ) ) {
				wp_dequeue_style( $handle );
			}
		}
	}

	/**
	 * Some themes set the Google Fonts URL as a dependency, so we need to replace
	 * it with a blank value rather than removing it entirely. As that would
	 * remove the stylesheet too.
	 */
	foreach ( $wp_styles->registered as $style ) {
		foreach( $style->deps as $dep ) {
			if ( ( strpos( $dep, 'google-fonts' ) !== false ) || ( strpos( $dep, 'google_fonts' ) !== false ) || ( strpos( $dep, 'googlefonts' ) !== false ) ) {
				$wp_styles->remove( $dep );
				$wp_styles->add( $dep, '' );
			}
		}
	}
	remove_action( 'wp_head', 'hu_print_gfont_head_link', 2 );
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

if ( ! function_exists( 'hemingway_get_google_fonts_url' ) ) {
	/**
	 * Dequeue Google Fonts loaded by the Hemingway theme.
	 */
	function hemingway_get_google_fonts_url() {
		return false;
	}
}

/**
 * Dequeue Google Fonts loaded by the Avia framework (Enfold theme).
 */
add_action( 'init', 'drgf_enfold_customization_switch_fonts' );
function drgf_enfold_customization_switch_fonts() {
		if ( class_exists( 'avia_style_generator' ) ) {
	    global $avia;
	    $avia->style->print_extra_output = false;
		}
}
