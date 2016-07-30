<?php
namespace modules\core\classes;
use modules\core\interfaces as i;

/**
 * Base Controller class
 * Copyright (C) 2015-2016 Matthew David Brown
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>. 
 * 
 * 21-July-2016 - view_set now chainable
 * 
 * @author lordmatt
 */
abstract class controller extends module_lib implements i\controller {
    
    protected $view_data    = array();
    protected $request_data = array();
    protected $specific;    
    protected $view;
    
    public function __construct(){
        $this->view =& \modules\core\core::get()->factory()->get_module_lib('core', 'view');
    }
    
    public function request($what, $data=array()){
        $this->request_data = $data;
        $what = trim($what);
        $this->specific = $what;
        $request = "request_{$what}";
        if(method_exists($this, $request)){
            call_user_func(array($this,$request));
        }else{
            $this->default_request();
        }
    }
    
    /**
     * Start of response 
     */
    public function response(){
        $method="response_{$this->specific}";
        $call = array(&$this,$method);
        if(is_callable($call)){
            return call_user_func($call);
        }else{
            return $this->default_response();
        }
    }
    
    protected function default_request(){
        
    }
    protected function default_response(){
        
    }
    
    protected function register_sub_view_event($event){
        
    }
    
    public function view_set($var,$val){
        $this->view_data[$var]=$val;
        return $this;
    }

    protected function register_view_part($event,$method){
        //$childNS = explode('\\',get_class($this));
        //$me = array_pop($childNS);
        //$view = \modules\core\core::get()->factory()->get_core_object('view');
        $this->view->register($event,$this,$method);
        // should controller (ResCon) be seperated?
    }
    
    /**
     * Process a view template and get the result in a string.
     * @param string $view
     * @param array $context
     * @return string 
     */
    public function view($view,$context=array()){
        //extract($context);
        extract($this->view_data);
        if(!empty($context)){
            #foreach($context as $con=>$text){
            #    $this->view_data[$con]=$text;
            #}
            extract($context);
        }
        ob_start();
            $this->get_core()->debug()->log('OB LEVEL', ob_get_level().':'.$view);
            $viewfile = $this->get_file_path()."/views/{$view}.php";
            if(!file_exists($viewfile)){
                $this->get_core()->debug()->log('BAD VIEW', $view);
                $code = 500;
                $message = "The controller asked for {$view} but it was not there.";
                $viewfile = _GNEISS_BASE_PATH_ . '/modules/core/views/error.php';
            }
            #$this->get_core()->debug()->log('OB LEVEL', 'PRE:'.$view);
            include($viewfile);
            $content = ob_get_contents();
        ob_end_clean();
        #$this->get_core()->debug()->log('OB LEVEL', array('END:',$view,$content));
        return $content;
    }
    /**
     * This function is stupid
     * @param type $view
     * @param type $context 
     */
    public function print_view($view,$context=array()){
        echo $this->view($view, $context);
    }
    
    public function current_url(){
        $ssl        = ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' );
        $sp         = strtolower( $_SERVER['SERVER_PROTOCOL'] );
        $protocol   = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
        $link       = $protocol.'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        return $link;
    }
    
    public function is_on_current_server($link){
        if(substr_count($_SERVER['SERVER_NAME'], $link) >0){
            return true;
        }
        return false;
    }
    
}


