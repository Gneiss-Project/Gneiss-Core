<?php
/**
 * config.php handles config loadingand defaults
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
 */
if(!isset($CONF) || !is_array($CONF)){
    $CONF=array();
}


/*
 * You can create seperate settings files by adding them to a folder called
 * settings which you will need to create. The names (without the ".php") should 
 * be added to the conf settings array. 
 * 
 * + If the folder does not exist this conf list will be ignored. 
 * + CONF values in the folder will override settings made here in the order 
 *   that they are listed.
 * + The files must end .php and should _not_ be writable on production systems.
 * + If the file does not exist it will be skipped.
 * 
 */

$CONF['settings'] = array('site','database','mappings','email');



$CONF['map']=array(
    'foobar'=>'test'
);

$CONF['subscribe']=array();


// DATABAS CONNECTION

$CONF['DB'] = array();

$CONF['DB'][0] = array();
$CONF['DB'][0]['user']          = '';
$CONF['DB'][0]['password']      = '';
$CONF['DB'][0]['database']      = '';
$CONF['DB'][0]['host']          = '';

/*
 // additional connections can be defined in a simialr manor.
$CONF['DB'][1] = array();
$CONF['DB'][1]['user']          = '';
$CONF['DB'][1]['password']      = '';
$CONF['DB'][1]['database']      = '';
$CONF['DB'][1]['host']          = '';
 */

// SALT is a long random sting used in one way encryption.
$CONF['salt']='!FV_F++FD=RFe.t53435@@#~~~~B;:';
// So long as it is UNGUESSABLE it is good. You will never have to remember it
// but all passwords that rely on it will instently be invalid and need to be
// reset.

// set a default timezone.
date_default_timezone_set('Europe/London');