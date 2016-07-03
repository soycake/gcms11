<?php
/*
 * @filesource index/models/login.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Login;

use \Gcms\Login;
use \Kotchasan\Language;
use \Kotchasan\Template;

/**
 * Controller หลัก สำหรับแสดง frontend ของ GCMS
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\KBase
{

  /**
   * ฟังก์ชั่นตรวจสอบการ Login
   */
  public function chklogin()
  {
    if (self::$request->isReferer() && self::$request->initSession()) {
      // กำหนด skin ให้กับ template
      Template::init(self::$cfg->skin);
      // ตรวจสอบการ login
      Login::create();
      // ตรวจสอบสมาชิก
      $login = Login::isMember();
      // คืนค่า Json
      if ($login) {
        $name = trim($login['fname'].' '.$login['lname']);
        $ret = array(
          'alert' => str_replace('%s', (empty($name) ? $login['email'] : $name), Language::get('Welcome %s, login complete')),
          'content' => rawurlencode(\Index\Login\Controller::init($login)),
          'action' => self::$request->post('login_action', self::$cfg->login_action)->toString()
        );
      } else {
        $ret = array(
          'alert' => Login::$login_message,
          'input' => Login::$login_input
        );
      }
      echo json_encode($ret);
    }
  }
}