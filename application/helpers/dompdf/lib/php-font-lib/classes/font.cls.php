<?php
/**
 * @package php-font-lib
 * @link    http://php-font-lib.googlecode.com/
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @version $Id: font.cls.php 41 2012-02-04 18:01:38Z fabien.menager $
 */

/**
 * Generic font file.
 * 
 * @package php-font-lib
 */
class Font {
  static $debug = false;
  
  /**
   * @param string $file The font file
   * @return Font_TrueType $file
   */
  public static function load($file) {
    $header = file_get_contents($file, false, null, null, 4);
    $class = null;
    
    switch($header) {
      case "\x00\x01\x00\x00": 
      case "true": 
      case "typ1": 
        $class = "Font_TrueType"; break;
      
      case "OTTO":
        $class = "Font_OpenType"; break;
      
      case "wOFF":
        $class = "Font_WOFF"; break;
        
      case "ttcf":
        $class = "Font_TrueType_Collection"; break;
        
      // Unknown type or EOT
      default: 
        $magicNumber = file_get_contents($file, false, null, 34, 2);
        
        if ($magicNumber === "LP") {
          $class = "Font_EOT";
        }
    }
    
    if ($class) {
      require_once dirname(__FILE__)."/".strtolower($class).".cls.php";
      
      $obj = new $class;
      $obj->load($file);
      
      return $obj;
    }
  }
  
  static function d($str) {
    if (!self::$debug) return;
    echo "$str\n";
  }
  
  static function UTF16ToUTF8($str) {
    return mb_convert_encoding($str, "utf-8", "utf-16");
  }
  
  static function UTF8ToUTF16($str) {
    return mb_convert_encoding($str, "utf-16", "utf-8");
  }
}
