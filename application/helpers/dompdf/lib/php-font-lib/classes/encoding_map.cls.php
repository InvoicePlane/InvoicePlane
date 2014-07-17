<?php
/**
 * @package php-font-lib
 * @link    http://php-font-lib.googlecode.com/
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @version $Id: encoding_map.cls.php 34 2011-10-23 13:53:25Z fabien.menager $
 */

/**
 * Encoding map used to map a code point to a Unicode char.
 * 
 * @package php-font-lib
 */
class Encoding_Map {
  private $f;
  
  function __construct($file) {
    $this->f = fopen($file, "r");
  }
  
  function parse(){
    $map = array();
    
    while($line = fgets($this->f)) {
      if (preg_match("/^[\!\=]([0-9A-F]{2,})\s+U\+([0-9A-F]{2})([0-9A-F]{2})\s+([^\s]+)/", $line, $matches)) {
        $unicode = (hexdec($matches[2]) << 8) + hexdec($matches[3]);
        $map[hexdec($matches[1])] = array($unicode, $matches[4]);
      }
    }
    
    ksort($map);
    
    return $map;
  }
}
