<?php
/*
 * @filesource personnel/models/lists.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Personnel\Lists;

use \Kotchasan\Http\Request;

/**
 * อ่านข้อมูลโมดูล
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
   * @param Request $request
   * @param Object $index
   * @return Object
   */
  public static function getItems(Request $request, $index)
  {
    $where = array(
      array('module_id', (int)$index->module_id)
    );
    $category_id = $request->request('cat')->toInt();
    if ($category_id > 0) {
      $where[] = array('category_id', $category_id);
    }
    // Model
    $model = new static;
    $index->items = $model->db()->createQuery()
      ->select()
      ->from('personnel')
      ->where($where)
      ->order('category_id', 'order', 'id')
      ->cacheOn()
      ->execute();
    // คืนค่า
    return $index;
  }
}