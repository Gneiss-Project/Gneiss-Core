<?php
namespace modules\core;
use modules\core\interfaces as i;
use modules\core\classes as c;

/*                                    .,.
                                ,nMMMMMMb.
                     .,,,.     dP""""MMMMMb            -
                  .nMMMMMMMn. `M     MMMMMM>
                 uMMMMMMMMMMMb Mx   uMMMMMM   . .,,.
                 MMMMMMP" '"4M.`4MMMMMMMMM' ?J$$$$ccc"=
                 MMMMMM     4M'  "4MMMMP" z$c,"?$c,""$c$h=
                 "MMMMMb,..,d' hdzc   ,cc$$$$$$$$$$L.$c "
     c"`          `4MMMMMMP" ,$$$$$$c,$$$$$$$$$$$$"?? ,c?$.
                 ,cc,.  .,zc$$$$$3?$?$P"?  " "  "    $$$$h`           -
              ,c$$$", c$$$$$$$$PP                   ,$$$$$c
              )$$$$$$$P"$$$$$"  "                   $c,"$c/     *
    '       ,cc$$$$$"".d$?? "                      J$$hc$=
           ,c" d$$??c="         '!!               z$$$$$??
=         `??-$""? ?"             '   .          d$$$""$ccc
            =`" "        "                     ,d$$$$P $$$"-
              d$$h.        ,;!               ,d$$$$P" .$$$h.     =
             <L$$$$c     <!!!!>.         !> -$$$$$P zd$$$""$.
              " "$" $c  !  `!,!'!;,,;!--!!!! `$$P .d$$$$$h $c"
                 $ d$".`!!  `'''`!!!!    !!,! $C,d$$$$$$$$.`$-
        "        $."$ '  ,zcc=cc ``'``<;;!!!> $?$$$$$$$$"$$F"
                 "=" .J,??"" .$",$$$$- '!'!!  $<$$$$$$$$h ?$=.
                 ".d$""",chd$$??""""".r >  !'.$?$$$$$$$$"=,$cc,      #
               ,c$$",c,d$$$"' ,c$$$$$$"'!;!'.$F.$$$$$$$$. $$$."L
              d $$$c$$$$$" ,hd$$$$??" z !!`.P' `$$$$$$$?? $$$P ..
           ,cd J$$$$$$$P',$$$$$$".,c$$" `.d$h h $$$$$$P"?.?$$ ,$$""
          c$$' $$$$$$$C,$$$$$$"',$$$P"z $$$$"-$J"$$$$$ ,$$?$F.$L?$$L
         ,$",J$$$$$$$$$$$$$$"  ,$$$C,r" $$$$$$$"z$$$$$$$$$,`,$$"d$$??
        .dF $$$$$$$$$$$$$$P" $$$$$$$F.-,$$$$$$$$$$$$$$$$$$$%d$$F$$$$cc,-
       ,$( c$$$$$$$$$$$$$$h,c$$$$$$c=".d$$   """',,.. .,.`""?$$$$$$.`?c .
       d$$.$$$$$$$$$$$$$$$$$$$P""P" ,c$$$$$c,"=d$$$$$h."$$$c,"$$$$$$. $$c
      $$$$$$$$$$$$$$$$$$$$$P"",c"  ,$$$$$$$$$h. `?$$$$$$$$$$P'.""`?$$.3$cr
    z$$$$$$$$$ 3?$$$$$$$$$ccc$P   ,$$$$$$$$$$$$$h.`"$$$$$$$$$c?$c .`?$$$$c
  dd$$"J$$$$$$$P $$$$$$$$F?$$$" .d$$$$$$$$$$$$$$$$L.`""???$$$$$$$$$c  ?$C?
 c$$$$ $$3$$$$$$ `$$$$$h$$P"".. $$$$$$$$$$$$$$$??""" ,ccc3$$$$$$$$$ hL`?$C
h$$$$$>`$$$$$$$$$.$$$$$??',c$"  $$$$$P)$$$$$P" ,$$$$$$$$$$$$$$$$$$$ "$ ."$
$$$$$$h.$C$$$$$$$$$$$ccc$P"3$$ ,$$$$$ "$$$P" =""',d$$???""',$$$$$$$$ $cc "
$$$$$$$$$$$$$$$$P$$?$$$.,c??",c$$$$$L$J$P"  zchd$$$$,ccc$$$$$$$$$$$$$$$$
$$$$$$$$$$$$$$$$$$$$3$$$F" c$$$$$$$$$$  ,cc$$$$$$P"""""`"""??$$$$$$$$$$$P/
$$$$$$$$$$$$$$$??)$$$JC".d$$4?$$$$$$$$$c .,c=" .,,J$$$$$$$$hc,J$$$$$$$$$$%
$$$$$$$$$$$$"".,c$$$$CF $$$h"d$$$$$$$$$h "',cd$????$$$$$$$$$$$$$$$$$$$$$$z
$$$$$$$$$P??"3$$P??$$" ,$$$FJ?$$$$$$$$$$cc,,. ,,.  =$$$$$$$$$$$$$$$$$$$$$$
$$$$$$$$$hc$$$$$r$P"'  $$$$."-3$$$$$$$$$$$$$"$"$$$c, ""????$$$$$$$$$$$$$$$
$$$$$3$$$$$$$h.zccc$$ ,$$$$L`,$$$$$$$$$$$$$L,"- $$$$$cc -cc??)$$$$$$$$$$$*/

