<?php
/*
 * @filesource gallery/controllers/index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Gallery\Index;

use \Gcms\Gcms;
use \Kotchasan\Http\Request;

/**
 * Controller หลัก สำหรับแสดง frontend ของ GCMS
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\Controller
{

  /**
   * Controller หลักของโมดูล ใช้เพื่อตรวจสอบว่าจะเรียกหน้าไหนมาแสดงผล
   *
   * @param Request $request
   * @param object $module ข้อมูลโมดูลจาก database
   * @return object
   */
  public function init(Request $request, $module)
  {
    // รายการที่เลือก
    $id = $request->request('id')->toInt();
    // ตรวจสอบโมดูลและอ่านข้อมูลโมดูล
    $index = \Document\Module\Model::get($request, $module);
    if (empty($index)) {
      // 404
      $page = createClass('Index\PageNotFound\Controller')->init($request, 'gallery');
    } elseif (empty($id)) {
      // หน้าแสดงอัลบัม
      $page = createClass('Gallery\Album\View')->index($request, $index);
    } else {
      // ดูอัลบัม
      $index->id = $id;
      $page = createClass('Gallery\View\View')->index($request, $index);
    }
    return $page;
  }

  /**
   * ฟังก์ชั่นสร้าง URL ของอัลบัม
   *
   * @param string $module ชื่อโมดูล
   * @param int $id ID ของบทความ
   * @return string
   */
  public static function url($module, $id)
  {
    return Gcms::createUrl($module, '', 0, 0, 'id='.$id);
  }
}