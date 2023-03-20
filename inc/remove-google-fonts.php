<?php
/**
 * Remove DNS prefetch, preconnect and preload headers.
 */
function drgf_remove_prefetch( $urls, $relation_type ) {
	if ( 'dns-prefetch' === $relation_type ) {
		$urls = array_diff( $urls, array( 'fonts.googleapis.com' ) );
	} elseif ( 'preconnect' === $relation_type || 'preload' === $relation_type ) {
		foreach ( $urls as $key => $url ) {
			if ( ! isset( $url['href'] ) ) {
				continue;
			}
			if ( preg_match( '/\/\/fonts\.(gstatic|googleapis)\.com/', $url['href'] ) ) {
				unset( $urls[ $key ] );
			}
		}
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'drgf_remove_prefetch', PHP_INT_MAX, 2 );

// Remove the aThemes resource hints.
remove_action( 'wp_head', 'sydney_preconnect_google_fonts' );
remove_action( 'wp_head', 'botiga_preconnect_google_fonts' );

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
			$strings = ['google-fonts', 'google_fonts', 'googlefonts', 'bookyourtravel-heading-font', 'bookyourtravel-base-font', 'bookyourtravel-font-icon', 'twb-open-sans'];
			if ( drgf_strposa( $dep, $strings ) === true ) {
				$wp_styles->remove( $dep );
				$wp_styles->add( $dep, '' );
			}
		}
	}

	remove_action( 'wp_head', 'hu_print_gfont_head_link', 2 );
	remove_action('wp_head', 'appointment_load_google_font');
}

add_action( 'wp_enqueue_scripts', 'drgf_dequeueu_fonts', PHP_INT_MAX );
add_action( 'wp_print_styles', 'drgf_dequeueu_fonts', PHP_INT_MAX );

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
 * Dequeue Google Fonts loaded by the Vantage theme.
 */
add_filter( 'vantage_import_google_fonts', '__return_false' );

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
function drgf_enfold_customization_switch_fonts() {
	if ( class_exists( 'avia_style_generator' ) ) {
		global $avia;
		$avia->style->print_extra_output = false;
	}
}
add_action( 'init', 'drgf_enfold_customization_switch_fonts' );

/**
 * Remove the preconnect hint to fonts.gstatic.com.
 */
function drgf_remove_divi_preconnect() {
	remove_action( 'wp_enqueue_scripts', 'et_builder_preconnect_google_fonts', 9 );
}
add_action( 'init', 'drgf_remove_divi_preconnect' );

/**
 * Dequeue Google Fonts loaded by Avada theme.
 */
$fusion_options = get_option( 'fusion_options', false );
if (
		$fusion_options
		&& isset( $fusion_options['gfonts_load_method'] )
		&& $fusion_options['gfonts_load_method'] === 'cdn'
	) {
	add_filter(
		'fusion_google_fonts',
		function( $fonts ) {
			return array();
		},
		99999
	);
}

/**
 * Avada caches the CSS output so we need to clear the
 * cache once the fonts have been removed.
 */
function drgf_flush_avada_cache() {
	if ( function_exists( 'fusion_reset_all_caches' ) ) {
		fusion_reset_all_caches();
	}
}
register_activation_hook( __FILE__, 'drgf_flush_avada_cache' );

/**
 * WPBakery enqueues fonts correctly using wp_enqueue_style
 * but does it late so this is required.
 */
function drgf_dequeue_wpbakery_fonts() {
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
}
add_action( 'wp_footer', 'drgf_dequeue_wpbakery_fonts' );

/**
 * Dequeue Google Fonts loaded by Kadence theme.
 */
add_filter( 'kadence_theme_google_fonts_array', '__return_empty_array' );
add_filter( 'kadence_print_google_fonts', '__return_false' );

/**
 * Dequeue Google Fonts loaded by X theme.
 */
add_filter( 'cs_load_google_fonts', '__return_false' );

/**
 * Helper function to run strpos() using an array as the needle.
 */
function drgf_strposa( $haystack, $needles, $offset = 0 ) {
	$chr = array();
	foreach( $needles as $needle)  {
		$res = strpos( $haystack, $needle, $offset );
		if ( $res !== false ) return true;
	}
return false;
}

/**
 * Dequeue Google Fonts loaded by Unyson.
 */
function drgf_remove_unyson_fonts() {
	remove_action( 'wp_enqueue_scripts', array( 'Artey_Unyson_Google_Fonts', 'output_url' ), 9999 );
};
add_action('init', 'drgf_remove_unyson_fonts');

/**
 * Dequeue Google Fonts loaded in wp-admin by the Sucuri plugin.
 */
function drgf_remove_sucuri_admin_fonts() {
	wp_dequeue_style( 'sucuriscan-google-fonts' );
}
add_action( 'admin_enqueue_scripts', 'drgf_remove_sucuri_admin_fonts' );

/**
 * Dequeue Google Fonts loaded by Kadence Blocks.
 */
add_filter( 'kadence_blocks_print_google_fonts', '__return_false' );

/**
 * Dequeue Google Fonts loaded in GeneratePress.
 */
function drgf_remove_generatepress_fonts() {
	wp_dequeue_style( 'generate-google-fonts' );
}
add_action( 'wp_enqueue_scripts', 'drgf_remove_generatepress_fonts', 99 );

/**
 * Dequeue Google Fonts loaded by Ajax Search lite.
 */
add_filter( 'asl_custom_fonts', '__return_empty_array' );

