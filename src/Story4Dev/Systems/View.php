<?php
namespace Story4Dev\Systems;

/**
 * View
 *
 * @author JOELINJATOVO
 * @version 1.0.0
 * @since 1.0.0
 */
class View{
    /**
    * @var $_datas Array
    */
    protected $_datas;
    
    /**
    * @param String $key
    * @param mixed $value
    */
    public function __set($key, $value){
        if(!isset($this->_datas) || !is_array($this->_datas)){
            $this->_datas = [];
        }
        $this->_datas[$key] = $value;
    }
    
    /**
    * @param String $key
    * @return mixed
    */
    public function __get($key){
        if(!isset($this->_datas[$key])){
            return '';
        }
        return $this->_datas[$key];
    }
    
    /**
    * @param String $key
    * @return mixed
    */
    public function __isset($key){
        return isset($this->_datas[$key]);
    }
    
    /*
    * get all setted var
    * @return Array
    */
    public function get_data(){
        return $this->_datas;
    }
    
}