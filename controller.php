<?php
namespace modules\core;
use modules\core\interfaces as i;
use modules\core\classes as c;


/**
 * 
 * The core controller does things rather differently.
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
class controller extends c\controller {

    protected $control_object = null;

    public function request($what,$data){
        $m      =  array_shift($data);
        $what   =  array_shift($data);
        $obj    =& core::get()->factory()->get_module($m);
        $view   =& core::get()->factory()->get_module_lib('core', 'view');
        #core::get()->factory()->debug()->log('core::request',"STARTED",FALSE,3);
        #core::get()->factory()->debug()->log('core::request',$obj,FALSE,6);
        if($obj instanceof error){
            core::get()->factory()->debug()->log('core::request',"Trying Fallback",FALSE,3);
            $obj = $this->get_fallback_module();
            if(trim($what)!=''){
                array_unshift($data,$what);
            }
            if(trim($m)!=''){
                array_unshift($data,$m);
            }
            $what = $m;
        }
        if(is_object($obj) && !($obj instanceof error)){ 
            core::get()->factory()->debug()->log('core::request',"Using recognised object",FALSE,3);
            #core::get()->factory()->debug()->log('core::request',$obj,FALSE,6);
            $obj->controller()->request($what,$data);
            $this->control_object = $obj;
            $view->set_control_object($this->control_object);
        }else{
            // oh dear - problem
            core::get()->factory()->debug()->log('core::request',"[MODULE:{$m}] not found",FALSE,3);
            $this->view_set('code', '500');
            $this->view_set('message', "Unable to find [{$m}] to process [{$what}]. Requesting ".print_r($data,true));
            $view->set_control_object($this->module());
        }
        core::get()->_sp_request_done();
        $view->set_template(core::get()->factory()->get_config('template', $view->get_template()));
        $view->response();
    }
    
    protected function get_fallback_module(){
        $map = core::get()->factory()->get_config('map');
        core::get()->factory()->debug()->log('core::get_fallback_module()','START');
        if(isset($map['fallback'])){
            $fallback = core::get()->factory()->get_module($map['fallback']);
            core::get()->factory()->debug()->log('core::get_fallback_module()','Falling back to '.$map['fallback']);
            return $fallback;
        }  
        core::get()->factory()->debug()->log('core::get_fallback_module()','ERROR');
        return new error('no fallback',404);
    }
    
    public function response(){
        return $this->view("error");
    }
    
}

