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
                    $gmt_date = $report->synced_at;
                    $post_args = array(
                        'ID'            => $post->post_id,
                        'post_title'    => $title,
                        'post_content'  => $description,
                        'post_date'     => \get_date_from_gmt( $gmt_date ),
                        'post_date_gmt' => $gmt_date
                    );
                    \wp_update_post($post_args);
                    $update++;
                }
            }
        }
        
        return ['new'=>$new, 'update'=>$update];
    }
    
    private function getDescription($report) {
        $description = '<div class="s4d-description">';
            $description .= $report->description;
        $description .= '</div>';
        
        $description .= '<div class="s4d-results">';
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
        $description .= '</div>';
        
        $description .= '<div class="s4d-files">';
            $description .= '<h3>Documents</h3>';
            $description .= '<ul class="report-files">';
            if(isset($report->reportFiles) && is_array($report->reportFiles)){
                foreach($report->reportFiles as $reportFile){
                    $file = $reportFile->file;
                    if($file){
                        $description .= '<li>';
                            //$description .= '<a href="'.WEB_URL.'/download/'.$file->id.'" target="_blank"></a>';
                            $description .= '<a href="'.$this->getFileUrl($file).'">'.$file->displayName.'</a>';
                        $description .= '</li>';
                    }
                }
            }
            $description .= '</ul>';
        $description .= '</div>';
        
        $description .= '<div class="s4d-maps">';
            $description .= '<iframe src="https://maps.google.com/maps?q='.$report->latitude.','.$report->longitude.'&hl=fr;z=14&output=embed" width="100%" height="450" frameborder="0" style="border:0;" allowfullscreen=""></iframe>';
        $description .= '</div>';

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
}