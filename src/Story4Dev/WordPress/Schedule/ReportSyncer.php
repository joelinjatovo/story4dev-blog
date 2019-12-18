<?php
namespace Story4Dev\WordPress\Schedule;

use Story4Dev\Interfaces\HooksInterface;

use Story4Dev\WordPress\Helpers\PostType;
use Story4Dev\Models\Importer\CatalogXmlImporter;
use Story4Dev\Models\App\Config;
use Story4Dev\Models\App\Product;

/**
 * ReportSyncer
 *
 * @author JOELINJATOVO Haja
 * @version 1.0.0
 * @since 1.0.0
 */
class ReportSyncer extends BaseCron{
    
    const WEB_URL = 'https://story4dev.com';
    
    public function __construct(){
        parent::__construct();
    }
    
    /**
    * @Override
    *
    * @see Story4Dev\WordPress\Schedule\BaseCron
    */
    protected function getIndex(){
        return '20-mins';
    }
    
    /**
    * @Override
    *
    * @see Story4Dev\WordPress\Schedule\BaseCron
    */
    protected function getAction(){
        return 'story4dev_report_syncer';
    }
    
    /**
    * @Override
    *
    * @see Story4Dev\WordPress\Schedule\BaseCron
    */
    protected function getInterval(){
        return 20 * 60; // 60 seconds * 20 minutes = 1/3 hour
    }
    
    /**
    * @Override
    *
    * @see Story4Dev\WordPress\Schedule\BaseCron
    */
    protected function getLabel(){
        return __('Once every 20 minutes', 'story4dev');
    }
    
    /**
    * @Override
    *
    * Function to run when scheduling
    */
    public function execute(){
        parent::execute();
        
        set_time_limit(0);
        ignore_user_abort(true);
        
        try{
            $response = $this->getData();
            s4d_log('response', $response);
            $result = $this->import($response);
        }catch(\Exception $e){
            s4d_log('exception', $e->getMessage());
        }
    }
    
    public function getData() {
        $url = $this->getFeedUrl();
        
        if( ! function_exists('curl_init') ) {
            throw new \Exception('PHP Curl required');
        }
        
        $curl = \curl_init();
        if ( ! $curl ) {
            throw new \Exception("CURL failed to initialize ");
        }
        
        \curl_setopt($curl, CURLOPT_URL, $url);
        \curl_setopt($curl, CURLOPT_FAILONERROR, true);
        \curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        \curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        \curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);  
        \curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Cache-Control: no-cache"));
        $response = \curl_exec($curl);
        
        //If there was an error, throw an Exception
        if( curl_errno( $curl ) ){
            throw new \Exception( curl_error( $curl ) );
        }
        
        //Get the HTTP status code.
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        //Close the cURL handler.
        curl_close($curl);
        
        // check response code
        if($httpcode != 200){
            throw new \Exception("HTTP Status Code: " . $httpcode);
        }
        
        // check response
        if (false === $response ) {
            throw new \Exception("CURL Failed ");
        }

        // check response is valid json
        $response = json_decode($response);
        if ( ! is_object( $response ) ) {
            throw new \Exception("Data format not supported.");
        }
        
