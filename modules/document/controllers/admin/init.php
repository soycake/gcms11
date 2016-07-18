<?php
/*
 * @filesource document/controllers/admin/init.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Document\Admin\Init;

use \Index\Index\Model as Menu;

/**
 * จัดการการตั้งค่าเริ่มต้น
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\Controller
{

  /**
   * คำอธิบายเกี่ยวกับโมดูล ถ้าไม่มีฟังก์ชั่นนี้ โมดูลนี้จะไม่สามารถใช้ซ้ำได้
   */
  public static function description()
  {
    return '{LNG_Modules for writing blogs, news or other basic modules that are similar}';
  }

  /**
   * ฟังก์ชั่นเรียกโดย admin
   */
  public static function init($items)
  {
    if (!empty($items)) {
      // เมนู
      foreach ($items AS $item) {
        Menu::$menus['modules'][$item->module]['write'] = '<a href="index.php?module=document-write&amp;mid='.$item->id.'"><span>{LNG_Add New} {LNG_Content}</span></a>';
      }
    }
  }
}