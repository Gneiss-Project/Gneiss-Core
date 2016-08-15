<?php
namespace modules\core;
use modules\core\interfaces as i;
use modules\core\classes as c;



/**
 * Gneiss::core::model - handles DB IO
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
class model extends c\model {
        
    public function get_tables(){
        return array('gneiss_keys','gneiss_modules','gneiss_events','gneiss_config','gneiss_module_config');
    }
    
    // Keys
    
    public function store_key($key,$me,$id){
        $this->purge_old_keys();
        $mysqli = $this->get_mysqli();
        $key    = $mysqli->real_escape_string($key);
        $me     = $mysqli->real_escape_string($me);
        $id     = intval($id);
        $SQL    = "INSERT INTO `gneiss_keys` VALUES('{$key}','{$me}','{$id}',NULL);";
        return $mysqli->query($SQL);
    }
    
    public function key_exists($key){
        $mysqli = $this->get_mysqli();
        $key    = $mysqli->real_escape_string($key);
        $query  = "SELECT `key` FROM `gneiss_keys` WHERE `key`='{$key}' LIMIT 1;";
        $result = $mysqli->query($query);
        return ($result->num_rows>0);
    }
    
    public function get_from_key($key){
        $mysqli = $this->get_mysqli();
        $key    = $mysqli->real_escape_string($key);
        $query  = "SELECT * FROM `gneiss_keys` WHERE `key`='{$key}' LIMIT 1;";
        $result = $mysqli->query($query);
        return $result->fetch_object();
    }
    
    public function kill_key($key){
        $mysqli = $this->get_mysqli();
        $key    = $mysqli->real_escape_string($key);
        $query = "DELETE FROM `gneiss_keys` WHERE `key`='{$key}' LIMIT 1;";
        $this->purge_old_keys();
        return $mysqli->query($query);
    }
    
    public function purge_old_keys(){
        $this->get_mysqli()->query('DELETE FROM `gneiss_keys` WHERE `made` < NOW() - INTERVAL 1 WEEK'); 
    }
    
    // 1.0
    
    public function get_next_sort_value(){
        if(!$this->table_exists('gneiss_modules')){
            return 1;
        }
        $query  = "SELECT MAX(`order`) as nnext FROM `gneiss_modules`;";
        $result = $this->get_mysqli()->query($query);
        $r      = $result->fetch_object();
        $val    = intval($r->nnext);
        $val++;
        return $val;
    }
    
    public function get_all_modules(){
        if(!$this->table_exists('gneiss_modules')){
            return array();
        }
        $query  = "SELECT `id`, `module`, `order` FROM `gneiss_modules` ORDER BY `order` DESC;";
        $result = $this->get_mysqli()->query($query);
        $all    = array();
        if($result->num_rows>0){
            while($row = $result->fetch_object()){
                $all[] = $row;
            }
        }
        return $all;
    }
    
    public function add_module_to_index($module){
        if(!$this->table_exists('gneiss_modules')){
            return FALSE;
        }
        $order  = $this->get_next_sort_value();
        $module = $this->get_mysqli()->real_escape_string($module);
        $query  = "INSERT INTO `gneiss_modules` VALUES(NULL,'{$module}','{$order}');";
        return $this->get_mysqli()->query($query);
    }
    
    public function add_listener($event,$module,$sub){
        if(!$this->table_exists('gneiss_events')){
            return FALSE;
        }
        $event  = $this->get_mysqli()->real_escape_string($event);
        $module = $this->get_mysqli()->real_escape_string($module);
        $sub    = $this->get_mysqli()->real_escape_string($sub);
        $query  = "INSERT INTO `gneiss_events` VALUES ('{$event}','{$module}','{$sub}') ON DUPLICATE KEY UPDATE sub=sub;";
        return $this->get_mysqli()->query($query);
    }
    
    public function remove_listener($event,$module,$sub){
        if(!$this->table_exists('gneiss_events')){
            return FALSE;
        }
        $event  = $this->get_mysqli()->real_escape_string($event);
        $module = $this->get_mysqli()->real_escape_string($module);
        $sub    = $this->get_mysqli()->real_escape_string($sub);
        $query = "DELETE FROM `gneiss_events` WHERE 
            `gneiss_events`.`event` = '{$event}' AND 
            `gneiss_events`.`module` = '{$module}' AND 
            `gneiss_events`.`sub` = '{$sub}'";
        return $this->get_mysqli()->query($query);
    }
    
    public function get_listeners($event){
        #return FALSE;
        if(!$this->table_exists('gneiss_events')){
            return array();
        }
        $query = "SELECT `module`,`sub` FROM `gneiss_events` WHERE `event` = ?;";
        if ($stmt = $this->get_mysqli()->prepare($query)) {
            $stmt->bind_param("s", $event);// s/i/d
            $stmt->execute();
            $result = array();
            $stmt->store_result();
            if($stmt->errno==0 && $stmt->num_rows>1){
                /* bind result variables */
                $stmt->bind_result($module, $sub);
                /* fetch values */
                while ($stmt->fetch()) {
                    $result[] = array('module'=>$module,'lib'=>$sub);;
                }
            }
            $stmt->close();
            return $result;
        }else{
            return FALSE;
        }
    }
    
    public function set_config($var,$val){
        if(!$this->table_exists('gneiss_config')){
            return FALSE;
        }
        $var    = $this->get_mysqli()->real_escape_string($var);
        $val    = $this->get_mysqli()->real_escape_string($val);
        $query  = "INSERT INTO `gneiss_config` VALUES('{$var}','{$val}');";
        return $this->get_mysqli()->query($query); 
    }
    
    public function get_config($var){
        #return FALSE;
        if(!$this->table_exists('gneiss_config')){
            return FALSE;
        }
        $query = "SELECT `val` FROM `gneiss_config` WHERE `var`=? LIMIT 1;";
        if($stmt = $this->get_mysqli()->prepare($query)){
            $stmt->bind_param("s", $var);// s/i/d
            $stmt->execute();
            $stmt->store_result();
            if($stmt->errno==0 && $stmt->num_rows>0){
                $stmt->bind_result($val);
                $stmt->fetch();
                $stmt->close();
                return $val;
            }
            return FALSE;
        }else{
            return FALSE;
        }
    }
    
    public function set_module_config($module,$var,$val){
        if(!$this->table_exists('gneiss_module_config')){
            return FALSE;
        }
        $module = $this->get_mysqli()->real_escape_string($module);
        $var    = $this->get_mysqli()->real_escape_string($var);
        $val    = $this->get_mysqli()->real_escape_string($val);
        $query  = "INSERT INTO `gneiss_module_config` (`module`,`var`,`val`)
                        VALUES('{$module}','{$var}','{$val}')
                        ON DUPLICATE KEY UPDATE `val`='{$val}';";
        return $this->get_mysqli()->query($query); 
    }
    
    public function get_module_config($module,$var){
        #return FALSE;
        if(!$this->table_exists('gneiss_module_config')){
            return FALSE;
        }
        $query = "SELECT `val` FROM `gneiss_module_config` WHERE `module`=? AND `var`=? LIMIT 1;";
        if($stmt = $this->get_mysqli()->prepare($query)){
            $stmt->bind_param("ss", $module,$var);// s/i/d
            $stmt->execute();
            $stmt->store_result();
            if($stmt->errno==0 && $stmt->num_rows>0){
                $stmt->bind_result($val);
                $stmt->fetch();
                $stmt->close();
                return $val;
            }
            return FALSE;
        }else{
            return FALSE;
        }
    }
}


