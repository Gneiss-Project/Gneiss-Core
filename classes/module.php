<?php
namespace modules\core\classes;


/**
 * A base class for modules
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
 * @author lordmatt
 */
abstract class module extends module_lib {
    /**
     * Do not call directly. Use $this->module_name()
     * @var string 
     */
    protected $_my_name_is = '';
    /**
     * The settings used array. Use $this->get_settings_used()
     * @var type 
     */
    protected $settings_used = array();
    /**
     * Returns a list of module names used but not depended on by this module
     * @return array 
     */
    public static function meta_uses(){
        return array();
    }
    /**
     * A list of modules depended on by this module
     * @return type 
     */
    public static function meta_depends(){
        return array();
    }
    
    /**
     * Get the root controller for this module
     * @return \modules\core\classes\controller
     */
    public function controller(){
        $childNS = explode('\\',get_class($this));
        array_pop($childNS); // drop the class name
        $module = array_pop($childNS);
        $controller = \modules\core\core::get()->factory()->get_module_lib($module,'controller');
        if(!($controller instanceof \modules\core\error)){
            $controller->inject_module($this);
        }
        return $controller;
    }
    /**
     * A structured array defining permissions this module might test for.
     * Complex settings should first be defined in 'view' or 'action'.
     * Assumes: {$module}.[view|action|custom].[custom]
     * @return array 
     */
    public static function explicit_user_actions(){
        $actions = array();
        $actions['view']=array('*');
        
        return $actions;
    }
    /**
     * Non static access to explicit_user_actions() but can be customised to 
     * include more details.
     * @return array 
     */
    public function list_user_actions(){
        return self::explicit_user_actions();
    }
    /**
     * Provides the module's name for factory calls about itself. It may be 
     * fractionally more efficient to hard code the name but this is not an 
     * ideal coding practice.
     * @return string 
     */
    protected function module_name(){
        if(trim($this->_my_name_is)!=''){
            return $this->_my_name_is;
        }
        $childNS = explode('\\',get_class($this));
        array_pop($childNS); // drop the class name
        $module = array_pop($childNS);
        $this->_my_name_is = $module;
        return $this->_my_name_is;
    }
    
    /**
     * Get the module model
     * @return modules\core\classes\model 
     */
    public function model(){
        #$childNS = explode('\\',get_class($this));
        #array_pop($childNS); // drop the class name
        #$module = array_pop($childNS);
        $module = $this->module_name();
        $model = \modules\core\core::get()->factory()->get_module_lib($module,'model');
        return $model;
    }
    /**
     * Fetch the setting for this module or the default if given, Otherwise 
     * returns FALSE.
     * 
     * @param string $var
     * @return mixed 
     */
    public function get_config($var){
        $this->load_once_settings();
        $settings = $this->get_settings_used();
        $module = $this->module_name();
        $val = \modules\core\core::get()->factory()->get_module_config($module,$var);
        if($val===FALSE && isset($settings[$var])){
            return $settings[$var][1]; // default value
        }
        if(isset($settings[$var])){
            if($settings[$var][0]=='bool'){
                if(strtoupper($val)=='ON'){
                    $val = TRUE;
                }elseif(strtoupper($val)=='OFF'){
                    $val= FALSE;
                }else{
                    $val= (bool) $val;
                }
            }elseif($settings[$var][0]=='int'){
                $val = (int) $val;
            }
        }
        return $val;
    }
    
    public function set_config($var,$val){
        $module = $this->module_name();
        \modules\core\core::get()->factory()->set_module_config($module,$var,$val);
    }
    
    /**
     * Does not actually load anything. Instead this method is triggered to load
     * defaults for the settings used values. Put your config values here.
     */
    protected function load_settings(){
        
    }
    /**
     * Don't overwrite this method without good reason. Use load_settings for
     * your config default needs.
     * 
     * @return void 
     */
    protected function load_once_settings(){
        if(isset($this->settings_used) && is_array($this->settings_used) && count($this->settings_used)>0){
            return;
        }
        return $this->load_settings();
    }
    /**
     * Returns the settings used by this module
     * @return array 
     */
    public function get_settings_used(){
        $this->load_once_settings();
        return $this->settings_used;
    }
    
    // used for defining UI.
    /**
     * Base method for adding settings used. Use the specialist methods, it's 
     * easier.
     * 
     * @param string $what
     * @param string $type
     * @param mixed $default
     * @param mixed $extra 
     */
    protected function add_setting($what,$type,$default,$extra=''){
        $this->settings_used[$what] = array($type,$default,$extra);
    }
    /**
     * Add a boolean value to settings used
     * @param string $what
     * @param bool $default 
     */
    protected function add_setting_bool($what,$default=FALSE){
        $this->add_setting($what, 'bool', $default);
    }    
    /**
     * Add an int value to settings used
     * @param string $what
     * @param int $default 
     */
    protected function add_setting_int($what,$default=0){
        $this->add_setting($what, 'int', $default);
    }    
    /**
     * Add a string or other value to settings used
     * @param string $what
     * @param string $default 
     */
    protected function add_setting_var($what,$default=''){
        $this->add_setting($what, 'var', $default);
    }    
    /**
     * Add a var with a limit set of options to settings used
     * @param string $what
     * @param string $default
     * @param array $list 
     */
    protected function add_setting_list($what,$default,$list=array()){
        $this->add_setting($what, 'list', $default, $list);
    }
    
    /**
     * Actions this module needs during install and setup 
     */
    public function install(){
        
    }
    /**
     * actions to cleanly uninstall this module 
     */
    public function uninstall(){
        
    }
    
}