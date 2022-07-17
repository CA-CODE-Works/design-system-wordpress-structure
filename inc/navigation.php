<?php
/**
 * Design System Structure Filters
 *
 * @package cagov-design-system-structure
 */

/* WP Filters */
add_filter( 'wp_nav_menu', 'cagov_ds_structure_nav_menu', 10, 2 );

/**
 * Filters the HTML content for navigation menus.
 *
 * @link  https://developer.wordpress.org/reference/hooks/wp_nav_menu/
 * @param string   $nav_menu The HTML content for the navigation menu.
 * @param stdClass $args     An object containing wp_nav_menu() arguments.
 *
 * @return string
 */
function cagov_ds_structure_nav_menu( $nav_menu, $args ) {
	global $post;
	$post_id = is_object( $post ) ? $post->ID : ( isset( $post['ID'] ) ? $post['ID'] : -1 );

	$theme_location = $args->theme_location;

	/* Header Menu Construction */
	if ( 'header-menu' === $theme_location && ! empty( $args->menu ) ) {
		$nav_menu = cagov_ds_structure_header_menu( $args );

		$mobile = '<div class="expanded-menu-section mobile-only"><strong class="expanded-menu-section-header"><a class="expanded-menu-section-header-link js-event-hm-menu" href="/">Home</a></strong></div>';

		$nav_menu = sprintf(
			'<nav id="main-menu" class="expanded-menu" role="navigation" aria-hidden="false" aria-label="Site Navigation">
					<div class="expanded-menu-grid">%1$s%2$s</div></nav>',
			$mobile,
			$nav_menu
		);
		/* Footer Menu Construction */
	} elseif ( 'footer-menu' === $theme_location && ! empty( $args->menu ) ) {
		$nav_menu = cagov_ds_structure_footer_menu( $args );

		$back_to_top = '<cagov-back-to-top data-hide-after="7000" data-label="Back to top"></cagov-back-to-top>';

		$logo = sprintf( '<a href="https://ca.gov" class="cagov-logo" title="ca.gov" target="_blank" rel="noopener">%1$s</a>', cagov_ds_structure_footer_logo() );

		$cc = sprintf( '<div class="container pt-0"><p class="copyright">Copyright <span aria-hidden="true">&copy;</span> %1$s State of California</p></div>', gmdate( 'Y' ) );

		$nav_menu = sprintf(
			'<footer>%1$s<div class="bg-light-grey"><div class="container">%2$s%3$s</div>%4$s</div></footer>',
			$back_to_top,
			$logo,
			$nav_menu,
			$cc
		);
	}

	return $nav_menu;
}


/**
 * HTML for the Design System Navigation Menu
 *
 * @param stdClass $args An object containing wp_nav_menu() arguments.
 *
 * @return string
 */
function cagov_ds_structure_header_menu( $args ) {
	$menuitems = wp_get_nav_menu_items( $args->menu->term_id, array( 'order' => 'DESC' ) );

	_wp_menu_item_classes_by_context( $menuitems );

	$nav_item = '';
	/* Iterate thru menuitems create Top Level (first-level-link) */
	foreach ( $menuitems as $i => $item ) {
		/*
		If a top level nav item,
		menu_item_parent= 0
		*/
		if ( ! $item->menu_item_parent ) {
			$item_meta = get_post_meta( $item->ID );
			/* Get array of Sub Nav Items (second-level-links) */
			$child_links = cagov_ds_structure_get_nav_menu_item_children( $item->ID, $menuitems );

			/* Count of Sub Nav Link */
			$child_count = count( $child_links );
			$sub_nav     = '';

			if ( 0 < $child_count && 'singlelevel' !== $args->style ) {
				/* Arrow */
				$arrow = '<span class="expanded-menu-section-header-arrow">
							<svg width="11" height="7" aria-hidden="true" 
							class="expanded-menu-section-header-arrow-svg" viewBox="0 0 11 7" fill="none"
							xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" clip-rule="evenodd"
								d="M1.15596 0.204797L5.49336 5.06317L9.8545 0.204797C10.4293 -0.452129 11.4124 0.625368 10.813 1.28143L5.90083 6.82273C5.68519 7.05909 5.32606 7.05909 5.1342 6.82273L0.174341 1.28143C-0.400433 0.6245 0.581838 -0.452151 1.15661 0.204797H1.15596Z"
								/>
							</svg>
						</span>';

				$link = sprintf(
					'<strong class="expanded-menu-section-header"><button class="expanded-menu-section-header-link js-event-hm-menu">
						<span>%1$s</span>%2$s</button></strong>',
					$item->title,
					$arrow
				);

				foreach ( $child_links as $i => $item ) {
					$sub_nav .= sprintf( '<a class="expanded-menu-dropdown-link js-event-hm-menu" href="%1$s" tabindex="-1">%2$s</a>', $item->url, $item->title );
				}

				$sub_nav = sprintf( '<div class="expanded-menu-dropdown">%1$s</div>', $sub_nav );
			} else {
				$link = sprintf(
					'<a class="expanded-menu-section-header-link js-event-hm-menu" href="%1$s">%2$s</a>',
					$item->url,
					$item->title
				);

			}

			/* if is current menut item add .active */
			$item->classes[] = in_array( 'current-menu-item', $item->classes, true ) ? ' active ' : '';

			/* Create Link */
			$nav_item .= sprintf(
				'<div class="expanded-menu-col js-cagov-navoverlay-expandable">
						<div class="expanded-menu-section">%1$s%2$s
						</div>
					  </div>',
				$link,
				$sub_nav
			);

		}
	} /* End of for each */

	/* Return Navigation */
	return $nav_item;
}

