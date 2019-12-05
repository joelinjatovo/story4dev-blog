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
     * @see Story4Dev\Interfaces\HooksInterface
     */
    public function hooks(){
        add_action( "admin_menu", array($this, 'admin_menu') );
        add_action( "admin_menu", array($this, 'dashboard_menu') );
        add_action( "admin_menu", array($this, 'sync_menu') );
        add_action( "admin_menu", array($this, 'settings_menu') );
    }
    
    public function admin_menu(){
        add_menu_page( __( 'Story4Dev', 'story4dev' ) , __( 'Story4Dev', 'story4dev' ), 'manage_options', 'story4dev', null, S4D_URL . 'assets/images/icon.png', 2);
    }
    
    public function dashboard_menu(){
        $welcome_page = add_submenu_page('story4dev', __( 'Welcome to Story4Dev', 'story4dev' ) , __( 'Story4Dev', 'story4dev' ), 'manage_options', 'story4dev', array($this, "welcome_page"), null, 0);
        add_action( 'load-' . $welcome_page, array( $this, 'welcome_page_init' ) );
    }
    
    public function sync_menu(){
        $welcome_page = add_submenu_page('story4dev', __( 'Sync from Story4Dev', 'story4dev' ) , __( 'Synchronisation', 'story4dev' ), 'manage_options', 'story4dev-sync-blog', array($this, "welcome_page"), null, 0);
        add_action( 'load-' . $welcome_page, array( $this, 'welcome_page_init' ) );
    }
    
    public function settings_menu(){
        $settings_page = add_submenu_page('story4dev', __( 'Settings', 'story4dev' ) , __( 'Settings', 'story4dev' ), 'manage_options', 'story4dev-settings', array($this, "setting_page"), null, 0);
        add_action( 'load-' . $settings_page, array( $this, 'settings_page_init' ) );
    }
    
	public function welcome_page_init() {
        global $current_page;
        
		// Include welcome pages.
		Welcome::get_pages();

        // Get current page.
		$current_page = empty( $_GET['page'] ) ? 'story4dev' : sanitize_title( wp_unslash( $_GET['page'] ) ); // WPCS: input var okay, CSRF ok.

		// Save welcomes if data has been posted.
		if ( apply_filters( "story4dev_save_welcomes_{$current_page}", ! empty( $_POST['save'] ) ) ) { // WPCS: input var okay, CSRF ok.
			Welcome::save();
		}

		// Add any posted messages.
		if ( ! empty( $_GET['story4dev_error'] ) ) { // WPCS: input var okay, CSRF ok.
			Welcome::add_error( wp_kses_post( wp_unslash( $_GET['story4dev_error'] ) ) ); // WPCS: input var okay, CSRF ok.
		}

		if ( ! empty( $_GET['story4dev_message'] ) ) { // WPCS: input var okay, CSRF ok.
			Welcome::add_message( wp_kses_post( wp_unslash( $_GET['story4dev_message'] ) ) ); // WPCS: input var okay, CSRF ok.
		}
        
		do_action( 'story4dev_welcome_page_init' );
	}
    
	public function settings_page_init() {
        global $current_story4dev_tab;
        
		// Include settings pages.
		Settings::get_pages();

		// Get current tab/section.
		$current_story4dev_tab = empty( $_GET['tab'] ) ? 'general' : sanitize_title( wp_unslash( $_GET['tab'] ) ); // WPCS: input var okay, CSRF ok.

		// Save settings if data has been posted.
		if ( apply_filters( "story4dev_save_settings_{$current_story4dev_tab}", ! empty( $_POST['save'] ) ) ) { // WPCS: input var okay, CSRF ok.
			Settings::save();
		}

		// Add any posted messages.
		if ( ! empty( $_GET['story4dev_error'] ) ) { // WPCS: input var okay, CSRF ok.
			Settings::add_error( wp_kses_post( wp_unslash( $_GET['story4dev_error'] ) ) ); // WPCS: input var okay, CSRF ok.
		}

		if ( ! empty( $_GET['story4dev_message'] ) ) { // WPCS: input var okay, CSRF ok.
			Settings::add_message( wp_kses_post( wp_unslash( $_GET['story4dev_message'] ) ) ); // WPCS: input var okay, CSRF ok.
		}

		do_action( 'story4dev_settings_page_init' );
	}
    
    public function welcome_page(){
        Welcome::output();
    }
    
    public function setting_page(){
        Settings::output();
    }
}