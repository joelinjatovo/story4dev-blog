<?php
namespace Story4Dev\WordPress\Enqueue;

use Story4Dev\Interfaces\HooksInterface;

/**
 * Script
 *
 * @author JOELINJATOVO Haja
 * @version 1.0.0
 * @since 1.0.0
 */
class Script implements HooksInterface{

    /**
     * @see WpPerDim\Interfaces\HooksInterface
     */
    public function hooks(){
        add_action("wp_enqueue_scripts", array($this, 'front_script'), PHP_INT_MAX);
        add_action("admin_enqueue_scripts", array($this, 'admin_script'), PHP_INT_MAX);
    }
    
    public function front_script(){
    }
    
    public function admin_script(){
    }
}