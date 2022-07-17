<?php
/**
 * Design System Helper Functions
 *
 * @package cagov-design-system
 */

/**
 * Load Minified Version of a file
 *
 * @param  string $f File to load.
 * @param  mixed  $ext Extension of file, default css.
 *
 * @return string
 */
function cagov_ds_structure_get_min_file( $f, $ext = 'css' ) {
	/* if a minified version exists load it */
	if ( ! CAGOV_DESIGN_SYSTEM_STRUCTURE__DEBUG && file_exists( CAGOV_DESIGN_SYSTEM_STRUCTURE . str_replace( ".$ext", ".min.$ext", $f ) ) ) {
		return CAGOV_DESIGN_SYSTEM_STRUCTURE_URI . str_replace( ".$ext", ".min.$ext", $f );
	} else {
		return CAGOV_DESIGN_SYSTEM_STRUCTURE_URI . $f;
	}
}


/**
 * Render a custom partial overriding the original one.
 *
 * @since 4.0
 *
 * @link https://developer.wordpress.org/reference/hooks/get_header/
 *
 * @param string       $partial Name of the partial to use.
 * @param string       $name Name of the specific file to use.
 * @param string|array $hooks Hooks that need to be skipped so that they dont fire twice.
 * @return void
 */
function cagov_ds_structure_override_partial( $partial, $name, $hooks = '' ) {
	global $wp_filter;

	/**
	 * Mimicking WordPress core code behavior.
	 *
	 * @link https://core.trac.wordpress.org/browser/tags/6.0/src/wp-includes/general-template.php#L40
	 */
	$templates = array();
	$name      = (string) $name;
	if ( '' !== $name ) {
		$templates[] = "$partial-$name.php";
	}
	$templates[] = "$partial.php";

	// Buffer and discard the original partial forcing a require_once so it doesn't load again later.
	$buffered = ob_start();
	if ( $buffered ) {
		$actions = array();
		$hooks   = is_string( $hooks ) ? array( $hooks ) : $hooks;

		foreach ( $hooks as $hook ) {
			// Skip any partial-specific actions so they don't run twice.
			$actions[ $hook ] = $wp_filter[ $hook ];
			unset( $wp_filter[ $hook ] );
		}

		locate_template( $templates, true, true );
		$html = ob_get_clean();

		// Restore skipped actions.
		// phpcs:disable
		foreach ( $hooks as $hook ) {
			$wp_filter[ $hook ] = $actions[ $hook ];
		}
		// phpcs:enable
	}

	require_once CAGOV_DESIGN_SYSTEM_STRUCTURE . "/$partial.php";

}


/**
 * Get User Profile Color
 *
 * @return string
 */
function cagov_ds_structure_get_user_color() {
	global $_wp_admin_css_colors;

	$admin_color = get_user_option( 'admin_color' );

	return $_wp_admin_css_colors[ $admin_color ];
}

/**
 * Retrieve Design System Color Schemes
 *
 * @param  string $field Whether to return filename, displayname or both.
 * @param  string $color Retrieve information on a specific colorscheme.
 *
 * @return array
 */
function cagov_ds_structure_color_schemes( $field = '', $color = '' ) {
	$css_dir = sprintf( '%1$s/css', CAGOV_DESIGN_SYSTEM_STRUCTURE );
	$pattern = '/.*cagov-design-system-(.*).css/';

	$schemes = array();

	/*
	Get glob of colorschemes
	*/
	$tmp = glob( sprintf( '%1$s/*[^.min]\.css', $css_dir ) );

	/*
	Iterate thru each colorscheme
	*/
	foreach ( $tmp as $css_file ) {
		$filename    = preg_replace( $pattern, '\1', $css_file );
		$displayname = ucwords( strtolower( $filename ) );

		$schemekey = strtolower( str_replace( ' ', '', $displayname ) );

		switch ( $field ) {
			case 'filename':
				$schemes[ $schemekey ] = $filename;

				break;
			case 'displayname':
				$schemes[ $schemekey ] = $displayname;

				break;
			default:
				$schemes[ $schemekey ] = array(
					'filename'    => $filename,
					'displayname' => $displayname,
				);

				break;

		}

		if ( ! empty( $color ) && $color === $schemekey && isset( $schemes[ $color ] ) ) {
			return $schemes[ $color ];
		}
	}

	ksort( $schemes );

	return $schemes;
}

/**
 * Returns all child nav_menu_items under a specific parent
 *
 * @source https://wpsmith.net/2011/how-to-get-all-the-children-of-a-specific-nav-menu-item/
 * @param  int   $parent_id The parent nav_menu_item ID.
 * @param  array $nav_menu_items Array of Nav Menu Objects.
 * @param  bool  $depth Gives all children or direct children only.
 *
 * @return array
 */
function cagov_ds_structure_get_nav_menu_item_children( $parent_id, $nav_menu_items, $depth = true ) {
	$nav_menu_item_list = array();

	foreach ( (array) $nav_menu_items as $nav_menu_item ) {
		if ( (int) $nav_menu_item->menu_item_parent === (int) $parent_id ) {
			$nav_menu_item_list[] = $nav_menu_item;
			if ( $depth ) {
				$children = cagov_ds_structure_get_nav_menu_item_children( $nav_menu_item->ID, $nav_menu_items );
				if ( $children ) {
					$nav_menu_item_list = array_merge( $nav_menu_item_list, $children );
				}
			}
		}
	}

	return $nav_menu_item_list;
}
