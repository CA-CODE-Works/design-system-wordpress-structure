<?php
/**
 * Loads Sitewide Header.
 *
 * @package cagov-design-system-structure
 */

 /* Branding */
$caweb_logo          = '' !== esc_url( get_option( 'header_ca_branding' ) ) ? esc_url( get_option( 'header_ca_branding' ) ) : '';
$caweb_logo_alt_text = ! empty( get_option( 'header_ca_branding_alt_text', '' ) ) ? get_option( 'header_ca_branding_alt_text' ) : caweb_get_attachment_post_meta( $caweb_logo, '_wp_attachment_image_alt' );

/* Search */
$caweb_google_search_id     = get_option( 'ca_google_search_id', '' );

?>

<!-- Sitewide Header -->
<div class="site-header">
	<div class="container<?php echo ! empty( $caweb_logo ) ? ' with-logo': '' ?>">
	<?php
		// Include Branding.
		require_once 'partials/branding.php';

		// Include Mobile Controls.
		require_once 'partials/mobile-controls.php';

		// Include Search.
		require_once 'partials/search-form.php';

	?>
	</div>
</div>
<!-- End of Sitewide Header -->
