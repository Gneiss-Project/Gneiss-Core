<?php
namespace modules\core;
use modules\core\interfaces as i;
use modules\core\classes as c;

/**
 * Description of core
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
 * 
 * 1.0.1 - changes URL to urlencode data elements
 * 1.0.2 - commented the heck out of module_lib functions
 * 1.0.3 - added module_exists method
 * 1.1.0 - gave in to the inevitable and added an api_controller to classes
 * 1.2.0 - added the pager object and made controller::view_set chainable
 * 1.2.1 - moved pager to it's own module - pages
 * 1.2.2 - URL Redirect seperated into more methods
 * 1.2.3 - core::classes::model::update() made protected
 *       - core::classes::model::table_exists() added to model
 * 1.3.0 - Now uses optional tables for config
 *       - core::classes::model::get_tables_status()
 *       - designed for use with the setup module
 *       - factory less assumptive about config files
 * 1.3.1 - now assumes new format for key table
 *       - purges old keys on new key and remove key
 * 1.3.2 - factory now uses optional tables
 *       - modules can now access module config
 *       - core::classes::module::get_config($var)
 *       - module base methods now have doc comments
 * 1.3.3 - cache added to core::classes::model::table_exists()
 * 1.3.4 - $this::url() now takes varable arugments
 *       - fixed a bug in module which caused settings to fail to load
 */
class core implements i\module {
    
    protected $control_object = null;

    public function request($data){
        // note the self URL
        $self=$this->factory()->get_config('home','/').implode('/', $data);
        $this->factory()->get_config('self',$self);
        // Any system init actions
        $mapper =& $this->factory()->get_module_lib('core', 'mapper');
        if($mapper instanceof error){
            print_r($mapper);
            die("fatal error");
        }
        $mapper->map($data);
        $ar = array('data'=>&$data);
        $this->notify('request',$ar);
        $controller = $this->factory()->get_module_lib('core', 'controller');
        $controller->request('core',$data);
    }
    
    public function _sp_request_done(){
        $attempt_to_start_session = true;
        $data = array('attempt_to_start_session'=>&$attempt_to_start_session);
        $this->notify('request_end',$data);
        if($attempt_to_start_session){
            $c = $this->cookie();
            $c->start_session();
        }
    }
    /**
     *
     * @return \modules\core\model
     */
    public function model(){
        $model = $this->factory()->get_module_lib('core','model');
        return $model;
    }
    
    public function init(){
        // nothing to do
    }
    
    // interigation
    public static function meta_name(){
        return 'Gneiss Core';
    }
    public static function meta_description(){
        return 'The core of the Gneiss system. Does stuff to make it work.';
    }
    public static function meta_version(){
        return '1.3.4';
    }    
    public static function meta_uses(){
        return array();
    }
    public static function meta_depends(){
        return array();
    }
    public function meta_($what){
        return $what;
    }
    public function subscribes(){
        return array();
    } 
    public static function explicit_user_actions(){
        $actions = array();
        $actions['view']=array('*');
        return $actions;
    }
    public function list_user_actions(){
        return self::explicit_user_actions();
    }
    
    public function &controller(){
        $controller = \modules\core\core::get()->factory()->get_module_lib('core','controller');
        if($controller!==FALSE && method_exists($controller, 'inject_module')){
            $controller->inject_module($this);
        }
        return $controller;
    } 
    
    // 0.7
    
    protected function getListeners($event){
        try {
            $l = $this->model()->get_listeners($event); // 1.3.1 went live
            if($l===FALSE){
                return array();
            }
            return $l;
        } catch (\Exception $e) {
            $this->debug()->log('Caught Exception in getListeners for '.$event, $e);
            return array();
        }
    }
    
