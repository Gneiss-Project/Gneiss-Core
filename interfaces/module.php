<?php
namespace modules\core\interfaces;


/**
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
interface module {
    
    /**
     * Called early 
     */
    public function init();
    
    // interigation
    public static function meta_name();
    public static function meta_description();
    public static function meta_version();
    public static function meta_uses();
    public static function meta_depends();
    public function meta_($what);
    
    /**
     * Should respond with an array 
     */
    public function subscribes();
    
    public static function explicit_user_actions();
    public function list_user_actions();
    
}


