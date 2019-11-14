<?php
namespace Story4Dev;

/**
 * Database
 *
 * @author JOELINJATOVO
 * @version 1.0.0
 * @since 1.0.0
 */
class Database{
    
    public function saveOptions(){
        update_option('enable_wp_nonce_validation', 1);
    }
    
    public function unsaveOptions(){
        delete_option('enable_wp_nonce_validation');
    }
    
    public function install()
    {
        ob_start();
        global $wpdb;
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $table_name = $wpdb->prefix . "s4d_units";	   		
        $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
            `id`          BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `title`       VARCHAR(255) COLLATE utf8mb4_unicode_520_ci,
            `label`       VARCHAR(255) COLLATE utf8mb4_unicode_520_ci
        );";
        $wpdb->query($sql);
        echo "Error $table_name: $wpdb->last_error \n";
        
        $log = ob_get_contents();
        ob_end_clean();
        $file = S4D_DIR . "log/activation.log";
        if(file_exists($file)){
            $current = file_get_contents($file);	
        }else{
            $current = '';
        }
        $current .= "\n====START: ".date("d/m/Y H:i:s")." IP=". $_SERVER['REMOTE_ADDR'] . " ====\n";
        $current .= $log;
        $current .= "\n====END: \n";
        file_put_contents($file,$current);
        
    }   
    
    public function uninstall() {
        global $wpdb;
        $table_names = [
            //$wpdb->prefix . 'nxw_categories',
        ];
        foreach($table_names as $table_name){
            $sql = "DROP TABLE IF EXISTS $table_name";
            $wpdb->query($sql);
        }
    }   
}