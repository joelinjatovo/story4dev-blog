<?php
namespace Story4Dev\Systems;

/**
 * BaseController
 *
 * @author JOELINJATOVO
 * @version 1.0.0
 * @since 1.0.0
 */
class BaseController{
    /**
    * @var View
    */
    public $view;
    
    function __construct($view){
        $this->view = $view;
    }
    
    public function render($path){
        return S4D_DIR. '/template/'.$path.'.php';
    }
    
    public function redirect($path){
        wp_redirect($path);
    }
    
    public function json($args){
        echo json_encode($args);
        exit();
    }
    
    public function back(){
        wp_redirect('/');
    }
}