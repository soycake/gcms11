<?php
/*
 * @filesource gallery/models/module.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Gallery\Module;

use \Kotchasan\Language;
use \Kotchasan\Http\Request;
use \Kotchasan\ArrayTool;

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
  public static function get(Request $request, $index)
  {
    // Model
    $model = new static;
    $query = $model->db()->createQuery()
      ->from('index_detail D')
      ->join('index I', 'INNER', array(array('I.index', 1), array('I.id', 'D.id'), array('I.module_id', 'D.module_id'), array('I.language', 'D.language')))
      ->where(array(array('I.module_id', (int)$index->module_id), array('D.language', array(Language::name(), ''))))
      ->cacheOn()
      ->toArray();
    return ArrayTool::merge($index, $query->first(array(
          'D.topic',
          'D.detail',
          'D.keywords',
          'D.description'
    )));
  }
}