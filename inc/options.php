<?php
/**
 * Main caGov Design Structions Options File
 *
 * @package cagov-design-system-structure
 */

add_action( 'admin_menu', 'cagov_ds_structure_admin_menu' );
add_filter( 'custom_menu_order', 'cagov_ds_structure_wpse_custom_menu_order', 10, 1 );
add_filter( 'menu_order', 'cagov_ds_structure_wpse_custom_menu_order', 10, 1 );

/**
 * caGov Design System Administration Menu Setup
 * Fires before the administration menu loads in the admin.
 *
 * @link https://developer.wordpress.org/reference/hooks/admin_menu/
 * @return void
 */
function cagov_ds_structure_admin_menu() {
	global $submenu;

	/* Add Design System Options */
	add_menu_page(
		'Design System',
		'Design System',
		'manage_options',
		'cagov_ds_structure_options',
		'cagov_ds_structure_option_page',
		sprintf( '%1$s/images/favicon.ico', CAGOV_DESIGN_SYSTEM_STRUCTURE_URI )
	);
	//add_submenu_page( 'cagov_ds_structure_options', 'Design System Options', 'Settings', 'manage_options', 'cagov_ds_structure_options', 'cagov_ds_structure_option_page' );

	/* If Multisite instance & user is a Network Admin */
	if ( is_multisite() && current_user_can( 'manage_network_options' ) ) {
		/* If on root site */
		if ( 1 === get_current_blog_id() ) {
			/* GitHub API Key */
			// add_submenu_page( 'cagov_ds_structure_options', 'Design System Options', 'GitHub API Key', 'manage_options', 'cagov_ds_structure_api', 'cagov_ds_structure_api_menu_option_setup' );
		}

		/* Else single site instance */
	} else {
		/* GitHub API Key */
		// add_submenu_page( 'cagov_ds_structure_options', 'Design System Options', 'GitHub API Key', 'manage_options', 'cagov_ds_structure_api', 'cagov_ds_structure_api_menu_option_setup' );
	}

}

/**
 * This filter is used to switch menu order.
 *
 * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/custom_menu_order
 * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/menu_order
 * @param  mixed $menu_ord Whether custom ordering is enabled. Default false.
 * @return array|boolean
 */
function cagov_ds_structure_wpse_custom_menu_order( $menu_ord ) {
	if ( ! $menu_ord ) {
		return true;
	}

	return array(
		'index.php', // Dashboard.
		'caweb_options', // CAWeb Options.
		'cagov_ds_structure_options', // Design System Options.
		'separator1', // First separator.
		'edit.php', // Posts.
		'upload.php', // Media.
		'link-manager.php', // Links.
		'edit-comments.php', // Comments.
		'edit.php?post_type=page', // Pages.
		'separator2', // Second separator.
		'themes.php', // Appearance.
		'plugins.php', // Plugins.
		'users.php', // Users.
		'tools.php', // Tools.
		'options-general.php', // Settings.
		'separator-last', // Last separator.
	);
}


/**
 * Filters the GitHub API cagov_ds_structure_password option before its value is updated.
 *
 * @link https://developer.wordpress.org/reference/hooks/pre_update_site_option_option/
 *
 * @param  mixed $value New value of the network option.
 * @param  mixed $old_value Old value of the network option.
 * @param  mixed $option Option name.
 *
 * @return string
 */
function cagov_ds_structure_pre_update_site_option_cagov_ds_structure_password( $value, $old_value, $option ) {
	$pwd = $value;

	if ( base64_decode( $value ) === $old_value ) {
		$pwd = $old_value;
	}

	return $pwd;
}


/**
 * Setup caGov Design System Options Menu
 *
 * @return void
 */
function cagov_ds_structure_option_page() {
	// if saving.
	if ( isset( $_POST['cagov_ds_structure_options_submit'], $_POST['cagov_ds_structure_options_nonce'] ) &&
	wp_verify_nonce( sanitize_key( $_POST['cagov_ds_structure_options_nonce'] ), 'cagov_ds_structure_options' ) ) {
		cagov_ds_structure_save_options( $_POST );
	}

	// Design System Options Page Nonce.
	$nonce = wp_create_nonce( 'cagov_ds_structure_options' );

	// Selected Tab.
	$selected_tab = isset( $_POST['tab_selected'] ) ? sanitize_text_field( wp_unslash( $_POST['tab_selected'] ) ) : 'general';

	// Get User Profile Color.
	$user_color = cagov_ds_structure_get_user_color()->colors[2];

	$color_scheme      = get_option( 'cagov_ds_structure_colorscheme' );
	$available_schemes = cagov_ds_structure_color_schemes( 'displayname' );

	$navigation_menu = get_option( 'cagov_ds_structure_navigation_menu', 'singlelevel' );

	?>
	<form id="cagov-ds-structure-options-form" action="<?php print esc_url( admin_url( 'admin.php?page=cagov_ds_structure_options' ) ); ?>" method="POST" enctype="multipart/form-data">
		<input type="hidden" id="tab_selected" name="tab_selected" value="<?php print esc_attr( $selected_tab ); ?>">
		<input type="hidden" name="cagov_ds_structure_options_nonce" value="<?php print esc_attr( $nonce ); ?>" />
		<!-- Colorscheme Row -->
		<div class="form-row">
			<div class="form-group col-sm-5">
				<label for="cagov_ds_structure_colorscheme" class="d-block mb-0"><strong>Color Scheme</strong></label>
				<small class="mb-2 text-muted d-block">Apply a site-wide color scheme.</small>
			    <select id="cagov_ds_structure_colorscheme" name="cagov_ds_structure_colorscheme" class="w-50 form-control">
					<?php
						foreach ( $available_schemes as $key => $data ) {
							$selected = $key === $color_scheme ? ' selected="selected"' : '';
					?>
					<option value="<?php print esc_attr( $key ); ?>"
						<?php print esc_attr( $selected ); ?>>
						<?php print esc_attr( $data ); ?>
					</option>
					<?php
						}
					?>
				</select>
			</div>
		</div>
		<!-- Header Menu Type Row -->
		<div class="form-row">
			<div class="form-group col-sm-5">
				<label for="cagov_ds_structure_navigation_menu" class="d-block mb-0"><strong>Header Menu Type</strong></label>
				<small class="mb-2 text-muted d-block">Set a menu style for all pages.</small>
				<select id="cagov_ds_structure_navigation_menu" name="cagov_ds_structure_navigation_menu" class="w-50 form-control">
					<option value="singlelevel" <?php print 'singlelevel' === $navigation_menu ? 'selected="selected"' : ''; ?>>Single Level</option>
					<option value="dropdown" <?php print 'dropdown' === $navigation_menu ? 'selected="selected"' : ''; ?>>Drop down</option>
				</select>
			</div>
		</div>

		<input type="submit" name="cagov_ds_structure_options_submit" class="button button-primary" value="Save Changes">
	</form>
	<?php
}

