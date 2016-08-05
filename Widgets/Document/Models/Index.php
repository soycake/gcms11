<?php
/*
 * @filesource Widgets/Document/Models/Index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Widgets\Document\Models;

use \Kotchasan\Language;

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
   * รายการบทความ
   *
   * @param int $module_id
   * @param string $categories
   * @param string $show_news
   * @param int $sort
   * @param int $limit
   * @return array
   */
  public static function get($module_id, $categories, $show_news, $sort, $limit)
  {
    // query
    $model = new static;
    // เรียงลำดับ
    $sorts = array('Q.`last_update` DESC,Q.`id` DESC', 'Q.`create_date` DESC,Q.`id` DESC', 'Q.`published_date` DESC,Q.`last_update` DESC', 'Q.`id` DESC');
    $where = array(
      array('Q.module_id', (int)$module_id),
      array('Q.index', 0),
      array('Q.published', 1),
      array('Q.published_date', '<=', date('Y-m-d'))
    );
    if (!empty($categories)) {
      $where[] = "Q.`category_id` IN ($categories)";
    }
    if (!empty($show_news) && preg_match('/^[a-z0-9]+$/', $show_news)) {
      $where[] = "Q.`show_news` LIKE '%$show_news=1%'";
    }
    return $model->db()->createQuery()
        ->select('Q.id', 'D.topic', 'Q.alias', 'D.description', 'Q.picture', 'Q.create_date', 'Q.last_update', 'Q.comment_date', 'C.topic category', 'Q.member_id', 'Q.sender', 'U.status', 'Q.comments', 'Q.visited')
        ->from('index Q')
        ->join('index_detail D', 'INNER', array(array('D.id', 'Q.id'), array('D.module_id', 'Q.module_id'), array('D.language', array(Language::name(), ''))))
        ->join('user U', 'LEFT', array('U.id', 'Q.member_id'))
        ->join('category C', 'LEFT', array(array('C.category_id', 'Q.category_id'), array('C.module_id', 'Q.module_id')))
        ->where($where)
        ->order($sorts[(int)$sort])
        ->limit((int)$limit)
        ->cacheOn()
        ->execute();
  }
}