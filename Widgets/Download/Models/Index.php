<?php
/*
 * @filesource Widgets/Download/Models/Index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Widgets\Download\Models;

/**
 * อ่านรายการอัลบัมทั้งหมด
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Index extends \Kotchasan\Model
{

  /**
   * รายการกระทู้
   *
   * @param int $module_id
   * @param string $categories
   * @param int $limit
   * @return array
   */
  public static function get($module_id, $categories, $limit)
  {
    // query
    $model = new static;
    $where = array(
      array('module_id', (int)$module_id),
    );
    if (!empty($categories)) {
      $where[] = "`category_id` IN ($categories)";
    }
    return $model->db()->createQuery()
        ->select()
        ->from('download')
        ->where($where)
        ->order('last_update DESC')
        ->limit((int)$limit)
        ->cacheOn()
        ->execute();
  }
}