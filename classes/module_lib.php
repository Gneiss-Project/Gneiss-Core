<?php
namespace modules\core\classes;

/**
 * Gneiss::core::classes::module_lib is the basis for most module libs, and also
 * for module. Common internal API.
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
abstract class module_lib {
    
    private   $module;
    public function inject_module(&$module){
        $this->module =& $module;
    }
    /**
     * Prives access to the parent module
     * @return \modules\<$SELF$>\<$SELF$> 
     */
    protected function &module(){
        if(!is_object($this->module)){
            $module_name = $this->module_name();
            $this->module =& $this->get_lib($module_name);
        }
        //todo: throw error if this is not an object
        return $this->module;
    }
    
    protected function get_file_path(){
        $childNS = explode('\\',get_class($this));
        array_pop($childNS); // we're not actually interested in the class name itself.
        $pathpart = '';
        foreach($childNS as $pp){
            $pathpart .= "/{$pp}";
        }
        return \_GNEISS_BASE_PATH_ . $pathpart;
    }
    
    protected function module_include($what){
        include_once($this->get_file_path()."/{$what}");
    }
    
    protected function module_name(){
        $childNS = explode('\\',get_class($this));
        array_pop($childNS); // we're not actually interested in the class name itself.
        return array_pop($childNS);
    }
    
    protected function url($specific,$data=array()){
        return \modules\core\core::get()->url($this->module_name(),$specific,$data);
    }
    
    protected function &get_lib($lib){
        return \modules\core\core::get()->factory()->get_module_lib($this->module_name(),$lib);
    }
    
    protected function is_allowed($specific,$detail,$strict=FALSE){
        return \modules\core\core::get()->is_allowed($this->module_name(),$specific,$detail,$strict);
    }
    
    protected function is_allowed_view($what,$detail=NULL){
        return $this->is_allowed_two_step('view', $what, $detail);
    }
     
    protected function is_allowed_action($what,$detail=NULL){
        return $this->is_allowed_two_step('action', $what, $detail);
    }   
    protected function is_allowed_two_step($mode,$what,$detail=NULL){
        if($this->is_allowed($mode, $what)){
            if($detail!==NULL && is_string($detail)){
                if($this->is_allowed($what, $detail)){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else{
                return TRUE;
            }  
        }
        return FALSE;
    }

    /**
     * Get a pointer to the core object
     * @return \modules\core\core 
     */
    protected function &get_core(){
        return \modules\core\core::get();
    }
    
}
