<?php
/**
 * @package php-font-lib
 * @link    http://php-font-lib.googlecode.com/
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @version $Id: font_table_kern.cls.php 34 2011-10-23 13:53:25Z fabien.menager $
 */

/**
 * `kern` font table.
 * 
 * @package php-font-lib
 */
class Font_Table_kern extends Font_Table {
  protected function _parse(){
    $font = $this->getFont();
    
    $tableOffset = $font->pos();
    
    $data = $font->unpack(array(
      "version"    => self::uint16,
      "nTables"    => self::uint16,
    
      // only the first subtable will be parsed
      "subtableVersion" => self::uint16,
      "length"     => self::uint16,
      "coverage"   => self::uint16,
    ));
    
    $data["format"] = ($data["coverage"] >> 8);
    
    $subtable = array();
    
    switch($data["format"]) {
      case 0:
      $subtable = $font->unpack(array(
        "nPairs"        => self::uint16,
        "searchRange"   => self::uint16,
        "entrySelector" => self::uint16,
        "rangeShift"    => self::uint16,
      ));
      
      $pairs = array();
      $tree = array();
       
      for ($i = 0; $i < $subtable["nPairs"]; $i++) {
        $left  = $font->readUInt16();
        $right = $font->readUInt16();
        $value = $font->readInt16();
        
        $pairs[] = array(
          "left"  => $left,
          "right" => $right,
          "value" => $value,
        );
        
        $tree[$left][$right] = $value;
      }
      
      //$subtable["pairs"] = $pairs;
      $subtable["tree"] = $tree;
      break;
      
      case 1:
      case 2:
      case 3:
      break;
    }
    
    $data["subtable"] = $subtable;
    
    $this->data = $data;
  }
}