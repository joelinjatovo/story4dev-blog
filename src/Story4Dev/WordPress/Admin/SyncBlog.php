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
    
    const WEB_URL = 'https://story4dev.com';

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
					'title' => __( 'Synchronisation Story4DEv vers Blog', 'story4dev' ),
					'type'  => 'title',
					'desc'  => __( 'Allow to sync wordpress blog catalogue with Story4Dev report.', 'story4dev' ).
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
					'default'         => '',
					'type'            => 'text',
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
        global $wpdb;
        
        $url = self::WEB_URL.'/feed/json/'.$slug;
        $curl = \curl_init();
        \curl_setopt($curl, CURLOPT_URL, $url);
        \curl_setopt($curl, CURLOPT_FAILONERROR, true);
        \curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        \curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        \curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);  
        $result = \curl_exec($curl);
        
        $response = json_decode($result);
        
        if(isset($response->data) && is_array($response->data)){
            foreach($response->data as $report){
                $post = $wpdb->get_row("SELECT * FROM {$wpdb->postmeta} WHERE meta_key = '_report_id' AND  meta_value = '{$report->id}' LIMIT 1");
                
                $title = $report->title;
                $description = $this->getDescription($report);
                $gmt_date = $report->synced_at;
                if(!$post){
                    $post_args = array(
                        'post_title'    => $title,
                        'post_content'  => $description,
                        'post_date'     => \get_date_from_gmt( $gmt_date ),
                        'post_date_gmt' => $gmt_date,
                        'post_type'	    => 'post',
                        'post_status'   => 'publish',
                    );
                    $post_id = \wp_insert_post($post_args);
                    \update_post_meta($post_id, '_report_id', $report->id);
                }else{
                    $gmt_date = $report->synced_at;
                    $post_args = array(
                        'ID'            => $post->post_id,
                        'post_title'    => $title,
                        'post_content'  => $description,
                        'post_date'     => \get_date_from_gmt( $gmt_date ),
                        'post_date_gmt' => $gmt_date
                    );
                    \wp_update_post($post_args);
                }
            }
        }
    }
    
    public function getDescription($report) {
        $description = $report->description;
        
        $description .= '<div class="results">';
            $description .= '<h3>Résultats</h3>';
            $description .= '<ul class="report-results">';
            if(isset($report->results) && is_array($report->results)){
                foreach($report->results as $result){
                    $indicator = $result->indicator;
                    if($indicator){
                        $description .= '<li>';
                            $description .= $indicator->title;
                        
                            $unit = $indicator->unit;
                            if($unit){
                                $description .= '( ' . $result->value . ' ' . $unit->title . ' )';
                            }
                        $description .= '</li>';
                    }
                }
            }
            $description .= '</ul>';
        $description .= '<div>';
        
        $description .= '<div class="files">';
            $description .= '<h3>Documents</h3>';
            $description .= '<ul class="report-files">';
            if(isset($report->reportFiles) && is_array($report->reportFiles)){
                foreach($report->reportFiles as $reportFile){
                    $file = $reportFile->file;
                    if($file){
                        $description .= '<li>';
                            //$description .= '<a href="'.WEB_URL.'/download/'.$file->id.'" target="_blank"></a>';
                            $description .= '<a href="'.self::WEB_URL.'/uploads/file/'.$file->name.'" target="_blank">'.$file->name.'</a>';
                        $description .= '</li>';
                    }
                }
            }
            $description .= '</ul>';
        $description .= '<div>';

        return $description;
    }
}