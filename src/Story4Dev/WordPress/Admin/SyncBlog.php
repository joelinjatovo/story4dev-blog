<?php
namespace Story4Dev\WordPress\Admin;

/**
 * SyncBlog
 *
 * @author JOELINJATOVO Haja
 * @version 1.0.0
 * @since 1.0.0
 */
class SyncBlog extends WelcomePage{

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'story4dev-sync-blog';
		$this->label = __( 'Créer Article', 'story4dev' );
		parent::__construct();
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_welcomes() {
        
        global $wpdb;
        
        $table_name = $wpdb->prefix.'story4dev_products';
        $row = $wpdb->get_row("SELECT count(story4dev_product_id) as nbr FROM $table_name WHERE wp_post_id IS NULL;");
        $count = (int) $row->nbr;

		$welcomes = apply_filters(
			'story4dev_syncwoo_welcomes',
			array(

				array(
					'title' => __( 'Synchronisation Database vers WooProduct', 'story4dev' ),
					'type'  => 'title',
					'desc'  => __( 'Allow to sync story4dev product catalogue with WooCommerce Product.', 'story4dev' ).
                                ' <br>' . sprintf( _n( 'You have %s new catalogue to synchronize.', 'You have %s new catalogues to synchronize.', $count, 'story4dev' ), number_format_i18n( $count ) ),
					'id'    => 'story4dev_sync',
				),

				array(
					'id'              => 'story4dev-table',
					'type'            => 'table-start',
				),

				array(
					'title'           => __( 'Project Slug', 'story4dev' ),
					'desc'            => __( 'Lien url du projet sur Story4Dev', 'story4dev' ),
					'id'              => 'story4dev_project_slug',
					'default'         => 'no',
					'type'            => 'checkbox',
					'show_if_checked' => 'option',
				),

				array(
					'id'              => 'story4dev-end',
					'type'            => 'table-end',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'story4dev_sync',
				),

				array(
					'type' => 'submit',
					'title' => __( 'Run Sync Now', 'story4dev' ),
					'id'   => 'story4dev_submit',
				),

			)
		);

		return apply_filters( 'story4dev_get_welcomes_' . $this->id, $welcomes );
	}

    /**
     * Run POST request.
     * Override
     */
    public function save() {
        $value = Welcome::get_post('story4dev_project_slug', false);
        if($value !== false) {
            try{
                self::import($value);
                $result = 5;
                Welcome::add_message( sprintf( _n( '%s new product imported.', '%s new products imported.', $result, 'story4dev' ), $result ) );
            }catch(\Exception $e){
                Welcome::add_error( __( 'An error has occured when syncing.', 'story4dev' ).' '.$e->getMessage() );
            }
        }
    }
    
    public function import($slug) {
    }
}