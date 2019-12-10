<?php
namespace Story4Dev\WordPress\Admin;

/**
 * SettingsGeneral
 *
 * @author JOELINJATOVO Haja
 * @version 1.0.0
 * @since 1.0.0
 */
class SettingsGeneral extends SettingsPage {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'general';
		$this->label = __( 'General', 'story4dev' );

		parent::__construct();
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {

		$settings = apply_filters(
			'story4dev_general_settings',
			array(

				array(
					'title' => __( 'General Settings', 'story4dev' ),
					'type'  => 'title',
					'desc'  => __( 'Description', 'story4dev' ),
					'id'    => 'story4dev_blog_project',
				),

                array(
                    'title'    => __( 'Projet', 'story4dev' ),
                    'desc'     => __( 'Saississez le <strong>slug</strong> du projet sur story4dev.com', 'story4dev' ),
                    'id'       => 'story4dev_project_url',
                    'type'     => 'text',
                    'default'  => 'projet-test',
                ),
				
				array(
					'type' => 'sectionend',
					'id'   => 'story4dev_blog_project',
				),

			)
		);

		return apply_filters( 'story4dev_get_settings_' . $this->id, $settings );
	}
}