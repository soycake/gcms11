<?php
/*
 * @filesource Widgets/Twitter/Controllers/Index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Widgets\Twitter\Controllers;

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
    if (empty(self::$cfg->twitter)) {
      self::$cfg->twitter = array(
        'height' => 214,
        'user' => 'gcmscms',
        'show_facepile' => 1,
        'small_header' => 0,
        'hide_cover' => 0
      );
    }
    foreach (self::$cfg->twitter as $key => $value) {
      if (!isset($query_string[$key])) {
        $query_string[$key] = $value;
      }
    }
    return \Widgets\Twitter\Views\Index::render($query_string);
  }
}