/**
 * cookieMonster is a helper lib that provides a b(r)unch of standardised cookie 
 * handling methods including session cookies, session delaied coocies and etc.
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
class cookiemonster extends c\module_lib {
    private $started=false;
    
    public function __construct(){
        $this->delay_to_cookie();
    }


    public function start_session(){
        if($this->started){
            return TRUE;
        }
        \session_start();
        $this->started = TRUE;
        return FALSE;
    }
    
    public function set_session($name,$value){
        $this->start_session();
        $_SESSION[$name] = $value;
    }
    public function isset_session($name){
        $this->start_session();
        return isset($_SESSION[$name]);
    }
    public function get_session($name){
        $this->start_session();
        if($this->isset_session($name)){
            return $_SESSION[$name];
        }
        return '';
    }
    
    protected function delay_to_cookie(){
        $ar = $this->get_session('_DELAY');
        if(!is_array($ar) || count($ar)<1){
            return TRUE;
        }
        foreach($ar as $k=>$d){
            if($this->try_cookie($d[0], $d[1], $d[2])){
                unset($ar[$k]);
            }
        }
        $this->set_session('_DELAY', $ar);
    }
    
    /**
     * Adds the cookie data to the session in the hope that the cookie can be 
     * sent before the session ends.
     * @param type $cookie
     * @param type $value
     * @param type $time 
     */
    public function delay_cookie($cookie,$value,$time=0){
        $this->start_session();
        $ar = array();
        if($this->isset_session('_DELAY')){
            $ar = $this->get_session('_DELAY');
        }
        if(!is_array($ar)){
            $ar = array();
        }
        $ar[] = array($cookie,$value,$time);
        $this->set_session('_DELAY', $ar);
    }
    
    public function set_cookie($cookie,$value,$time=0){
        $cookieCONF = core::get()->factory()->get_config('cookie',array('path'=>'/','domain'=>'.'));
        core::get()->debug()->log("COOKIE[{$cookie}]", $value, FALSE, 7);
        if($time!==0){
            $time= time()+$time;
        }
        if($cookieCONF['domain']=='localhost'){
            $cookieCONF['domain']=null;
        }
        if(!is_array($value)){
            setcookie($cookie, $value, $time, $cookieCONF['path'], $cookieCONF['domain']);
        }else{
            foreach($value as $val=>$ue){
                core::get()->debug()->log("{$cookie}[{$val}]", $ue, FALSE, 8);
                if(setcookie("{$cookie}[{$val}]", $ue, $time, $cookieCONF['path'], $cookieCONF['domain'])){
                    core::get()->debug()->log('COOKIE RESULT',"SET {$cookie}[{$val}]={$ue}", FALSE, 8);
                }else{
                    core::get()->debug()->log('COOKIE RESULT',"NOPE {$cookie}[{$val}]={$ue}", FALSE, 8);
                }
            }
        }
    }
    
    public function unset_cookie($name){
        $this->set_cookie($name,null,-1);
        $this->module()->debug()->log('UNSET COOKIE:',$name);
    }
    
    public function get_cookie($name){
        if(isset($_COOKIE[$name])){
            return $_COOKIE[$name];
        }
        return null;
    }


    /**
     * If headers not sent sets cookie otherwise does nothing to set cookie next
     * page load use cookie_or_delay() instead.
     * @param string $cookie
     * @param string $value
     * @param int $time 
     */
    public function try_cookie($cookie,$value,$time=0){
        if(!\headers_sent()){
            $this->set_cookie($cookie, $value, $time);
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * Sets the cookie but if that cannot be done it queues the cookie to be set
     * on another page load.
     * 
     * @param string $cookie
     * @param string $value
     * @param int $time 
     */
    public function cookie_or_delay($cookie,$value,$time=0){
        if(!\headers_sent()){
            $this->set_cookie($cookie, $value, $time);
            return true;
        }else{
            $this->delay_cookie($cookie, $value, $time);
            return false;
        }
    }
        
}