    public function notify($event,&$data){
        $forced = $this->factory()->get_config('subscribers', array());
        $loaded = $this->getListeners($event);
        if(isset($forced[$event])){
            $loaded = array_merge($forced[$event],$loaded);
        }
        if(count($loaded)==0){
            return false;
        }
        // array unique $loaded
        foreach($loaded as $listen){
            //print_r($loaded);
            $object=null;
            try {
                $object = $this->factory()->get_module_lib($listen['module'], $listen['lib']);
            } catch (\Exception $e) {
                // todo: do something clever
            }
            #$this->factory()->debug()->log('checking-notify-listeners ['.$event.']', print_r($loaded,true) );
            if(is_object($object)){
                $method ="event_{$event}";
                $callme = array(&$object,$method);
                if(is_callable($callme)){
                    call_user_func_array($callme, array(&$data));
                    #$this->debug()->log('EVENT:'.$event, $data);
                }
            }
        }
    }
    /**
     * $core->url([$module [, $specific [, array()]]])
     * $core->url([$module [, $specific [, $string [, $string [, ...]]]]])
     * 
     * Takes a variable input and converts it to a valid system URL
     * 
     * @param string $module
     * @param string $specific
     * @param string $data ...
     * @return string 
     */
    public function url(){
        // $module,$specific='',$data=array()
        $args = func_get_args();
        $url = $this->factory()->get_config('home','/');
        if(count($args)==0){
            return $url;
        }
        if(isset($args[0])){
            $module=array_shift($args);
        }
        if(isset($args[0])){
            $specific=array_shift($args);
        }else{
            $specific='';
        }
        if(is_array($args[0])){
            $data=$args[0];
        }else{
            $data=$args;
        }
        $url .= "{$module}/";
        if($specific!=''){
            $url .= "{$specific}/";
        }
        #.
        if(count($data)>0){
            foreach($data as $d){
                $d=urlencode($d);
            }
        }
        
        $url .= implode('/', $data);
        
        $url = str_replace('-', '%2D', $url);// literal non space -'s
        $url = str_replace(' ', '-', $url);
        return $url;
    }
    
    /**
     * Split from redirect 22-July-2016
     * @param string $url
     * @param string $status
     * @param bool $headsentNoDie 
     */
    public function url_redirect($url,$status=303,$headsentNoDie=false){
        if(!headers_sent()){
            header('Location: '.$url, true, $status);
            die();
        }else{
            header( "refresh:1;url={$url}" );
            echo "<p>If you are not redirectd <a href='{$url}'>CLICK HERE</a>.</p>";
            if(!$headsentNoDie){
                die();
            }
        } 
    }
    
    public function redirect($path=array(),$status=303,$headsentNoDie=false){
        $path = implode('/', $path);
        $path = str_replace(' ', '-', $path);
        $url = $this->factory()->get_config('home','/') . $path;
        $this->url_redirect($url, $status, $headsentNoDie);
    }

    
    
    // The following functions are convenience shortcuts to core libs
    // A by-product of which is the ability to move libs other name space with 
    // minimal fuss.
    
    /**
     *
     * @return \modules\core\core 
     */
    public static function &get(){
        return factory::instence()->get_module('core');
    }
    /**
     *
     * @return \modules\core\factory
     */
    public function &factory(){
        return factory::instence();
    }
    /**
     *
     * @return \modules\core\database
     */
    public function &database(){
        return $this->factory()->get_module_lib('core', 'database');
    }
    /**
     * @todo TEST - is this working?
     * @return \modules\core\debug
     */
    public function &debug(){
        return $this->factory()->debug();
    }
    /**
     * Get access to the cookie lib
     * @return \modules\core\cookieMonster 
     */
    public function &cookie(){                #          cookieMonster
        return $this->factory()->get_module_lib('core', 'cookieMonster');
    }
    /**
     * Get access to the sessions lib
     * @return \modules\core\cookieMonster 
     */
    public function &session(){
        return $this->factory()->get_module_lib('core', 'cookieMonster');
    }
    /**
     *
     * @param int $allow
     * @param boolean $strict
     * @return boolean 
     */
    protected function fuzzy_to_bool($allow=0,$strict=false){
        if($allow<0 || ($strict && $allow==0) ){
            return FALSE;
        }
        return TRUE;
    }
    /**
     * Three grain permission check
     * @param string $module
     * @param string $specific
     * @param string $detail
     * @param boolean $strict
     * @return boolean 
     */
    public function is_allowed($module,$specific,$detail,$strict=FALSE){
        //  -1     0     1
        // deny nutral allow
        $allow = 0;  
        $data = array();
        $data['perm']=array($module,$specific,$detail);
        $data['allow']=&$allow;
        $this->notify('is_allowed', $data);
        return $this->fuzzy_to_bool($allow, $strict);
    }
    
