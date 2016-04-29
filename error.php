<?php
namespace modules\core;

/**
 * An object for returning an error
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
 * 
 * The following codes are generally used with Official Gneiss modules
 * 
          0 I am Lazy - the programmer forgot to define a code      
        400 Bad Request
        401 Unauthorized 
        403 Forbidden
        404 Not Found
        405 Method Not Allowed
        408 Request Timeout
        409 Conflict
        410 Gone
        415 Unsupported Media Type
        418 I'm a teapot (RFC 2324)
        423 Locked

        500 Internal Server Error
        501 Not Implemented
        507 Insufficient Storage (WebDAV; RFC 4918)
        508 Loop Detected
 * 
 * Generally errors will be 5xx for internal and 4xx for users
 * [500] something is wrong and it cannot be fixed by the code;
 * [501] that should work but the develoepr has not made it so (yet)
 * [404] the thing cannot be found;
 * [401] the user needs to login or get more permissions to do that;
 * 
 *
 * @author lordmatt
 */
class error {
    protected $message = '';
    protected $code = 0;
    protected $data = array();
    
    
    public function __construct($message,$code=0,$data=array()) {
        $this->message = $message;
        $this->code = $code;
        $this->data = $data;
    }
    
    public function message(){
        return $this->message;
    }
    public function code(){
        return $this->code;
    }
    public function data(){
        return $this->data;
    }
}

