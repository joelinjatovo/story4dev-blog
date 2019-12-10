<?php
namespace Story4Dev\WordPress\Admin;

/**
 * Dashboard
 *
 * @author JOELINJATOVO Haja
 * @version 1.0.0
 * @since 1.0.0
 */
class Dashboard extends WelcomePage {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'story4dev';
		$this->label = __( 'Dashboard', 'story4dev' );
		parent::__construct();
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_welcomes() {

		$welcomes = apply_filters(
			'story4dev_dashboard_welcomes',
			array(

				array(
					'type'  => 'title',
					'title' => __( 'Remarque', 'story4dev' ),
					'desc'  => sprintf(
                        __( 'Tous les rapports de votre projet <code>%s</code> sur story4dev.com seront importés dans ce blog tous les 20 minutes.', 'story4dev' ),
                        'https://story4dev.com/p/'.get_option('story4dev_project_url', 'slug').'/reports'
                        ),
					'id'    => 'story4dev_dashboard',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'story4dev_dashboard',
				),

			)
		);

		return apply_filters( 'story4dev_get_welcomes_' . $this->id, $welcomes );
	}
}