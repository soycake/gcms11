<?php
/*
 * @filesource Widgets/Album/Controllers/Index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Widgets\Album\Controllers;

/**
 * Controller หลัก สำหรับแสดงผล Widget
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Index extends \Kotchasan\Controller
{

  /**
   * แสดงผล Widget
   *
   * @param array $query_string ข้อมูลที่ส่งมาจากการเรียก Widget
   * @return string
   */
  public function get($query_string)
  {
    $query_string['rows'] = empty($query_string['rows']) ? 3 : (int)$query_string['rows'];
    if (empty($query_string['cols']) || !in_array($query_string['cols'], array(1, 2, 4, 6, 8))) {
      $query_string['cols'] = 2;
    } else {
      $query_string['cols'] = (int)$query_string['cols'];
    }
    return \Widgets\Album\Views\Index::render($query_string);
  }
}