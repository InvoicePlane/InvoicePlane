<?php
/**
 * @package php-font-lib
 * @link    http://php-font-lib.googlecode.com/
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @version $Id: font_table_name.cls.php 36 2011-11-03 23:02:06Z fabien.menager $
 */

require_once dirname(__FILE__)."/font_table_name_record.cls.php";

/**
 * `name` font table.
 * 
 * @package php-font-lib
 */
class Font_Table_name extends Font_Table {
  private static $header_format = array(
    "format"       => self::uint16,
    "count"        => self::uint16,
    "stringOffset" => self::uint16,
  );
  
  protected function _parse(){
    $font = $this->getFont();
    $data = array();
    
    $tableOffset = $font->pos();
    
    $data = $font->unpack(self::$header_format);
    
    $records = array();
    for($i = 0; $i < $data["count"]; $i++) {
      $record = new Font_Table_name_Record();
      $record_data = $font->unpack(Font_Table_name_Record::$format);
      $record->map($record_data);
      
      $records[] = $record;
    }
    
    $names = array();
    foreach($records as $record) {
      $font->seek($tableOffset + $data["stringOffset"] + $record->offset);
      $s = $font->read($record->length);
      $record->string = Font::UTF16ToUTF8($s);
      $names[$record->nameID] = $record;
    }
    
    $data["records"] = $names;
    
    $this->data = $data;
  }
  
  protected function _encode(){
    $font = $this->getFont();
    
    $records = $this->data["records"];
    $count_records = count($records);
    
    $this->data["count"] = $count_records;
    $this->data["stringOffset"] = 6 + $count_records * 12; // 6 => uint16 * 3, 12 => sizeof self::$record_format
    
    $tableOffset = $font->pos();
    
    $length = $font->pack(self::$header_format, $this->data);
    
    $recordsOffset = $font->pos();
    
    $offset = 0;
    foreach($records as $record) {
      $record->length = mb_strlen($record->getUTF16(), "8bit");
      $record->offset = $offset;
      $offset += $record->length;
      $length += $font->pack(Font_Table_name_Record::$format, (array)$record);
    }
    
    foreach($records as $record) {
      $str = $record->getUTF16();
      $length += $font->write($str, mb_strlen($str, "8bit"));
    }
    
    return $length;
  }
}