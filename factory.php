<?php
namespace modules\core;
use modules\core\interfaces as i;
use modules\core\classes as c;

/**
 * Gneiss::factory creates instances of modules and module objects.
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
 * @author lordmatt
 */
class factory extends c\module_lib {
    
    protected $cache = array();
    protected $CONF;
    protected $helpers = array();
    
    protected function __construct() {}
    protected function __clone() {}
    
    /**
     *
     * @staticvar null $me
     * @return \modules\core\factory 
     */
    public static function &instence(){
        static $me = null;
        if($me===null || !is_object($me)){
            $me = new factory();
        }
        return $me;
    }
    
    /**
     * Legacy shortcut.
     * @return \modules\core\debug
     */
    public function &debug(){
        return $this->get_module_lib('core', 'debug');
    }
    
    
    /**
     * Alias of get_module_lib($name,$name)
     * @param string $name
     * @return \modules\{$name}\{$name}
     */
    public function &get_module($name){
        if(isset($this->cache[$name][$name]) && is_object($this->cache[$name][$name])){
            return $this->cache[$name][$name];
        }
        $mynameis = "core\\factory\\get_module({$name})";
        if(!ctype_alnum($name)){
            // bad, bad, bad. Only give me letters and numbers
            $this->debug()->log($mynameis,'notAlphaNumeric');
            $error = new \modules\core\error('Module notAlphaNumeric', 500);
            return $error;
        }
        return $this->get_module_lib($name, $name);
    }
    /**
     * @param string $module
     * @param string $lib
     * @return modules\{$module}\{$lib} 
     */
    public function &get_module_lib($module,$lib){
        $class = "modules\\{$module}\\{$lib}";
        
        if( isset($this->cache[$module][$lib]) && is_object($this->cache[$module][$lib]) ){
            return $this->cache[$module][$lib];
        } 
        
        $mynameis = "core\\factory\\get_module_lib({$module},{$lib})";
        
        try {
            if(class_exists($class)){
                try {
                    $this->cache[$module][$lib] = new $class();
                    if($this->cache[$module][$lib] instanceof i\module){
                        $this->cache[$module][$lib]->init();
                    }
                    return $this->cache[$module][$lib];
                } catch (\Exception $e) {
                    // var_dump($e->getMessage());
                    // do something clever here
                    $this->debug()->log($mynameis,print_r($e,true),true);
                }  
                // give up
                $this->debug()->log($mynameis,'tried and failed');
                $error = new \modules\core\error('tried and failed', 500);
                return $error;
            }else{
                // not here
                $this->debug()->log($mynameis,'not a class'.$module,true);
                $error = new \modules\core\error($mynameis,'not a class', 500);
                return $error;
            }
        } catch (\Exception $e) {
            // var_dump($e->getMessage());
            // do something clever here
            $this->debug()->log($mynameis,print_r($e,true),true);
        }
        $error = new \modules\core\error('Exception during module creation', 500);
        return $error;
    }
    
    private function load_CONF(){
        if(!is_array($this->CONF)){
            include $this->get_file_path() . '/config.php';
            if(!is_array($CONF)){
                $CONF=array();
            }
            $settings_path = $this->get_file_path() . '/settings/';
            if(is_array($CONF['settings']) && file_exists($settings_path)){
                $CONF['settings'][] = 'development';
                foreach($CONF['settings'] as $setting){
                    $file = $settings_path.$setting.'.php';
                    if(file_exists($file)){
                        include $file;
                    }
                }
            $this->CONF = $CONF;
            }
        }
    }
    /**
     * Push additional values into the Gneiss config stack
     * @param string $var
     * @param mixed $val 
     */
    public function set_config($var,$val){
        $this->load_CONF();
        $this->CONF[$var]=$val;
    }
    /**
     * Get Gneiss config values.
     * @param string $var
     * @param mixed $default
     * @return mixed 
     */
    public function get_config($var,$default=''){
        $this->load_CONF();
        if(isset($this->CONF[$var])){
            return $this->CONF[$var];
        }else{
            $val = $this->module()->model()->get_config($var);
            if($val!==FALSE){
                $default = $val;
            }
            $this->set_config($var, $default);
            return $this->CONF[$var];
        }
    }
    /**
     * Returns FALSE if config not set or table not ready.
     * @param string $module
     * @param string $var
     * @return mixed 
     */
    public function get_module_config($module,$var){
        return $this->module()->model()->get_module_config($module,$var);
    }
    /**
     *
     * @param string $message
     * @param int $code
     * @param array $data
     * @return \modules\core\error 
     */
    public function new_error($message,$code=0,$data=array()){
        $error = new error($message,$code,$data);
        $this->debug()->log("ERROR:{$code}:{$message}", $data);
        return $error;
    }
    
    protected function populate_helpers(){
        if( count($this->helpers)<1 ){
            $data = array();
            core::get()->notify('helpers',$data);
            #$this->debug()->log("TRACE:helpers", $data);
            $this->helpers=&$data;
        }
    }
    
    public function &get_helper($what){
        $this->populate_helpers();
        if(isset($this->helpers[$what])){
            return $this->helpers[$what];
        }
        $what = new error("{$what} not found",404,array());
        return $what;
    }
    
}
