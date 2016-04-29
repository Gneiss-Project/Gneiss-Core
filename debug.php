<?php
namespace modules\core;

/**
 * Debug logging stack.
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
class debug {
    protected $log = array();
    protected $level=11;
    /**
     * Chainable debug logging method
     * 
     * @param string $what
     * @param mixed $ref
     * @param boolean $backtrace
     * @param integer $level
     * @return \modules\core\debug 
     */
    public function &log($what,$ref,$backtrace=false,$level=5){
        if($level>$this->level){
            return $this;
        }
        $log = array();
        $log['message']=$what;
        if(is_array($ref) || is_object($ref)){
            $ref = print_r($ref, true);
        }
        $log['ref']=$ref;
        if($backtrace){
            $log['backtrace']=debug_backtrace();
        }
        $this->log[] = $log;
        return $this;
    }
    /**
     * Change the log recording level (chainable)
     * @param integer $new_level 
     * @return \modules\core\debug 
     */
    public function switch_level($new_level=5){
        $this->level = $new_level;
        return $this;
    }
    /**
     * Get the current log for inspection
     * @return array 
     */
    public function get(){
        return $this->log;
    }
    /**
     * Get the log and empty the record
     * @return type 
     */
    public function flush(){
        $log = $this->log;
        return $this->log=array();
    }
}
