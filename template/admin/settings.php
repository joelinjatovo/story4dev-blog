<?php
/**
 * Admin View: Settings
 *
 * @package WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tab_exists        = isset( $tabs[ $current_nexway_tab ] ) || has_action( 's4d_settings_' . $current_nexway_tab );
$current_nexway_tab_label = isset( $tabs[ $current_nexway_tab ] ) ? $tabs[ $current_nexway_tab ] : '';

if ( ! $tab_exists ) {
	wp_safe_redirect( admin_url( 'admin.php?page=s4d-settings' ) );
	exit;
}
?>
<div class="wrap nexway-wrap">
    <h1><?php echo sprintf( __( '%s Settings', 's4d' ), 'Story4Dev' ); ?></h1>
    <div class="about-text"></div>
	<form method="<?php echo esc_attr( apply_filters( 's4d_settings_form_method_tab_' . $current_nexway_tab, 'post' ) ); ?>" id="mainform" action="" enctype="multipart/form-data">
		<nav class="nav-tab-wrapper nxw-nav-tab-wrapper">
			<?php

			foreach ( $tabs as $slug => $label ) {
				echo '<a href="' . esc_html( admin_url( 'admin.php?page=s4d-settings&tab=' . esc_attr( $slug ) ) ) . '" class="nav-tab ' . ( $current_nexway_tab === $slug ? 'nav-tab-active' : '' ) . '">' . esc_html( $label ) . '</a>';
			}

			do_action( 's4d_settings_tabs' );

			?>
		</nav>
        
		<h1 class="screen-reader-text"><?php echo esc_html( $current_nexway_tab_label ); ?></h1>
        
		<?php
			self::show_messages();

			do_action( 's4d_settings_' . $current_nexway_tab );
		?>
        
		<p class="submit">
			<?php if ( empty( $GLOBALS['hide_save_button'] ) ) : ?>
				<button name="save" class="button-primary nexway-save-button" type="submit" value="<?php esc_attr_e( 'Save changes', 's4d' ); ?>"><?php esc_html_e( 'Save changes', 'woocommerce' ); ?></button>
			<?php endif; ?>
			<?php wp_nonce_field( 's4d-settings' ); ?>
		</p>
	</form>
</div>
