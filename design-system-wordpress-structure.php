<?php
/**
 * Plugin Name: ca.gov Design System Structure
 *
 * @source https://github.com/cagov/design-system/
 * Plugin URI: https://github.com/CA-CODE-Works/design-system-wordpress-structure/
 * Description: Integrates the <a href="https://designsystem.webstandards.ca.gov">State of California Design System Structure</a> into the WordPress.
 * Author: Office of Digital Innovation
 * Author URI: https://digital.ca.gov
 * Version: 1.0.0
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: cagov-design-system-structure
 * Requires at least: 5.8
 *
 * @package  cagov-design-system-structure
 * @author   Office of Digital Innovation <info@digital.ca.gov>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/cagov/design-system-wordpress-structure#README
 * @docs https://designsystem.webstandards.ca.gov
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin constants.
$cagov_doc_root = isset( $_SERVER['DOCUMENT_ROOT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['DOCUMENT_ROOT'] ) ) : '';

define( 'CAGOV_DESIGN_SYSTEM_STRUCTURE', __DIR__ );
define( 'CAGOV_DESIGN_SYSTEM_STRUCTURE__VERSION', '1.2.0.3' );
define( 'CAGOV_DESIGN_SYSTEM_STRUCTURE_URI', esc_url( str_replace( $cagov_doc_root, '', __DIR__ ) ) );
define( 'CAGOV_DESIGN_SYSTEM_STRUCTURE__DEBUG', true ); // Can associate with env variable later.

/**
 * Plugin API/Action Reference
 * Actions Run During a Typical Request
 *
 * @link https://codex.wordpress.org/Plugin_API/Action_Reference#Actions_Run_During_a_Typical_Request
 */

add_action( 'init', 'cagov_ds_structure_init' );
add_action( 'admin_init', 'cagov_ds_structure_admin_init' );
add_action( 'admin_enqueue_scripts', 'cagov_ds_structure_admin_enqueue_scripts', 15 );

add_action( 'wp_enqueue_scripts', 'cagov_ds_structure_wp_enqueue_scripts', 999999999 );
add_action( 'get_header', 'cagov_ds_structure_get_header', 10, 2 );
add_action( 'get_footer', 'cagov_ds_structure_get_footer', 10, 2 );

/**
 * Design System Admin Init
 *
 * Triggered before any other hook when a user accesses the admin area.
 * Note, this does not just run on user-facing admin screens.
 * It runs on admin-ajax.php and admin-post.php as well.
 *
 * @link https://developer.wordpress.org/reference/hooks/admin_init/
 * @return void
 */
function cagov_ds_structure_admin_init() {
	// Updater.
	require_once CAGOV_DESIGN_SYSTEM_STRUCTURE . '/admin/class-ca-design-system-structure-update.php';
}

/**
 * Design System Initialization
 *
 * Fires after WordPress has finished loading but before any headers are sent.
 * Include Gutenberg Block assets by getting the index file of each block build file.
 *
 * @link https://developer.wordpress.org/reference/hooks/init/
 * @return void
 */
function cagov_ds_structure_init() {
	/* Include Design System Functionality */
	foreach ( glob( CAGOV_DESIGN_SYSTEM_STRUCTURE . '/inc/*.php' ) as $file ) {
		require_once $file;
	}
}

/**
 * Design System Admin Enqueue Scripts and Styles
 *
 * @link https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
 * @category add_action( 'admin_enqueue_scripts', 'cagov_ds_structure_admin_enqueue_scripts', 15 );
 * @param  string $hook The current admin page.
 *
 * @return void
 */
