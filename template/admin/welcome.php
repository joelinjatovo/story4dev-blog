<?php
/**
 * Admin View: Dashboard
 *
 * @package WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$page_exists = isset( $tabs[ $current_page ] ) || has_action( 'story4dev_welcomes_' . $current_page );

if ( ! $page_exists ) {
	wp_safe_redirect( admin_url( 'admin.php?page=story4dev' ) );
	exit;
}
?>
<div class="wrap nexway-wrap">
    <h1><?php echo sprintf( __( 'Welcome to %s!', 'story4dev' ), 'Story4Dev Blog' ); ?></h1>
    <div class="about-text"></div>
    <form method="<?php echo esc_attr( apply_filters( 'nexway_welcomes_form_method_tab_' . $current_page, 'post' ) ); ?>" id="mainform" action="" enctype="multipart/form-data">
        <nav class="nav-tab-wrapper nxw-nav-tab-wrapper">
            <?php
                foreach ( $tabs as $slug => $label ) {
                    echo '<a href="' . esc_html( menu_page_url( esc_attr( $slug ), false ) ) . '" class="nav-tab ' . ( $current_page === $slug ? 'nav-tab-active' : '' ) . '">' . esc_html( $label ) . '</a>';
                }
                do_action( 'story4dev_welcomes_tabs' );
            ?>
        </nav>
        <?php
            self::show_messages();

            do_action( 'story4dev_welcomes_' . $current_page );
        ?>
    </form>
</div>