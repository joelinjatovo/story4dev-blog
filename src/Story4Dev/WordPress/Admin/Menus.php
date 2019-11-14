<?php
namespace Story4Dev\WordPress\Admin;

use Story4Dev\Interfaces\HooksInterface;

/**
 * Menus
 *
 * @author JOELINJATOVO Haja
 * @version 1.0.0
 * @since 1.0.0
 */
class Menus implements HooksInterface{

    /**
     * @see WpPerDim\Interfaces\HooksInterface
     */
    public function hooks(){
        add_action( "admin_menu", array($this, 'admin_menu') );
        add_action( "admin_menu", array($this, 'dashboard_menu') );
        add_action( "admin_menu", array($this, 'units_menu') );
        add_action( "admin_menu", array($this, 'trackers_menu') );
        add_action( "admin_menu", array($this, 'indicators_menu') );
        add_action( "admin_menu", array($this, 'datas_menu') );
    }
    
    public function admin_menu(){
        add_menu_page( __( 'WpPerDim', 'wppd' ) , __( 'WpPerDim', 'wppd' ), 'manage_options', 'wppd', null, plugins_url( 'wp-per-dim/assets/images/icon.png' ), 2);
    }
    
    public function dashboard_menu(){
        $welcome_page = add_submenu_page('wppd', __( 'Welcome to WpPerDim', 'wppd' ) , __( 'Welcome', 'wppd' ), 'manage_options', 'wppd', array($this, "welcome_page"), null, 0);
        add_action( 'load-' . $welcome_page, array( $this, 'welcome_page_init' ) );
    }
    
    public function units_menu(){
        $page = add_submenu_page('wppd', __( 'Liste: Unités', 'wppd' ) , __( 'Unités', 'wppd' ), 'manage_options', 'wppd-units', array($this, "units_page"), null, 0);
        add_action( 'load-' . $page, array( $this, 'welcome_page_init' ) );
    }
    
    public function trackers_menu(){
        $page = add_submenu_page('wppd', __( 'Liste: Suivis', 'wppd' ) , __( 'Suivis', 'wppd' ), 'manage_options', 'wppd-trackers', array($this, "trackers_page"), null, 0);
        add_action( 'load-' . $page, array( $this, 'welcome_page_init' ) );
    }
    
    public function indicators_menu(){
        $page = add_submenu_page('wppd', __( 'Liste: Indicateurs', 'wppd' ) , __( 'Indicateurs', 'wppd' ), 'manage_options', 'wppd-indicators', array($this, "indicators_page"), null, 0);
        add_action( 'load-' . $page, array( $this, 'welcome_page_init' ) );
    }
    
    public function datas_menu(){
        $page = add_submenu_page('wppd', __( 'Liste: Données', 'wppd' ) , __( 'Données', 'wppd' ), 'manage_options', 'wppd-datas', array($this, "datas_page"), null, 0);
        add_action( 'load-' . $page, array( $this, 'welcome_page_init' ) );
    }
    
	/**
     *
	 */
	public function welcome_page_init() {
        global $current_page;
        
		// Include welcome pages.
		Welcome::get_pages();

        // Get current page.
		$current_page = empty( $_GET['page'] ) ? 'wppd' : sanitize_title( wp_unslash( $_GET['page'] ) ); // WPCS: input var okay, CSRF ok.

		// Save welcomes if data has been posted.
		if ( apply_filters( "wppd_save_welcomes_{$current_page}", ! empty( $_POST['save'] ) ) ) { // WPCS: input var okay, CSRF ok.
			Welcome::save();
		}

		// Add any posted messages.
		if ( ! empty( $_GET['wppd_error'] ) ) { // WPCS: input var okay, CSRF ok.
			Welcome::add_error( wp_kses_post( wp_unslash( $_GET['wppd_error'] ) ) ); // WPCS: input var okay, CSRF ok.
		}

		if ( ! empty( $_GET['wppd_message'] ) ) { // WPCS: input var okay, CSRF ok.
			Welcome::add_message( wp_kses_post( wp_unslash( $_GET['wppd_message'] ) ) ); // WPCS: input var okay, CSRF ok.
		}
        
		do_action( 'wppd_welcome_page_init' );
	}
    
    public function welcome_page(){
        Welcome::output();
    }
    
    public function units_page(){
        Welcome::output();
    }
    
    public function indicators_page(){
        Welcome::output();
    }
    
    public function datas_page(){
        Welcome::output();
    }
    
    public function trackers_page(){
        Welcome::output();
    }
}