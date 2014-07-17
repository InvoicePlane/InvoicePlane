<?php
/**
 * @package dompdf
 * @link    http://www.dompdf.com/
 * @author  Benj Carson <benjcarson@digitaljunkies.ca>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @version $Id: null_frame_decorator.cls.php 448 2011-11-13 13:00:03Z fabien.menager $
 */

/**
 * Dummy decorator
 *
 * @access private
 * @package dompdf
 */
class Null_Frame_Decorator extends Frame_Decorator {

  function __construct(Frame $frame, DOMPDF $dompdf) {
    parent::__construct($frame, $dompdf);
    $style = $this->_frame->get_style();
    $style->width = 0;
    $style->height = 0;
    $style->margin = 0;
    $style->padding = 0;
  }

}
