<?php
namespace modules\core\classes;


/**
 * A base class for models
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
abstract class model {
    
    protected $CONN = 0;
    
    protected function install(){
        
    }
    
    public function get_tables(){
        return array();
    }
    
    /**
     * Takes a table name and returns true if it can bind the statment and find
     * the table. Otherwise false.
     * @param string $table
     * @return boolean 
     */
    protected function table_exists($table){
        $query="SELECT * FROM information_schema.TABLES WHERE TABLE_NAME = ?;";
        if ($stmt = $this->get_mysqli()->prepare($query)) {
            $stmt->bind_param("s", $table); //This is line 24.
            $stmt->execute();            
            $result = FALSE;
            $stmt->store_result();
            if($stmt->errno==0 && $stmt->num_rows>0){
                $result = TRUE;
            }
            $stmt->close();
            return $result;
        }
        #die('CANNOT PREPARE THAT '.$query);
        return FALSE; //error
    }
    
    /**
     * Provides an array with tales as keys and boolean values for table status
     * @return array 
     */
    public function get_tables_status(){
        $t = array();
        foreach($this->get_tables() as $table){
            $t[$table]=$this->table_exists($table);
        }
        return $t;
    }
    
    /**
     * A short cut for updating a table. The array keys must be the feild names.
     * 
     * Data (values) to be set are escaped but it is assumed that field and key
     * values are already clean - ONLY YOU CAN PREVENT SQL INJECTION!!
     * 
     * @param array $values
     * @param string $table 
     */
    protected function update($values,$table,$id,$key='id'){
        if(!is_array($values)){
            return false;
        }
        $mysqli = $this->get_mysqli();
        $todo = false;
        $CHANGES='';
        $d='';
        foreach($values as $field=>$value){
            $value = $mysqli->real_escape_string($value);
            $CHANGES .= "{$d}`{$field}` = '{$value}'";
            $d=', ';
            $todo=true;
        }
        if($todo){
            $sql = "UPDATE `{$table}` SET {$CHANGES} WHERE `{$key}` = '{$id}';";
            $mysqli->query($sql);
        }
    }
    /**
     * shortcut/help get standard connection
     * @return \mysqli 
     */
    protected function &get_mysqli(){
        return \modules\core\core::get()->database()->get($this->CONN);
    }
    
    
}