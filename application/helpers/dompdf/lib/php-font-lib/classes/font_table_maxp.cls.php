<?php
/**
 * @package php-font-lib
 * @link    http://php-font-lib.googlecode.com/
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @version $Id: font_table_maxp.cls.php 37 2011-11-07 07:38:44Z fabien.menager $
 */

/**
 * `maxp` font table.
 * 
 * @package php-font-lib
 */
class Font_Table_maxp extends Font_Table {
  protected $def = array(
    "version"               => self::Fixed,
    "numGlyphs"             => self::uint16,
    "maxPoints"             => self::uint16,
    "maxContours"           => self::uint16,
    "maxComponentPoints"    => self::uint16,
    "maxComponentContours"  => self::uint16,
    "maxZones"              => self::uint16,
    "maxTwilightPoints"     => self::uint16,
    "maxStorage"            => self::uint16,
    "maxFunctionDefs"       => self::uint16,
    "maxInstructionDefs"    => self::uint16,
    "maxStackElements"      => self::uint16,
    "maxSizeOfInstructions" => self::uint16,
    "maxComponentElements"  => self::uint16,
    "maxComponentDepth"     => self::uint16,
  );
  
  function _encode(){
    $font = $this->getFont();
    $this->data["numGlyphs"] = count($font->getSubset());
    
    return parent::_encode();
  }
}