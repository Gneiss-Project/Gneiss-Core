<?php
namespace modules\core;
use modules\core\interfaces as i;
use modules\core\classes as c;
/**
 * An object controlling the logical flow of view creation. Response Controllers 
 * will pass view results to this object. The view acts as a specilised 
 * controller.
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
class view extends c\controller {
    
    protected $subscriptions = array();
    protected $template='template';
    protected $primary_object;
    
    public function set_control_object(&$object){
        $this->primary_object =& $object;
    }

    public function get_primary_view(){
                                    //controller
        return $this->primary_object->controller()->response();
        //return $this->primary_object->response();
    }

    public function __construct(){
        // overrides default to avoid getting a copy of itself.
    }
    
    public function register($event,&$controller,$method){
        $this->subscriptions[$event][] = array(&$controller,$method);
    }
    
    public function deregister($event,$method){
        foreach($this->subscriptions[$event] as $a=>$b){
            if($b[1]==$method){
                unset($this->subscriptions[$event][$a]);
            }
        }
    }
    
        
    public function response(){
        echo $this->view($this->template, array());
    }
    
    private function TMP_DBUG($note){
        #\modules\core\core::get()->factory()->debug()->log('EVENT', $note, false, 6);
    }
    
    protected function view_event($event){
        $output = '';
        $this->TMP_DBUG('START:'.$event);
        if(isset($this->subscriptions[$event]) && is_array($this->subscriptions[$event])){
            $this->TMP_DBUG('is_set');
            foreach($this->subscriptions[$event] as $callback){
                $this->TMP_DBUG('looping');
                if(is_object($callback[0])){
                    $method = $callback[1];
                    $classname = get_class($callback[0]);
                    $this->TMP_DBUG("ready to call it {$classname}->{$method}");
                    if(is_callable(array(&$callback[0],$method))){
                        $output .= call_user_func(array(&$callback[0],$method));
                        $this->TMP_DBUG('called it');
                    }
                }
            }
        }
        $this->TMP_DBUG('END:'.$event);
        return $output;
    }
    
    
    /**
     * A method to tactically send content from the view best used to optimise
     * page rendering. For example by sending the head part of a page as soon as
     * it is ready and the body later.
     */
    protected function vFlush(){
        ob_flush();
        flush();
    }
    
    
    
    public function get_template(){
        return $this->template;
    }
    
    public function set_template($template){
        $this->template = $template;
    }
    
}


