<?php
namespace modules\core;
use modules\core\interfaces as i;

/**
 * This class assumes that the system is useing MySQLi to use another database
 * system, simply write a repalcement and create models that use the new system.
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
 * @author lordmatt
 */
class database {
    
    protected $DB = array();
    /**
     * Get the selected database connection
     * @param int $n
     * @return \mysqli 
     */
    public function &get($n=0){
        if(!isset($this->DB[$n]) || !is_object($this->DB[$n])){
            $settings = core::get()->factory()->get_config('DB');
            if(!is_array($settings)){
                die('Database has no values');
            }
            $DB = $settings[$n];
            $mysqli = new \mysqli($DB['host'], $DB['user'], $DB['password'], $DB['database']);
            if($mysqli===FALSE){
                die('FAILED TO CONNECT');
            }
            if ($mysqli->connect_errno) {
                $err = "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
                core::get()->factory()->debug()->log($err,500);
                $this->DB[$n]=false;
                #print_r($mysqli);
                die();
            }else{
                $this->DB[$n] = $mysqli;
            }
        }
        return $this->DB[$n];
    }
    
    public function __destruct(){
        foreach($this->DB as $n=>$db){
            $db->close();
            unset($this->DB[$n]);
        }
    }
}

