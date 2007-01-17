<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/licenses/gpl.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. Notify the above author of modifications to contents within.
 * 
 *  WHY? Because this is a community project -- purposely released under the GPL.
 *    We'd love to have the possiblity to include your derivative works! 
 *    We'd love to coordinate around your development efforts!
 *    We'd love to assist you with your changes!
 *    DON'T BE A STRANGER!
 * 
 ** [END HEADER]**/


class PommoCSVStream{
   var $position; 
   var $varname; 
   function stream_open($path, $mode, $options, &$opened_path){ 
       $url = parse_url($path); 
       $this->varname = $url['host'] ;
       $this->position = 0; 
       return true;
   }
  function stream_read($count){ 
       $ret = substr($GLOBALS[$this->varname], $this->position, $count); 
       $this->position += strlen($ret); 
       return $ret; 
   }
  function stream_eof(){ 
       return $this->position >= strlen($GLOBALS[$this->varname]); 
   } 
   function stream_tell(){ 
       return $this->position; 
   } 
}

?>