/**
 * Setup caGov Design System API Menu
 *
 * @return void
 */
function cagov_ds_structure_api_menu_option_setup() {
	// if saving.
	if ( isset( $_POST['cagov_ds_structure_api_options_submit'], $_POST['cagov_ds_structure_theme_api_options_nonce'] ) &&
	wp_verify_nonce( sanitize_key( $_POST['cagov_ds_structure_theme_api_options_nonce'] ), 'cagov_ds_structure_theme_api_options' ) ) {
		cagov_ds_structure_save_api_options( $_POST, $_FILES );
	}

	// caGov Design System API Nonce.
	$cagov_ds_structure_nonce      = wp_create_nonce( 'cagov_ds_structure_theme_api_options' );
	$privated_enabled = get_site_option( 'cagov_ds_structure_private_theme_enabled', false ) ? ' checked' : '';
	$username         = get_site_option( 'cagov_ds_structure_username', 'CA-CODE-Works' );
	$password         = get_site_option( 'cagov_ds_structure_password', '' );
	?>
	<form id="cagov-ds-structure-api-options-form" action="<?php print esc_url( admin_url( 'admin.php?page=cagov_ds_structure_api' ) ); ?>" method="POST">
		<input type="hidden" name="cagov_ds_structure_theme_api_options_nonce" value="<?php print esc_attr( $cagov_ds_structure_nonce ); ?>" />
		<h2>GitHub API Key</h2>
		<div class="form-row">
			<div class="form-group col-sm-5">
				<label for="cagov_ds_structure_private_theme_enabled">Is Private?</label>
				<input type="checkbox" name="cagov_ds_structure_private_theme_enabled" class="form-control" size="50"<?php print esc_attr( $privated_enabled ); ?>/>
				<small class="text-muted d-block">Is this theme hosted as a private repo?</small>
			</div>
		</div>
		<div class="form-row">
			<div class="form-group col-sm-5">
				<label for="cagov_ds_structure_username" class="d-block mb-0">Username</label>
				<small class="text-muted">Setting this feature enables us to update the theme through GitHub</small>
				<input type="text" name="cagov_ds_structure_username" class="form-control" size="50" value="<?php print esc_attr( $username ); ?>" placeholder="Default: CA-CODE-Works" />
			</div>
		</div>
		<div class="form-row">
			<div class="form-group col-sm-5">
				<label for="cagov_ds_structure_password" class="d-block mb-0">Token</label>
				<small class="text-muted">Setting this feature enables us to update the theme through GitHub</small>
				<input type="password" class="form-control" name="cagov_ds_structure_password" size="50" value="<?php print esc_attr( $password ); ?>" />
			</div>
		</div>
		<input type="submit" name="cagov_ds_structure_api_options_submit" id="submit" class="button button-primary" value="Save Changes" />
	</form>
	<?php
}

/**
 * Save caGov Design System Options
 *
 * @param  array $values caGov Design System option values.
 * @param  array $files caGov Design System files being uploaded.
 *
 * @return void
 */
function cagov_ds_structure_save_options( $values = array() ) {
	/* Remove unneeded values */
	unset( $values['tab_selected'], $values['cagov_ds_structure_options_submit'] );

	/* Save caGov Design System Options */
	foreach ( $values as $opt => $val ) {
		switch ( $opt ) {
			default:
				if ( 'on' === $val ) {
					$val = true;
				}
		}

		update_option( $opt, $val );
	}

	print '<div class="updated notice is-dismissible"><p><strong>Design System Options</strong> have been updated.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
}

/**
 * Save API Values
 *
 * @param  mixed $values caGov Design System API Values.
 *
 * @return void
 */
function cagov_ds_structure_save_api_options( $values = array() ) {
	update_site_option( 'cagov_ds_structure_private_theme_enabled', isset( $values['cagov_ds_structure_private_theme_enabled'] ) ? true : false );
	update_site_option( 'cagov_ds_structure_username', ! empty( $values['cagov_ds_structure_username'] ) ? $values['cagov_ds_structure_username'] : 'CA-CODE-Works' );
	update_site_option( 'cagov_ds_structure_password', $values['cagov_ds_structure_password'] );

	print '<div class="updated notice is-dismissible"><p><strong>API Key</strong> has been updated.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
}
