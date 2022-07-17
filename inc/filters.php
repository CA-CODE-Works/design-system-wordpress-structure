<?php
/**
 * Design System Structure Filters
 *
 * @package cagov-design-system-structure
 */

/* WP Filters */
add_filter( 'body_class', 'cagov_ds_structure_body_class', 20, 2 );
add_filter( 'post_class', 'cagov_ds_structure_post_class', 15 );
add_filter( 'theme_page_templates', 'cagov_ds_structure_theme_page_templates', 15 );

/**
 * Theme Filters
 */
add_filter( 'caweb_page_title_class', 'cagov_ds_structure_page_title_class' );
add_filter( 'caweb_page_container_class', 'cagov_ds_structure_page_container_class' );
add_filter( 'caweb_page_main_content_class', 'cagov_ds_structure_main_content_class' );
add_filter( 'caweb_post_title_class', 'cagov_ds_structure_page_title_class' );
add_filter( 'caweb_post_container_class', 'cagov_ds_structure_page_container_class' );
add_filter( 'caweb_post_main_content_class', 'cagov_ds_structure_main_content_class' );

/**
 * Plugin Filters
 */
// The Events Calendar.
add_filter( 'tribe_default_events_template_classes', 'cagov_ds_structure_default_events_template_classes' );

/**
 * CAWeb Page Body Class
 *
 * Filters the list of CSS body class names for the current post or page.
 *
 * @link https://developer.wordpress.org/reference/hooks/body_class/
 * @param  array $wp_classes An array of body class names.
 * @param  array $extra_classes An array of additional class names added to the body.
 *
 * @category add_filter( 'body_class','cagov_ds_structure_body_class' , 20 , 2 );
 * @return array
 */
function cagov_ds_structure_body_class( $wp_classes, $extra_classes ) {
	global $post;

	/* List of the classes that need to be removed */
	$blacklist = array( '5.5', '6.0' );

	/* List of extra classes that need to be added to the body */
	if ( isset( $post->ID ) ) {
		$whitelist = array( 'design-system' );
	}

	/* Remove any classes in the blacklist from the wp_classes */
	$wp_classes = array_diff( $wp_classes, $blacklist );

	/* Return filtered wp class */
	return array_merge( $wp_classes, (array) $whitelist );
}

/**
 * CAWeb Post Body Class
 *
 * @link https://developer.wordpress.org/reference/hooks/post_class/
 * @param  array $classes An array of post class names.
 * @category add_filter( 'post_class','cagov_ds_structure_post_class' , 15 );
 * @return array
 */
function cagov_ds_structure_post_class( $classes ) {
	global $post;

	return $classes;
}

/**
 * Design System Theme Page Templates
 * Filters list of page templates for a theme.
 *
 * @link https://developer.wordpress.org/reference/hooks/theme_page_templates/
 * @param  array $templates Array of page templates. Keys are filenames, values are translated names.
 *
 * @return array
 */
function cagov_ds_structure_theme_page_templates( $templates ) {
	return $templates;
}

/**
 * Allows filtering the classes for the main element for the /events/ page.
 *
 * @since 5.8.0
 *
 * @param array<string> $classes An (unindexed) array of classes to apply.
 */
function cagov_ds_structure_default_events_template_classes( $classes ) {
	$classes[] = 'main-content-ds';

	return $classes;
}

/**
 * Allows filtering the classes for the CAWeb Page Title element.
 *
 * @since 1.6.4
 *
 * @param array<string> $classes An (unindexed) array of classes to apply.
 */
function cagov_ds_structure_page_title_class( $classes = '' ) {
	return 'page-title-ds';
}

/**
 * Allows filtering the classes for the CAWeb Page Container element.
 *
 * @since 1.6.4
 *
 * @param array<string> $classes An (unindexed) array of classes to apply.
 */
function cagov_ds_structure_page_container_class( $classes = '' ) {
	return 'page-container-ds';
}

/**
 * Allows filtering the classes for the CAWeb Page Container element.
 *
 * @since 1.6.4
 *
 * @param array<string> $classes An (unindexed) array of classes to apply.
 */
function cagov_ds_structure_main_content_class( $classes = '' ) {
	return 'main-container-ds';
}
