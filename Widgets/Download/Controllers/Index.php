<?php
/*
 * @filesource Widgets/Download/Controllers/Index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Widgets\Download\Controllers;

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
    if (preg_match('/^[0-9]+$/', $query_string['module'])) {
      // ระบุ ID มา
      $file = \Widgets\Download\Models\Download::get($query_string['module']);
      if ($file) {
        return \Widgets\Download\Views\Download::render($file);
      }
    } elseif (preg_match('/^[a-z0-9]{3,}$/', $query_string['module']) && isset(\Gcms\Gcms::$install_modules[$query_string['module']])) {
      // module
      $index = \Gcms\Gcms::$install_modules[$query_string['module']];
      // ค่าที่ส่งมา
      $query_string['cat'] = isset($query_string['cat']) ? $query_string['cat'] : 0;
      $query_string['count'] = isset($query_string['count']) ? (int)$query_string['count'] : 10;
      return \Widgets\Download\Views\Lists::render($index, $query_string);
    }
    return '';
  }
}