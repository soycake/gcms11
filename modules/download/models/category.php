<?php
/*
 * @filesource download/models/category.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Download\Category;

use \Gcms\Gcms;
use \Kotchasan\ArrayTool;

/**
 * อ่านข้อมูลหมวดหมู่ (Frontend)
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * อ่านข้อมูลหมวดหมู่ที่สามารถเผยแพร่ได้
   * สำหรับหน้าแสดงรายการหมวดหมู่
   *
   * @param int $module_id
   * @return array คืนค่าแอเรย์ของ Object ไม่มีคืนค่าแอเรย์ว่าง
   */
  public static function all($module_id)
  {
    $result = array();
    if (is_int($module_id) && $module_id > 0) {
      $model = new static;
      $query = $model->db()->createQuery()
        ->select('id', 'category_id', 'topic')
        ->from('category')
        ->where(array(array('module_id', $module_id), array('published', '1')))
        ->cacheOn()
        ->order('category_id');
      foreach ($query->toArray()->execute() as $item) {
        $item['topic'] = Gcms::ser2Str($item, 'topic');
      }
    }
    return $result;
  }
}