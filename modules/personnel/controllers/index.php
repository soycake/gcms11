<?php
/*
 * @filesource personnel/controllers/index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Personnel\Index;

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
   * @param object $index ข้อมูลโมดูล
   * @return object
   */
  public function init(Request $request, $index)
  {
    // รายการที่เลือก
    $id = $request->request('id')->toInt();
    // ตรวจสอบโมดูลและอ่านข้อมูลโมดูล
    $index = \Index\Module\Model::getDetails($index);
    if (empty($index)) {
      // 404
      $page = createClass('Index\PageNotFound\Controller')->init($request, 'personnel');
    } elseif (!empty($id)) {
      // แสดงข้อมูลบุคคลาการ
      $page = createClass('Personnel\View\View')->index($request, $index);
    } else {
      // แสดงรายการบุคคลากร
      $page = createClass('Personnel\Lists\View')->index($request, $index);
    }
    return $page;
  }

  /**
   * ฟังก์ชั่นสร้าง URL
   *
   * @param string $module ชื่อโมดูล
   * @param int $id ID
   * @return string
   */
  public static function url($module, $id)
  {
    return Gcms::createUrl($module, '', 0, $id);
  }
}