/**
 * HTML for the Design System Footer Menu
 *
 * @param stdClass $args An object containing wp_nav_menu() arguments.
 *
 * @return string
 */
function cagov_ds_structure_footer_menu( $args ) {
	$nav_links = '';

	/* loop thru and create a link (parent nav item only) */
	$menuitems = wp_get_nav_menu_items( $args->menu->term_id, array( 'order' => 'DESC' ) );

	foreach ( $menuitems as $item ) {
		if ( ! $item->menu_item_parent ) {
			$nav_links .= sprintf(
				'<a href="%1$s"%2$s>%3$s</a>',
				$item->url,
				( ! empty( $item->target ) ? sprintf( ' target="%1$s"', $item->target ) : '' ),
				$item->title
			);
		}
	}

	$nav_links = sprintf( '<div class="footer-secondary-links">%1$s</div>', $nav_links );

	return $nav_links;
}

/**
 * Return CA.gov logo used in footer menu.
 *
 * @return string
 */
function cagov_ds_structure_footer_logo() {
	return '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="34px" height="34px" viewbox="0 0 44 34" style="enable-background:new 0 0 44 34;" xml:space="preserve">
			<path class="ca" d="M27.4,14c0.1-0.4,0.4-1.5,0.9-3.2c0.1-0.5,0.4-1.3,0.9-2.7c0.5-1.4,0.9-2.5,1.2-3.3c-0.9,0.6-1.8,1.4-2.7,2.3
		c-3.2,3.5-6.9,7.6-8.3,9.8c0.5-0.1,1.5-1.2,4.7-2.3C26.3,14,27.4,14,27.4,14L27.4,14z M26.9,16.2c-10.1,0-14.5,16.1-21.6,16.1
		c-1.6,0-2.8-0.7-3.7-2.1c-0.6-0.9-0.8-2-0.8-3.1c0-2.9,1.4-6.7,4.2-11.1c2.4-3.8,4.9-6.9,7.5-9.2c2.3-2,4.2-3,5.9-3
		c0.9,0,1.6,0.3,2.1,1C20.8,5.2,21,5.8,21,6.5c0,1.3-0.4,2.8-1.3,4.5c-0.8,1.5-1.7,2.8-2.9,3.9c-0.8,0.8-1.4,1.1-1.8,1.1
		c-0.3,0-0.6-0.1-0.8-0.4c-0.2-0.2-0.3-0.4-0.3-0.7c0-0.5,0.4-1,1.2-1.6c1.2-0.9,2.1-1.8,2.8-2.9c1-1.5,1.5-2.8,1.5-3.8
		c0-0.4-0.1-0.7-0.3-0.9c-0.2-0.2-0.5-0.3-0.8-0.3c-0.7,0-1.8,0.5-3.2,1.6c-1.6,1.2-3.2,2.9-5,5C8,14.8,6.3,17.4,5.2,20
		c-1.2,2.7-1.8,5-1.8,6.9c0,0.9,0.3,1.7,0.8,2.3c0.6,0.7,1.3,1.1,2.1,1.1c3.2-0.1,7.2-7.4,8.4-9.1C27,4.3,27.9,4.3,29.8,2.5
		c1.1-1,1.9-1.6,2.5-1.6c0.4,0,0.7,0.1,0.9,0.4c0.2,0.3,0.3,0.5,0.3,0.9c0,0.4-0.2,1-0.6,2c-0.7,1.7-1.3,3.5-1.9,5.4
		c-0.5,1.7-0.9,3-1,3.9c0.2,0,0.4,0,0.5,0c0.4,0,0.7,0,1,0c0.8,0,1.2,0.3,1.2,0.9c0,0.3-0.1,0.5-0.3,0.8c-0.2,0.3-0.4,0.4-0.6,0.5
		c-0.1,0-0.3,0-0.7,0c-0.8,0-1.4,0-1.7,0.1c-0.1,0.4-0.5,4.1-1.1,4.2C26.7,21.5,26.8,16.7,26.9,16.2L26.9,16.2z"/>
			<g>
			  <path class="gov" d="M16.8,27.2c0.4,0,0.8,0.2,1.1,0.5c0.3,0.3,0.5,0.7,0.5,1.1c0,0.4-0.2,0.8-0.5,1.1c-0.3,0.3-0.7,0.5-1.1,0.5
			c-0.4,0-0.8-0.2-1.1-0.5c-0.3-0.3-0.5-0.7-0.5-1.1c0-0.4,0.2-0.8,0.5-1.1C16,27.4,16.4,27.2,16.8,27.2L16.8,27.2z"/>
			  <path class="gov" d="M26.7,22.9l-1.1,1.1c-0.7-0.8-1.5-1.1-2.5-1.1c-0.8,0-1.5,0.3-2.1,0.8c-0.6,0.6-0.8,1.2-0.8,2
			c0,0.8,0.3,1.5,0.9,2.1c0.6,0.6,1.3,0.8,2.2,0.8c0.6,0,1-0.1,1.4-0.3c0.4-0.2,0.7-0.6,0.9-1.1h-2.4v-1.5h4.2l0,0.4
			c0,0.7-0.2,1.4-0.6,2.1c-0.4,0.7-0.9,1.2-1.5,1.5c-0.6,0.3-1.3,0.5-2.1,0.5c-0.9,0-1.7-0.2-2.3-0.6c-0.7-0.4-1.2-0.9-1.6-1.6
			c-0.4-0.7-0.6-1.5-0.6-2.3c0-1.1,0.4-2.1,1.1-2.9c0.9-1,2-1.5,3.4-1.5c0.7,0,1.4,0.1,2.1,0.4C25.7,22,26.2,22.4,26.7,22.9
			L26.7,22.9z"/>
			  <path class="gov" d="M32.2,21.4c1.2,0,2.2,0.4,3.1,1.3c0.9,0.9,1.3,1.9,1.3,3.2c0,1.2-0.4,2.3-1.3,3.1c-0.8,0.9-1.9,1.3-3.1,1.3
			c-1.3,0-2.3-0.4-3.2-1.3c-0.8-0.9-1.3-1.9-1.3-3.1c0-0.8,0.2-1.5,0.6-2.2c0.4-0.7,0.9-1.2,1.6-1.6C30.7,21.5,31.4,21.4,32.2,21.4
			L32.2,21.4z M32.2,22.9c-0.8,0-1.4,0.3-2,0.8c-0.5,0.5-0.8,1.2-0.8,2.1c0,0.9,0.3,1.7,1,2.2c0.5,0.4,1.1,0.6,1.8,0.6
			c0.8,0,1.4-0.3,1.9-0.8c0.5-0.6,0.8-1.2,0.8-2c0-0.8-0.3-1.5-0.8-2C33.6,23.2,33,22.9,32.2,22.9L32.2,22.9z"/>
			  <polygon class="gov" points="36.3,21.6 38,21.6 40.1,27.6 42.2,21.6 43.9,21.6 40.8,30 39.3,30 36.3,21.6 	"/>
			</g>
		  </svg>';
}
