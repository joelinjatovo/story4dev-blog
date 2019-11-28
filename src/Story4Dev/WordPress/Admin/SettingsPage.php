<?php
namespace Story4Dev\WordPress\Admin;

/**
 * SettingsPage
 *
 * @author JOELINJATOVO Haja
 * @version 1.0.0
 * @since 1.0.0
 */
class SettingsPage{
    /**
     * Setting page id.
     *
     * @var string
     */
    protected $id = '';

    /**
     * Setting page label.
     *
     * @var string
     */
    protected $label = '';

    /**
     * Constructor.
     */
    public function __construct() {
        add_filter( 'story4dev_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
        add_action( 'story4dev_settings_' . $this->id, array( $this, 'output' ) );
        add_action( 'story4dev_settings_save_' . $this->id, array( $this, 'save' ) );
    }

    /**
     * Get settings page ID.
     *
     * @since 3.0.0
     * @return string
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Get settings page label.
     *
     * @since 3.0.0
     * @return string
     */
    public function get_label() {
        return $this->label;
    }

    /**
     * Add this page to settings.
     *
     * @param array $pages
     *
     * @return mixed
     */
    public function add_settings_page( $pages ) {
        $pages[ $this->id ] = $this->label;

        return $pages;
    }

    /**
     * Get settings array.
     *
     * @return array
     */
    public function get_settings() {
        return apply_filters( 'story4dev_get_settings_' . $this->id, array() );
    }

    /**
     * Output the settings.
     */
    public function output() {
        $settings = $this->get_settings();
        
        Settings::output_fields( $settings );
    }

    /**
     * Save settings.
     */
    public function save() {
        $settings = $this->get_settings();
        
        Settings::save_fields( $settings );
    }
}