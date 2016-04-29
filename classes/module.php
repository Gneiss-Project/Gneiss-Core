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
    
    protected $settings_used = array();
    
    public static function meta_uses(){
        return array();
    }
    public static function meta_depends(){
        return array();
    }
    
    
    public function &controller(){
        $childNS = explode('\\',get_class($this));
        array_pop($childNS); // drop the class name
        $module = array_pop($childNS);
        $controller = \modules\core\core::get()->factory()->get_module_lib($module,'controller');
        if(!($controller instanceof \modules\core\error)){
            $controller->inject_module($this);
        }
        return $controller;
    }
    
    public static function explicit_user_actions(){
        $actions = array();
        $actions['view']=array('*');
        
        return $actions;
    }
    
    public function list_user_actions(){
        return self::explicit_user_actions();
    }

    public function model(){
        $childNS = explode('\\',get_class($this));
        array_pop($childNS); // drop the class name
        $module = array_pop($childNS);
        $model = \modules\core\core::get()->factory()->get_module_lib($module,'model');
        return $model;
    }
    
    protected function get_settings_used(){
        return $this->settings_used;
    }
    
    protected function add_setting($what,$type,$default,$extra=''){
        $this->settings_used[$what] = array($type,$default,$extra);
    }
    
    protected function add_setting_bool($what,$default=TRUE){
        $this->add_setting($what, 'bool', $default);
    }    
    protected function add_setting_int($what,$default=0){
        $this->add_setting($what, 'int', $default);
    }    
    protected function add_setting_var($what,$default=''){
        $this->add_setting($what, 'var', $default);
    }    
    protected function add_setting_list($what,$default,$list=array()){
        $this->add_setting($what, 'list', $default, $list);
    }
    
}