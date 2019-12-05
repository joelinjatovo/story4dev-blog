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
		$this->label = __( 'Welcome', 'story4dev' );
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
					'title' => __( 'Dashboard', 'story4dev' ),
					'desc'  => __( 'Description', 'story4dev' ),
					'id'    => 'story4dev_dashboard',
				),

				array(
					'type'  => 'notice',
					'class' => 'story4dev-notice story4dev-notice-important',
					//'title' => __( 'Important', 'story4dev' ),
					'desc'  => __( 'Notice', 'story4dev' ),
					'id'    => 'dashboard-notice',
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