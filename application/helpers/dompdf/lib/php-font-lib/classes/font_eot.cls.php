<?php
/**
 * @package php-font-lib
 * @link    http://php-font-lib.googlecode.com/
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @version $Id: font_eot.cls.php 34 2011-10-23 13:53:25Z fabien.menager $
 */

require_once dirname(__FILE__)."/font_truetype.cls.php";

/**
 * EOT font file.
 * 
 * @package php-font-lib
 */
class Font_EOT extends Font_TrueType {
  private $origF;
  private $fileOffset = 0;
  
  public $header;
  
  function parseHeader(){
    $this->header = $this->unpack(array(
      "EOTSize"        => self::uint32,
      "FontDataSize"   => self::uint32,
      "Version"        => self::uint32,
      "Flags"          => self::uint32,
    ));
    
    $this->header["FontPANOSE"] = $this->read(10);
    
    $this->header += $this->unpack(array(
      "Charset"        => self::uint8,
      "Italic"         => self::uint8,
      "Weight"         => self::uint32,
      "fsType"         => self::uint16,
      "MagicNumber"    => self::uint16,
      "UnicodeRange1"  => self::uint32,
      "UnicodeRange2"  => self::uint32,
      "UnicodeRange3"  => self::uint32,
      "UnicodeRange4"  => self::uint32,
      "CodePageRange1" => self::uint32,
      "CodePageRange2" => self::uint32,
      "CheckSumAdjustment" => self::uint32,
      "Reserved1"      => self::uint32,
      "Reserved2"      => self::uint32,
      "Reserved3"      => self::uint32,
      "Reserved4"      => self::uint32,
      "Padding1"       => self::uint16,
      "FamilyNameSize" => self::uint16,
    ));
  }
  
  function parse() {
    exit("EOT not supported yet");
  }
  
  public function readUInt16() {
    $a = unpack('vv', $this->read(2));
    return $a['v'];
  }

  public function readUInt32() {
    $a = unpack('VV', $this->read(4));
    return $a['V'];
  }
}
