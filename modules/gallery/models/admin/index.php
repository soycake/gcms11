<?php
/*
 * @filesource gallery/models/admin/index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Gallery\Admin\Index;

use \Kotchasan\ArrayTool;

/**
 *  Model สำหรับอ่านข้อมูลโมดูล
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * อ่านข้อมูลโมดูล
   *
   * @param int $module_id
   * @return object|null ข้อมูลโมดูล (Object) หรือ null หากไม่พบบ
   */
  public static function module($module_id)
  {
    $model = new static;
    // ตรวจสอบโมดูลที่เรียก
    $index = $model->db()->createQuery()
      ->select('id module_id', 'module', 'owner', 'config')
      ->from('modules')
      ->where(array(
        array('id', $module_id),
        array('owner', 'gallery')
      ))
      ->limit(1)
      ->toArray()
      ->execute();
    if (empty($index)) {
      return null;
    } else {
      // ค่าติดตั้งเริ่มต้น
      $default = array(
        'icon_width' => 400,
        'icon_height' => 300,
        'image_width' => 800,
        'img_typies' => array('jpg', 'jpeg'),
        'rows' => 3,
        'cols' => 4,
        'sort' => 1,
        'can_write' => array(1),
        'can_config' => array(1)
      );
      $default = ArrayTool::unserialize($index[0]['config'], $default);
      unset($index[0]['config']);
      $index = ArrayTool::merge($default, $index[0]);
      return (object)$index;
    }
  }
}