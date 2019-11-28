<?php
namespace Story4Dev\WordPress\Schedule;

use Story4Dev\Interfaces\HooksInterface;

use Story4Dev\WordPress\Helpers\PostType;

/**
 * BaseCron
 *
 * @author JOELINJATOVO Haja
 * @version 1.0.0
 * @since 1.0.0
 */
abstract class BaseCron implements HooksInterface{
    
    public function __construct(){
        add_action('init', array($this, 'activation'));
        register_activation_hook(S4D_FILE,   array($this, 'activation'));
        register_deactivation_hook(S4D_FILE, array($this, 'deactivation'));
    }
    
    /**
    * Action to hook when activating
    */
    public function activation(){
        if( ! wp_next_scheduled( $this->getActionName() ) ){				
            wp_schedule_event( time(), $this->getIndex(), $this->getActionName() );
        }
    }
    
    /**
    * Action to hook when deactivating
    */
    public function deactivation(){
        wp_clear_scheduled_hook( $this->getActionName() );
    }
    
    /**
     * @see Story4Dev\Interfaces\HooksInterface
     */
    public function hooks(){
        add_filter('cron_schedules', array($this, 'cron_schedules'));
        add_action($this->getActionName(), array($this, 'execute'));
    }
    
    /**
    * Filter schedule time
    */
    public function cron_schedules($schedules){
        if(!isset($schedules[$this->getIndex()])){
            $schedules[$this->getIndex()] = array(
                'interval' => $this->getInterval(),
                'display'  => $this->getLabel()
            );
        }
        return $schedules;
    }
    
    /**
    * Function to run when scheduling
    */
    public function execute(){
        // nothing
    }
    
    /**
    * Get Hook Name
    */
    public function getActionName(){
        return $this->getAction();
    }
    
    /**
    * Schedule index
    */
    abstract protected function getIndex();
    
    /**
    * Schedule Action
    */
    abstract protected function getAction();
    
    /**
    * Schedule Interval Time
    */
    abstract protected function getInterval();
    
    /**
    * Schedule Label Text
    */
    abstract protected function getLabel();
}