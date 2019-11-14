<?php
namespace Story4Dev;

if ( ! defined( 'ABSPATH' ) ) exit;

use Story4Dev\Interfaces\HooksInterface;
use Story4Dev\Interfaces\HooksFrontInterface;
use Story4Dev\Interfaces\HooksAdminInterface;
use Story4Dev\Systems\View;

/**
 * Story4Dev
 *
 * @author JOELINJATOVO Haja
 * @version 1.0.0
 * @since 1.0.0
 */
class Story4Dev implements HooksInterface{
    
    /**
     * define if already initiated
     * @var bool
     */
    public static $initiated;
    
    /**
    * @var View
    */
    public $view;

    /**
    * @var Database
    */
    protected $database;
    
    /**
    * @var Action Hook
    */
    protected $actions = array();

    /**
     * @param array $actions
     */
    public function __construct($actions = array(), $config = array()){
        $this->actions = $actions;
        $this->database = new Database();
        $this->view = new View();
    }

    /**
     * @return boolean
     */
    protected function canBeLoaded(){
        return true;
    }

    /**
     * Execute plugin
     */
    public function execute($file){
        register_activation_hook($file, array($this->database, 'install'));
        register_activation_hook($file, array($this->database, 'saveOptions'));
        
        register_deactivation_hook($file, array($this->database, 'uninstall'));
        register_deactivation_hook($file, array($this->database, 'unsaveOptions'));
        
        if ($this->canBeLoaded($file)){
            add_action('plugins_loaded', array($this, 'hooks'), 0);
        }
    }

    /**
     * @return array
     */
    public function getActions(){
        return $this->actions;
    }

    /**
     * Implements hooks from HooksInterface
     *
     * @see Todolist\Models\HooksInterface
     *
     * @return void
     */
    public function hooks(){
        foreach ($this->getActions() as $key => $action) {
            switch(true) {  // Cela m'évite de faire un if / else if
                case $action instanceof HooksAdminInterface:
                    if (is_admin()) {
                        $action->hooks();
                    }
                    break;
                case $action instanceof HooksFrontInterface:
                    if (!is_admin()) {
                        $action->hooks();
                    }
                    break;
                case $action instanceof HooksInterface:
                    $action->hooks();
                    break;
            }
        }
    }
}