        return $response;
    }
    
    public function import($response) {
        global $wpdb;
        $new = $update = 0;
        
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
                    $new++;
                }else{
                    $post_id   = $post->post_id;
                    $gmt_date  = $report->synced_at;
                    $post_args = array(
                        'ID'            => $post_id,
                        'post_title'    => $title,
                        'post_content'  => $description,
                        'post_date'     => \get_date_from_gmt( $gmt_date ),
                        'post_date_gmt' => $gmt_date
                    );
                    \wp_update_post($post_args);
                    $update++;
                }
                
                $this->addAttachment($post_id, $report);
            }
        }
        
        return ['new'=>$new, 'update'=>$update];
    }
    
    private function getDescription($report) {
        $description = '<div class="s4d-description">';
            $description .= $report->description;
        $description .= '</div>';
        
        $resultHtml = '';
            if(isset($report->results) && is_array($report->results)){
                foreach($report->results as $result){
                    $indicator = $result->indicator;
                    if($indicator){
                        $resultHtml .= '<li>';
                            $resultHtml .= $indicator->title;

                            $unit = $indicator->unit;
                            if($unit){
                                $resultHtml .= '( ' . $result->value . ' ' . $unit->title . ' )';
                            }
                        $resultHtml .= '</li>';
                    }
                }
            }
            if( !empty($resultHtml) ) {
                $description .= '<div class="s4d-results">';
                    $description .= '<h5>Résultats</h5>';
                    $description .= '<ul class="report-results">';
                        $description .= $resultHtml;
                    $description .= '</ul>';
                $description .= '</div>';
            }
        
        $documentHtml = '';
            if(isset($report->reportFiles) && is_array($report->reportFiles)){
                foreach($report->reportFiles as $reportFile){
                    $file = $reportFile->file;
                    if($file){
                        $url = $this->getFileUrl($file);
                        $filename = basename( $url );
                        $wp_filetype = wp_check_filetype( $filename, null );
                        if( strpos($wp_filetype['type'], 'image/') === false ){
                            $documentHtml .= '<li>';
                                $documentHtml .= '<a href="'.$this->getFileUrl($file).'">'.$file->displayName.'</a>';
                            $documentHtml .= '</li>';
                        }
                    }
                }
            }
            if( !empty($documentHtml) ) {
                $description .= '<div class="s4d-files">';
                    $description .= '<h5>Documents</h5>';
                    $description .= '<ul class="report-files">';
                        $description .= $documentHtml;
                    $description .= '</ul>';
                $description .= '</div>';
            }
        
        $gallery = '';
            if(isset($report->reportFiles) && is_array($report->reportFiles)){
                foreach($report->reportFiles as $reportFile){
                    $file = $reportFile->file;
                    if($file){
                        $url = $this->getFileUrl($file);
                        $filename = basename( $url );
                        $wp_filetype = wp_check_filetype( $filename, null );
                        if( strpos($wp_filetype['type'], 'image/') === false ){
                            continue;
                        }
                        $gallery .= '<li class="blocks-gallery-item">';
                            $gallery .= '<figure>';
                                $gallery .= '<img src="'.$url.'" alt="" data-id="'.$file->id.'" data-full-url="'.$url.'" data-link="'.$url.'" class="wp-image-'.$file->id.'"/>';
                            $gallery .= '</figure>';
                        $gallery .= '</li>';
                    }
                }
            }
            if( !empty($gallery) ) {
                $description .= '<div class="s4d-gallery">';
                    $description .= '<h5>Images</h5>';
                    $description .= '<figure class="wp-block-gallery columns-3 is-cropped">';
                        $description .= '<ul class="blocks-gallery-grid">';
                            $description .= $gallery;
                        $description .= '</ul>';
                    $description .= '</figure>';
                $description .= '</div>';
            }
        
        if( !empty($report->latitude) && !empty($report->longitude) ) {
            $description .= '<div class="s4d-maps">';
                $description .= '<h5>Localisation</h5>';
                $description .= '<iframe src="https://maps.google.com/maps?q='.$report->latitude.','.$report->longitude.'&hl=fr;z=14&output=embed" width="100%" height="450" frameborder="0" style="border:0;" allowfullscreen=""></iframe>';
            $description .= '</div>';
        }

        return $description;
    }
    
    private function getUrl(){
        return self::WEB_URL;
    }
    
    private function getFileUrl($file){
        return $this->getUrl().'/uploads/file/'.$file->name;
    }
    
    private function getFeedUrl(){
        $slug = \get_option('story4dev_project_url', true);
        if(empty($slug)){
            throw new \Exception('Need to setup');
        }
        return $this->getUrl().'/feed/json/'.$slug;
    }
    
    private function addAttachment($post_id, $report){
        if ( has_post_thumbnail( $post_id )
                || !isset($report->reportFiles)
                || !is_array($report->reportFiles)
                || empty($report->reportFiles) ){
            return;
        }
        
        foreach($report->reportFiles as $reportFile){
            $file = $reportFile->file;
            
            $url = $this->getFileUrl($file);

            $filename = basename( $url );
            $wp_filetype = wp_check_filetype( $filename, null );
            
            if( strpos($wp_filetype['type'], 'image/') === false ){
                continue;
            }

            $upload_dir = wp_upload_dir();
            if ( wp_mkdir_p( $upload_dir['path'] ) ) {
                $file_path = $upload_dir['path'] . '/' . $filename;
            } else {
                $file_path = $upload_dir['basedir'] . '/' . $filename;
            }

            $image_data = file_get_contents( $url );
            file_put_contents( $file_path, $image_data );


            $attachment = array(
                'guid'           => $upload_dir['url'] . '/' . $filename, 
                'post_mime_type' => $wp_filetype['type'],
                'post_title'     => sanitize_file_name( $filename ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );

            $attach_id = wp_insert_attachment( $attachment, $file_path );

            require_once( ABSPATH . 'wp-admin/includes/image.php' );

            $attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );

            wp_update_attachment_metadata( $attach_id, $attach_data );
            
            set_post_thumbnail( $post_id, $attach_id );
            
            return;
        }
    }
}