function cagov_ds_structure_admin_enqueue_scripts( $hook ) {
	$pages     = array( 'toplevel_page_cagov_ds_structure_options' );
	$admin_css = cagov_ds_structure_get_min_file( '/css/admin.css' );

	$color = get_option( 'cagov_ds_structure_colorscheme', 'cagov' );

	$editor_css = cagov_ds_structure_get_min_file( "/css/cagov-design-system-$color.css" );

	if ( in_array( $hook, $pages, true ) ) {
		$admin_js = cagov_ds_structure_get_min_file( '/js/admin.js', 'js' );

		/* Enqueue Scripts */
		wp_enqueue_script( 'jquery' );
		wp_enqueue_media();
		wp_enqueue_editor();
		wp_enqueue_script( 'custom-header' );

		// wp_register_script( 'cagov-ds-structure-admin-scripts', $admin_js, array( 'jquery', 'thickbox' ), CAGOV_DESIGN_SYSTEM_STRUCTURE__VERSION, true );

		/*$schemes = array( 'design-system' => caweb_color_schemes( 'design-system' ) );
		foreach ( caweb_template_versions() as $v => $label ) {
			$schemes[ "$v" ] = caweb_color_schemes( $v );
		}

		$caweb_localize_args = array(
			'defaultFavIcon'     => caweb_default_favicon_url(),
			'changeCheck'        => $hook,
			'caweb_icons'        => array_values( caweb_symbols( -1, '', '', false ) ),
			'caweb_colors'       => caweb_template_colors(),
			'tinymce_settings'   => caweb_tiny_mce_settings(),
			'caweb_colorschemes' => $schemes,
		);

		wp_localize_script( 'caweb-admin-scripts', 'caweb_admin_args', $caweb_localize_args );

		wp_enqueue_script( 'caweb-admin-scripts' );
		*/
		/*
		Bootstrap 4 Toggle
		https://gitbrent.github.io/bootstrap4-toggle/
		*/
		wp_enqueue_script( 'caweb-boot1', 'https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js', array( 'jquery' ), '3.6.1', true );

		/* Enqueue Styles */
		wp_enqueue_style( 'cagov-ds-structure-admin-styles', $admin_css, array(), CAGOV_DESIGN_SYSTEM_STRUCTURE__VERSION );
		wp_enqueue_style( 'cagov-ds-structure-boot1-toggle', 'https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css', array(), CAWEB_VERSION );
	} elseif ( in_array( $hook, array( 'post.php', 'post-new.php', 'widgets.php' ), true ) ) {
		wp_enqueue_style( 'cagov-ds-structure-admin-styles', $admin_css, array(), CAGOV_DESIGN_SYSTEM_STRUCTURE__VERSION );
	}

	/* Load editor styling */
	wp_dequeue_style( get_template_directory_uri() . 'css/editor-style.css' );
	add_editor_style( $editor_css );
}

/**
 * Register Design System scripts/styles with priority of 99999999
 *
 * Fires when scripts and styles are enqueued.
 *
 * @category add_action( 'wp_enqueue_scripts', 'cagov_ds_structure_wp_enqueue_scripts', 99999999 );
 * @link https://developer.wordpress.org/reference/hooks/wp_enqueue_scripts/
 *
 * @return void
 */
function cagov_ds_structure_wp_enqueue_scripts() {
	global $pagenow;
	$cwes     = wp_create_nonce( 'cagov_ds_structure_wp_enqueue_scripts' );
	$verified = isset( $cwes ) && wp_verify_nonce( sanitize_key( $cwes ), 'cagov_ds_structure_wp_enqueue_scripts' );

	$color = get_option( 'cagov_ds_structure_colorscheme', 'cagov' );
	
	$core_css_file = cagov_ds_structure_get_min_file( "/css/cagov-design-system-$color.css" );

	/* caGov Design System Core CSS */
	wp_enqueue_style( 'cagov-design-system-structure-style', $core_css_file, array(), CAGOV_DESIGN_SYSTEM_STRUCTURE__VERSION );
	wp_enqueue_style( 'caweb-google-font-style', 'https://fonts.googleapis.com/css?family=Asap+Condensed:400,600|Source+Sans+Pro:400,700', array(), CAWEB_VERSION );

	$localize_args = array(
		'structure_version'           => CAGOV_DESIGN_SYSTEM_STRUCTURE__VERSION,
		'structure_site_color_scheme' => $color,
		'is_front'                    => is_front_page(),
		'ajaxurl'                     => admin_url( 'admin-post.php' ),
		'path'                        => wp_parse_url( get_site_url() )['path'] ?? '/',
	);

	$frontend_js_file = cagov_ds_structure_get_min_file( '/js/cagov-design-system.js', 'js' );

	wp_register_script( 'cagov-design-system-structure-script', $frontend_js_file, array(), CAGOV_DESIGN_SYSTEM_STRUCTURE__VERSION, true );

	wp_localize_script( 'cagov-design-system-structure-script', 'args', $localize_args );

	/* Enqueue Scripts */
	wp_enqueue_script( 'cagov-design-system-structure-script' );

	// Deregister styles
	wp_dequeue_style( 'divi-fonts' );
	wp_dequeue_style( 'caweb-core-style' );

	// Deregister scripts
	wp_dequeue_script( 'caweb-script' );
	
}

/**
 * Fires before the header template file is loaded.
 *
 * @link https://developer.wordpress.org/reference/hooks/get_header/
 *
 * @param string $name Name of the specific header file to use. Null for the default header.
 * @param array  $args Additional arguments passed to the header template.
 * @return void
 */
function cagov_ds_structure_get_header( $name, $args = array() ) {
	cagov_ds_structure_override_partial( 'header', $name, array( 'wp_head', 'get_header' ) );
}

/**
 * Fires before the footer template file is loaded.
 *
 * @link https://developer.wordpress.org/reference/hooks/get_footer/
 *
 * @param string $name Name of the specific footer file to use. Null for the default footer.
 * @param array  $args Additional arguments passed to the footer template.
 * @return void
 */
function cagov_ds_structure_get_footer( $name, $args = array() ) {
	cagov_ds_structure_override_partial( 'footer', $name, array( 'wp_footer', 'get_footer' ) );
}