    public function lib_exists($what){
        $temp = factory::instence()->get_helper($what);
        if($temp instanceof error){
            $this->debug()->log('No LIB '.$what, $temp);
            return false;
        }
        return true;
    }
    
    public function &get_lib($what){
        return factory::instence()->get_helper($what);
    }
    
    // Keys
    // @todo Expire old (needs a date function in the DB).
    
    public function get_new_key($me,$id){
        $key = $this->make_key($me, $id);
        if($key instanceof error){
            return $key;
        }
        if($this->model()->store_key($key, $me, $id)){
            return $key;
        }
        return new error('Key store failure',500);
    }
    
    protected function make_key($me,$id,$sanity=5){
        if($sanity<1){
            return new error('Core loop detected',500);
        }
        $algo = $this->factory()->get_config('keyhash', 'haval128,5');
        $salt = $this->factory()->get_config('salt', bin2hex(openssl_random_pseudo_bytes(5)));
        $timedata = microtime();
        $rand = bin2hex(openssl_random_pseudo_bytes(5));
        $key = hash($algo, "{$timedata}#{$me}-{$id}+{$salt}@{$rand}{$sanity}");
        if( !$this->model()->key_exists($key) ){
            return $key;
        }else{
            $sanity--;
            return $this->make_key($me, $id, $sanity);
        }
    }
    
    public function check_key($key,$me,$forget=FALSE){
        $good = $this->model()->get_from_key($key);
        if(!is_object($good)){
            return FALSE;
        }
        if($forget){
            $this->model()->kill_key($key);
        }
        return (int) $good->id;
    }
    
    // 1.0
    
    protected $modules = array();
    
    public function get_modules_list(){
        if(count($this->modules)<1){
            $this->reload_modules();
        }
        return $this->modules;
    }
    
    protected function reload_modules(){
        $this->modules = $this->model()->get_all_modules();
    }
    
    protected function check_deps($module){
        $static = "\\modules\\{$module}\\{$module}";
        $dep = $static::meta_depends();
        $use = $static::meta_uses();
        $all = $this->get_modules_list();
        $list = array();
        $results = array();
        $results['missing']=array();
        $results['warn']=array();
        foreach($all as $mods){
            $list[] = $mods->module;
        }
        if(is_array($dep) && count($dep)>0){
            foreach($dep as $d){
                if(!in_array($d, $list)){
                    $results['missing'][] = $d;
                }
            }
        }
        if(is_array($use) and count($use)>0){
            foreach($use as $u){
                if(!in_array($u, $list)){
                    $results['warn'][] = $u;
                }
            }
        }
        return $results;
    }


    public function install($module){
        $stage = 0;
        try {
            $mod = $this->factory()->get_module($module);
            if($mod instanceof error){
                return $mod;
            }
            $stage++;
            $results = $this->check_deps($module);
            if(count($results['missing'])>0){
                return $results;
            }
            $stage++;
            if($mod instanceof \modules\core\interfaces\installs){
                $in = $mod->install();
            }
            $stage++;
            $events = $mod->subscribes();
            if(is_array($events) && count($events)>0){
                foreach($events as $event){
                    $this->add_listener($event, $module, $module);
                }
            }
            $stage++;
            if($mod instanceof \modules\core\interfaces\configurable){
                $settings = $mod->get_settings();
                foreach($settings as $what=>$set){
                    if(is_array($set)){
                        $this->model()->set_module_config($module,$what,$set[1]);
                    }
                }
            }
            $stage++;
            $this->model()->add_module_to_index($module);
        }catch(\Exception $e){
            return $this->factory()->new_error("Error on module load for {$module} stage {$stage}", 500);
        }
        return $results;
    }
    
    public function uninstall($module){
        
    }
    
    public function add_listener($event,$module,$sub){
        return $this->model()->add_listener($event,$module,$sub);
    }
    /**
     * added 1.0.3
     * @param string $module
     * @return bool 
     */
    public function module_exists($module){
        $file = _GNEISS_BASE_PATH_."modules/{$module}";
        return file_exists($file);
    }
    
}