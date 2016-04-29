<?php
/*
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

/**
 * @author Lord Matt
 * 
 * Set these values so that Gneiss knows how to map URI requests. If you want to
 * have Gneiss assume an unmatched URI should be passed to a module ("fallback")
 * then comment out the 404 option.
 * 
 * These defaults assume that you have also installed
 * - Gneiss::index
 * - Gneiss::content
 */
$CONF['map']['default'] = array('index','index'); # default
$CONF['map']['404'] = array('index','404');
$CONF['map']['alias'] = array(); 
$CONF['map']['fallback'] = 'content';