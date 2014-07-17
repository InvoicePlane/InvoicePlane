<?php
/**
 * @package php-font-lib
 * @link    http://php-font-lib.googlecode.com/
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @version $Id: font_table_head.cls.php 34 2011-10-23 13:53:25Z fabien.menager $
 */

/**
 * `head` font table.
 * 
 * @package php-font-lib
 */
class Font_Table_head extends Font_Table {
  protected $def = array(
    "tableVersion"       => self::Fixed,
    "fontRevision"       => self::Fixed,
    "checkSumAdjustment" => self::uint32,
    "magicNumber"        => self::uint32,
    "flags"              => self::uint16,
    "unitsPerEm"         => self::uint16,
    "created"            => self::longDateTime,
    "modified"           => self::longDateTime,
    "xMin"               => self::FWord,
    "yMin"               => self::FWord,
    "xMax"               => self::FWord,
    "yMax"               => self::FWord,
    "macStyle"           => self::uint16,
    "lowestRecPPEM"      => self::uint16,
    "fontDirectionHint"  => self::int16,
    "indexToLocFormat"   => self::int16,
    "glyphDataFormat"    => self::int16,
  );
  
  protected function _parse(){
    parent::_parse();
    
    if($this->data["magicNumber"] != 0x5F0F3CF5) {
      throw new Exception("Incorrect magic number (".dechex($this->data["magicNumber"]).")");
    }
  }
}