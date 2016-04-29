<?php
namespace modules\core;
use modules\core\interfaces as i;
use modules\core\classes as c;

/**
 * Core\Lib::mapper - Maps URL arrays to actual modules and ensures that default
 * module is selected when no URI data is presented. Handles module not found
 * fallback options too.
 * 
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
 *********
 * NOTES *
 *********
 * 
 * Triggers 1 (one) callback notice.
 * 
 * @todo BUG: Mapper not running as expected (possibly related)
 *
 * @author lordmatt
 */
class mapper extends c\module_lib  {
    protected $mapping  = array();
    protected $default  = array();
    protected $notfound = array();
    
    protected function load_mapping(){
        $this->module()->debug()->log('Map Loader','START');
        if( count($this->mapping)>0 ){ 
            $this->module()->debug()->log('Map Loader','NOT NEEDED');
            return true;
        }
        $maps = $this->module()->factory()->get_config('map',array());
        $data = array('maps'=>&$maps);
        $this->module()->notify('mapping',$data);
        if(isset($maps['default'])){
            $this->default = $maps['default'];
        }else{
            $this->module()->debug()->log('WARNING','Map Loader: No default to map');
        }
        if(isset($maps['404'])){
            $this->notfound = $maps['404'];
        }else{
            $this->module()->debug()->log('WARNING','Map Loader: No 404 to map');
        }
        if(isset($maps['alias'])){
            $this->mapping = $maps['alias'];
        }else{
            $this->module()->debug()->log('Notice','Map Loader: No Alias to map');
        }
        $this->module()->debug()->log('Map Loader','FINISH');
        //
        // @todo load further mapping from DB
    }
    
    public function map(&$data){
        $this->load_mapping();
        $this->module()->debug()->log('INSPECT',"MAP: data count : ".count($data),false,11);
        $this->module()->debug()->log('INSPECT',"MAP: data : ".print_r($data,true),false,11);
        if(count($data)<1 || trim($data[0]=='')){
            $this->module()->debug()->log('Notice',"MAP: Using Default : ".print_r($this->default,true));
            $data = $this->default;
            return true;
        }
        if(isset($this->mapping[$data[0]])){
            $data[0] = $this->mapping[$data[0]];
        }
        $mynameis = 'mapper->map(...)';
        $found = false;
        if(!isset($data[0]) || trim($data[0]) == ''){
            return false;
        }
        $class="\\modules\\{$data[0]}\\{$data[0]}";
        ob_start();
        try {
            if(class_exists($class)){
                $found = true;
            }
        } catch (\Exception $e) {
            $this->module()->debug()->log($mynameis,"Class [{$class}] probably not found: ".var_dump($e->getMessage()));
        }
        ob_end_clean();
        if(!$found){
            $new = $this->notfound;
            foreach($data as $d){
                $new[] = $d;
            }
            $data=$new;
        }
    }
    
    
}

