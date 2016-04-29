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
 * Set these values so that Gneiss knows how to correctly attribute emails. 
 * 
 * While it is true that core does not use these values, Gneiss::email and maybe
 * some others will do.  
 */
$CONF['email-from'] = 'The Gneiss Site Team';
$CONF['email-address'] = 'no-reply@example.com';