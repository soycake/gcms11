<?php
/*
 * @filesource board/models/module.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Board\Module;

use \Gcms\Gcms;
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
    // หมวดหมู่
    $categories = array();
    $value = $request->request('cat')->filter('\d,');
    if (!empty($value)) {
      foreach (explode(',', $value) as $v) {
        $v = (int)$v;
        if ($v > 0) {
          $categories[$v] = $v;
        }
      }
    }
    // Model
    $model = new static;
    // จำนวนหมวดในโมดูล
    $query = $model->db()->createQuery()->selectCount()->from('category')->where(array('module_id', 'D.module_id'));
    $select = array(
      'D.topic',
      'D.detail',
      'D.keywords',
      array($query, 'categories'),
      'D.description'
    );
    $query = $model->db()->createQuery()
      ->from('index_detail D')
      ->join('index I', 'INNER', array(
        array('I.index', 1),
        array('I.id', 'D.id'),
        array('I.module_id', 'D.module_id'),
        array('I.language', 'D.language')
      ))
      ->where(array(
        array('I.module_id', (int)$index->module_id),
        array('D.language', array(Language::name(), ''))
      ))
      ->cacheOn()
      ->toArray();
    if (sizeof($categories) == 1) {
      // มีการเลือกหมวด เพียงหมวดเดียว
      $select[] = 'C.category_id';
      $select[] = 'C.topic c_topic';
      $select[] = 'C.detail c_description';
      $select[] = 'C.icon';
      $select[] = 'C.config';
      $query->join('category C', 'LEFT', array(
        array('C.category_id', (int)reset($categories)),
        array('C.module_id', 'D.module_id')
      ));
    }
    $result = $query->first($select);
    if ($result) {
      foreach ($result as $key => $value) {
        switch ($key) {
          case 'c_topic':
            $index->topic = Gcms::ser2Str($value);
            break;
          case 'c_description':
            $index->description = Gcms::ser2Str($value);
            break;
          case 'icon':
            $index->icon = Gcms::ser2Str($value);
            break;
          case 'config':
            $value = @unserialize($value);
            if (is_array($value)) {
              foreach ($value as $k => $v) {
                $index->$k = $v;
              }
            }
            break;
          default:
            $index->$key = $value;
            break;
        }
      }
    }
    if (empty($index->category_id)) {
      $index->category_id = empty($categories) ? 0 : (int)reset($categories);
    }
    return $index;
  }

  /**
   * อ่านข้อมูลกระทู้สำหรับการแก้ไข
   *
   * @param int $id ID ที่แก้ไข
   * @param object $index ข้อมูลโมดูล
   * @return object|null ข้อมูล (Object), false ถ้าไม่พบ
   */
  public static function getQuestionById($id, $index)
  {
    $model = new static;
    $query = $model->db()->createQuery()
      ->from('board_q Q')
      ->join('index_detail D', 'INNER', array('D.module_id', 'Q.module_id'))
      ->join('category C', 'LEFT', array(array('C.category_id', 'Q.category_id'), array('C.module_id', 'Q.module_id')))
      ->where(array(
        array('Q.id', $id),
        array('Q.module_id', (int)$index->module_id),
        array('D.id', (int)$index->index_id)
      ))
      ->toArray()
      ->first('Q.*', 'C.config', 'D.description', 'D.keywords');
    if ($query) {
      $query = ArrayTool::unserialize($query['config'], $query);
      unset($query['config']);
      foreach ($query as $k => $v) {
        $index->$k = $v;
      }
      return $index;
    }
    return false;
  }

  /**
   * อ่านข้อมูลความคิดเห็นสำหรับการแก้ไข
   *
   * @param int $id ID ที่แก้ไข
   * @param object $index ข้อมูลโมดูล
   * @return object|null ข้อมูล (Object), false ถ้าไม่พบ
   */
  public static function getCommentById($id, $index)
  {
    $model = new static;
    $query = $model->db()->createQuery()
      ->from('board_r R')
      ->join('board_q Q', 'INNER', array(array('Q.id', 'R.index_id'), array('Q.module_id', 'R.module_id')))
      ->join('index_detail D', 'INNER', array('D.module_id', 'Q.module_id'))
      ->join('category C', 'LEFT', array(array('C.category_id', 'Q.category_id'), array('C.module_id', 'Q.module_id')))
      ->where(array(
        array('R.id', $id),
        array('Q.module_id', (int)$index->module_id),
        array('D.id', (int)$index->index_id)
      ))
      ->toArray()
      ->first('R.*', 'C.config', 'D.description', 'D.keywords', 'Q.topic', 'Q.category_id', 'C.topic category');
    if ($query) {
      $query = ArrayTool::unserialize($query['config'], $query);
      unset($query['config']);
      foreach ($query as $k => $v) {
        $index->$k = $v;
      }
      return $index;
    }
    return false;
  }
}