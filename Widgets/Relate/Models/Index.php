<?php
/*
 * @filesource Widgets/Relate/Models/Index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Widgets\Relate\Models;

use \Kotchasan\Language;
use \Kotchasan\Date;
use \Kotchasan\ArrayTool;

/**
 * อ่านข้อมูลโมดูล
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Index extends \Kotchasan\Model
{

  /**
   * อ่านข้อมูลโมดูล
   *
   * @param int $id
   * @return object|bool คืนค่าข้อมูลที่พบ (object) ไม่พบคืนค่า false
   */
  public function getModule($id, $count)
  {
    if ($id > 0) {
      // วันนี้
      $c_date = Date::mktimeToSqlDate();
      // ภาษา
      $lng = Language::name();
      // อ่านโมดูล จาก id ของ บทความ
      $index = $this->db()->createQuery()
        ->from('index Q')
        ->join('index_detail D', 'INNER', array(array('D.id', 'Q.id'), array('D.module_id', 'Q.module_id')))
        ->join('modules M', 'INNER', array('M.id', 'D.module_id'))
        ->where(array(
          array('Q.id', $id),
          array('M.owner', 'document'),
          array('Q.index', '0')
        ))
        ->toArray()
        ->cacheOn()
        ->first('M.config', 'M.module', 'D.relate', 'Q.id', 'Q.module_id');
      if ($index && $index['relate'] !== '') {
        // config
        $index = ArrayTool::unserialize($index['config'], $index);
        unset($index['config']);
        // relate
        $qs = array();
        foreach (explode(',', $index['relate']) as $q) {
          $qs[] = "D.`relate` LIKE '%$q%'";
        }
        $qs = implode(' OR ', $qs);
        // ชื่อตาราง
        $table_index = $this->getFullTableName('index');
        $table_index_detail = $this->getFullTableName('index_detail');
        $table_user = $this->getFullTableName('user');
        $select = array('Q.id', 'D.topic', 'Q.alias', 'Q.picture', 'Q.comment_date', 'Q.last_update', 'Q.create_date', 'D.description', 'Q.comments', 'Q.visited', 'Q.member_id', 'D.language');
        $where = array(
          array('Q.module_id', (int)$index['module_id']),
          array('Q.published', '1'),
          array('Q.published_date', '<=', $c_date),
          array('Q.index', '0'),
          array('Q.id', '>', $id),
          '('.$qs.')',
          array('D.language', array($lng, ''))
        );
        // newest
        $q1 = $this->db()->createQuery()
          ->select($select)
          ->from('index Q')
          ->join('index_detail D', 'INNER', array(array('D.id', 'Q.id'), array('D.module_id', 'Q.module_id')))
          ->where($where)
          ->order('Q.create_date');
        $sql1 = 'SELECT @n:=@n+1 AS `row`,Q.* FROM ('.$q1->text().') AS Q, (SELECT @n:=0) AS R';
        // older
        $where[4][1] = '<';
        $q1->select($select)->where($where)->order('Q.create_date DESC');
        $sql2 = 'SELECT @m:=@m+1 AS `row`,Q.* FROM ('.$q1->text().') AS Q, (SELECT @m:=0) AS L';
        $sql3 = $this->db()->createQuery()
          ->select()
          ->from(array("($sql1) UNION ($sql2)", 'N'))
          ->order('N.row')
          ->limit($count);
        $query = $this->db()->createQuery()
          ->select('Y.id', 'Y.topic', 'Y.alias', 'Y.picture', 'Y.comment_date', 'Y.last_update', 'Y.create_date', 'Y.description', 'Y.comments', 'Y.visited', 'U.status', 'U.id member_id', 'U.fname', 'U.lname', 'U.email')
          ->from(array($sql3, 'Y'))
          ->join('user U', 'LEFT', array('U.id', 'Y.member_id'))
          ->order('Y.create_date');
        $index['items'] = $query->cacheOn()->execute();
        return (object)$index;
      }
    }
    return false;
  }
}