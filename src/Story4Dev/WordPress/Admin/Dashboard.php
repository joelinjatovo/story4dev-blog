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
		$this->id    = 'wppd';
		$this->label = __( 'Welcome', 'wppd' );
		parent::__construct();
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_welcomes() {

		$welcomes = apply_filters(
			'wppd_dashboard_welcomes',
			array(

				array(
					'type'  => 'title',
					'title' => __( 'Dashboard', 'wppd' ),
					'desc'  => __( 'Description', 'wppd' ),
					'id'    => 'wppd_dashboard',
				),

				array(
					'type'  => 'notice',
					'class' => 'wppd-notice wppd-notice-important',
					//'title' => __( 'Important', 'WpPerDim' ),
					'desc'  => __( 'Notice', 'wppd' ),
					'id'    => 'dashboard-notice',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wppd_dashboard',
				),

			)
		);

		return apply_filters( 'wppd_get_welcomes_' . $this->id, $welcomes );
	}
}