<?php
/**
 * Loads CAWeb <header> tag.
 *
 * @package CAWeb
 */

?>

<header id="header" class="global-header">
	<div id="skip-to-content"><a href="#main-content">Skip to Main Content</a></div>
	<?php

	/* Include Statewide Header */
	require_once 'structures/statewide-header.php';

	/* Include Sitewide Header */
	require_once 'structures/sitewide-header.php';

	?>
	<cagov-site-navigation>
		<div>
		<?php

			/* Include Navigation */
			wp_nav_menu(
				array(
					'theme_location' => 'header-menu',
					'style'          => get_option( 'cagov_ds_structure_navigation_menu', 'singlelevel' ),
				)
			);

			?>
		</div>
	</cagov-site-navigation>
</header>
