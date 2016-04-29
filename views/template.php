<?php
/**
 * Generic Master template
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

    ?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <style>
            html { font-size: 24px; }
            body{
                padding:0;
                margin:0;
            }
            header {
                background-color:whitesmoke;
                border-bottom: 3px solid #666;
                margin: 0 0 0.5em 0;
            }
            footer {
                background-color:#EEEEEE;
                border-top: 3px solid #555;
                margin: 0.5em 0 0 0;
                
            }
            #paper{
                text-align:center;
                margin:auto;
                width:1200px;
            }
            main {
                width:800px;
                float:left;
                text-align:left;
            }
            #sidebar {
                width:300px;
                float:right;
            }
            footer, .vc {
                clear:both;
            }
            .message {
                margin:0.4em auto;
                padding:0.4em;
                border:1px solid #666;
                background-color: #FFFFCC;
                color:#111;
            }
            .button {
                background-color: #4CAF50; /* Green */
                border: none;
                color: white;
                padding: 15px 32px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                margin-top:12px;
            }
            .middle, .middle input {
                text-align:center;
                margin-right:auto;
                margin-left:auto;
            }
        </style>
        <?=$this->view_event('headstyle')?>
        <title><?=$this->view_event('title')?></title>
        <?=$this->view_event('headmeta')?>
    </head><?php
    // a good place to flush();
    // https://developer.yahoo.com/performance/rules.html
    $this->vFlush();
    ?>
    <body>
        <header>
            <h1 style="text-align:center;margin:0 0 0.5em 0;">Gneiss</h1>
            <div style="margin:1em auto;width:35%;border:1px solid #666;background-color:#FFFFCC;padding:0.5em;font-size:0.8em;"><b>gneiss</b>: <i>noun</i>: a metamorphic rock with a banded or foliated 
    structure, typically coarse-grained and consisting mainly of feldspar, 
    quartz, and mica.</div>
            <?=$this->view_event('header')?>
            <div class="vc"></div>
        </header>
        <div id="paper">
            <main>
                <?=$this->get_primary_view()?>
            </main>    
            <section id="sidebar">
                <?=$this->view_event('sidebar')?>
            </section>
            <div class="vc"></div>
        </div>
        <footer>
            <?=$this->view_event('footer')?>
        </footer>
        <?=$this->view_event('scripts')?>
    </body>
</html>
