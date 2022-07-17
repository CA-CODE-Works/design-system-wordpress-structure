<?php
/**
 * Loads caGov Design System site branding.
 *
 * @package cagov-design-system-structure
 */

?>

<!-- Branding -->
<?php if ( ! empty( $cagov_ds_structure_logo ) ) : ?>
<a href="/" class="grid-logo" aria-label="<?php print esc_attr( get_bloginfo( 'name' ) ); ?> Logo">
	<img src="<?php print esc_url( $cagov_ds_structure_logo ); ?>" alt="<?php print esc_attr( $cagov_ds_structure_logo_alt_text ); ?>" />
</a>
<?php else : ?>
<a class="grid-org-name" href="/">
	<span class="org-name-dept"><?php print esc_attr( get_bloginfo( 'name' ) ); ?></span>
	<span class="org-name-state">State of California</span>
</a>
<?php endif; ?>
