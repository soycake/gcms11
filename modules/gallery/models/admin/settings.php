<?php
/*
 * @filesource gallery/models/admin/settings.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Gallery\Admin\Settings;

use \Kotchasan\Login;
use \Kotchasan\Language;
use \Gcms\Gcms;

/**
 *  การตั้งค่า
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * ค่าติดตั้งเรื่มต้น
   *
   * @return array
   */
  public static function defaultSettings()
  {
    return array(
      'icon_width' => 400,
      'icon_height' => 300,
      'image_width' => 800,
      'img_typies' => array('jpg', 'jpeg'),
      'rows' => 3,
      'cols' => 4,
      'sort' => 1,
      'can_write' => array(1),
      'can_config' => array(1)
    );
  }

  /**
   * บันทึกข้อมูล config ของโมดูล
   */
  public function save()
  {
    $ret = array();
    // referer, session, member
    if (self::$request->initSession() && self::$request->isReferer() && $login = Login::isMember()) {
      if ($login['email'] == 'demo') {
        $ret['alert'] = Language::get('Unable to complete the transaction');
      } else {
        // รับค่าจากการ POST
        $save = array(
          'icon_width' => max(75, self::$request->post('icon_width')->toInt()),
          'icon_height' => max(75, self::$request->post('icon_height')->toInt()),
          'image_width' => max(400, self::$request->post('image_width')->toInt()),
          'img_typies' => self::$request->post('img_typies', array())->toString(),
          'rows' => self::$request->post('rows')->toInt(),
          'cols' => self::$request->post('cols')->toInt(),
          'sort' => self::$request->post('sort')->toInt(),
          'can_view' => self::$request->post('can_view', array())->toInt(),
          'can_write' => self::$request->post('can_write', array())->toInt(),
          'can_config' => self::$request->post('can_config', array())->toInt(),
        );
        // โมดูลที่เรียก
        $index = \Gallery\Admin\Index\Model::module(self::$request->post('id')->toInt());
        if ($index && Gcms::canConfig($login, $index, 'can_config')) {
          if (empty($save['img_typies'])) {
            // คืนค่า input ที่ error
            $ret['input'] = 'img_typies_jpg';
          } else {
            $save['can_view'][] = 1;
            $save['can_write'][] = 1;
            $save['can_config'][] = 1;
            $this->db()->createQuery()->update('modules')->set(array('config' => serialize($save)))->where($index->module_id)->execute();
            // คืนค่า
            $ret['alert'] = Language::get('Saved successfully');
            $ret['location'] = self::$request->getUri()->postBack('index.php', array('module' => 'gallery-settings', 'mid' => $index->module_id));
          }
        } else {
          $ret['alert'] = Language::get('Unable to complete the transaction');
        }
      }
    } else {
      $ret['alert'] = Language::get('Unable to complete the transaction');
    }
    // คืนค่าเป็น JSON
    echo json_encode($ret);
  }
}