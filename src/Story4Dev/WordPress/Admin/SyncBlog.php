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
		$this->label = __( 'Synchronisation', 'story4dev' );
		parent::__construct();
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_welcomes() {
        
		$welcomes = apply_filters(
			'story4dev_syncblog_welcomes',
			array(

				array(
					'title' => __( 'Synchronisation des rapports sur Story4Dev.com vers un blog', 'story4dev' ),
					'type'  => 'title',
					'desc'  => __( 'Permettant d\'ajouter les rapports dans les articles de ce blog.', 'story4dev' ),
					'id'    => 'story4dev_sync',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'story4dev_sync',
				),

				array(
					'type' => 'submit',
					'title' => __( 'Synchroniser maintenant', 'story4dev' ),
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
        //$value = Welcome::get_post('story4dev_project_slug', false);
        //if($value !== false) {
            try{
                $result = 5;
                
                $model = new \Story4Dev\WordPress\Schedule\ReportSyncer();
                $response = $model->getData();
                $result = $model->import($response);
                
                Welcome::add_message( sprintf( _n( '%s new product imported.', '%s new products imported.', $result, 'story4dev' ), $result ) );
            }catch(\Exception $e){
                Welcome::add_error( __( 'An error has occured when syncing.', 'story4dev' ).' '.$e->getMessage() );
            }
        //}
    }
}