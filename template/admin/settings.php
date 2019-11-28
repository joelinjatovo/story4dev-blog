<?php
/**
 * Admin View: Settings
 *
 * @package WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tab_exists        = isset( $tabs[ $current_story4dev_tab ] ) || has_action( 'story4dev_settings_' . $current_story4dev_tab );
$current_story4dev_tab_label = isset( $tabs[ $current_story4dev_tab ] ) ? $tabs[ $current_story4dev_tab ] : '';

if ( ! $tab_exists ) {
	wp_safe_redirect( admin_url( 'admin.php?page=story4dev-settings' ) );
	exit;
}
?>
<div class="wrap story4dev-wrap">
    <h1><?php echo sprintf( __( '%s Settings', 'story4dev' ), 'Story4Dev' ); ?></h1>
    <div class="about-text"></div>
	<form method="<?php echo esc_attr( apply_filters( 'story4dev_settings_form_method_tab_' . $current_story4dev_tab, 'post' ) ); ?>" id="mainform" action="" enctype="multipart/form-data">
		<nav class="nav-tab-wrapper nxw-nav-tab-wrapper">
			<?php

			foreach ( $tabs as $slug => $label ) {
				echo '<a href="' . esc_html( admin_url( 'admin.php?page=story4dev-settings&tab=' . esc_attr( $slug ) ) ) . '" class="nav-tab ' . ( $current_story4dev_tab === $slug ? 'nav-tab-active' : '' ) . '">' . esc_html( $label ) . '</a>';
			}

			do_action( 'story4dev_settings_tabs' );

			?>
		</nav>
        
		<h1 class="screen-reader-text"><?php echo esc_html( $current_story4dev_tab_label ); ?></h1>
        
		<?php
			self::show_messages();

			do_action( 'story4dev_settings_' . $current_story4dev_tab );
		?>
        
		<p class="submit">
			<?php if ( empty( $GLOBALS['hide_save_button'] ) ) : ?>
				<button name="save" class="button-primary story4dev-save-button" type="submit" value="<?php esc_attr_e( 'Save changes', 'story4dev' ); ?>"><?php esc_html_e( 'Save changes', 'woocommerce' ); ?></button>
			<?php endif; ?>
			<?php wp_nonce_field( 'story4dev-settings' ); ?>
		</p>
	</form>
</div>
