<?php
/*
 * @filesource index/models/menuwrite.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Menuwrite;

use \Kotchasan\Login;
use \Kotchasan\Language;
use \Gcms\Gcms;

/**
 * อ่าน/บันทึก ข้อมูลหน้าเพจ
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * อ่านเมนูตาม id
   * 0 หมายถึง สร้างเมนูใหม่
   *
   * @return array|boolean คืนค่าแอเรย์ของข้อมูล ไม่พบคืนค่า false
   */
  public static function getMenu($id)
  {
    if (is_int($id)) {
      if (empty($id)) {
        // ใหม่
        $index = (object)array(
            'id' => 0,
            'index_id' => 0,
            'parent' => self::$request->get('_parent', 'MAINMENU')->toString(),
            'level' => 0,
            'language' => Language::name(),
            'menu_text' => '',
            'menu_tooltip' => '',
            'accesskey' => '',
            'menu_order' => 0,
            'menu_url' => '',
            'menu_target' => '',
            'alias' => '',
            'owner' => '',
            'published' => 1
        );
      } else {
        // อ่านข้อมูลจาก db
        $model = new static;
        $query = $model->db()->createQuery()->select('I.module_id')->from('index I')->where(array('I.id', 'U.index_id'));
        $query = $model->db()->createQuery()->select('M.owner')->from('modules M')->where(array('M.id', $query));
        $index = $model->db()->createQuery()->from('menus U')->where($id)->first('U.*', array($query, 'owner'));
      }
      return $index;
    }
    return false;
  }

  /**
   * รายการ หน้าเว็บทั้งหมด และ โมดูลที่ติดตั้ง
   *
   * @return array
   */
  public static function getModules()
  {
    $result = array();
    if (defined('MAIN_INIT')) {
      $model = new static;
      $select = array(
        'I.id',
        'M.owner',
        'M.module',
        'D.topic',
        'I.language'
      );
      $query = $model->db()->createQuery()
        ->select($select)
        ->from('index I')
        ->join('index_detail D', 'INNER', array(array('D.id', 'I.id'), array('D.module_id', 'I.module_id'), array('D.language', 'I.language')))
        ->join('modules M', 'INNER', array('M.id', 'I.module_id'))
        ->where(array('I.index', 1))
        ->order(array('M.owner', 'I.module_id', 'I.language'));
      foreach ($query->toArray()->execute() AS $item) {
        $result[$item['owner']][$item['owner'].'_'.$item['id']] = $item['module'].(empty($item['language']) ? '' : " [$item[language]]").', '.$item['topic'];
      }
      foreach (Gcms::$module_menus as $key => $values) {
        foreach ($values as $menu => $details) {
          $result[$key][$key.'_'.$menu] = $details[0];
        }
      }
    }
    return $result;
  }

  /**
   * action
   */
  public static function action()
  {
    $ret = array();
    // referer, session, member
    if (self::$request->initSession() && self::$request->isReferer() && $login = Login::isAdmin()) {
      if ($login['email'] == 'demo') {
        $ret['alert'] = Language::get('Unable to complete the transaction');
      } else {
        $action = self::$request->post('action')->toString();
        $parent = self::$request->post('parent')->toString();
        $id = self::$request->post('id')->toInt();
        $model = new static;
        if ($action === 'get' && !empty($parent)) {
          // query menu
          $query = $model->db()->createQuery()
            ->select('id', 'level', 'menu_text', 'menu_tooltip')
            ->from('menus')
            ->where(array('parent', $parent))
            ->order('menu_order');
          foreach ($query->execute() as $item) {
            $text = '';
            for ($i = 0; $i < $item->level; $i++) {
              $text .= '&nbsp;&nbsp;';
            }
            $ret['O_'.$item->id] = (empty($text) ? '' : $text.'↳&nbsp;').(empty($item->menu_text) ? (empty($item->menu_tooltip) ? '---' : $item->menu_tooltip) : $item->menu_text).(empty($item->language) ? '' : ' ['.$item->language.']');
          }
        } elseif ($action === 'copy' && !empty($id)) {
          // สำเนาเมนู
          $table_menus = $model->getFullTableName('menus');
          $menu = $model->db()->first($table_menus, $id);
          if ($menu->language == '') {
            $ret['alert'] = Language::get('This entry is displayed in all languages');
          } else {
            $lng = strtolower(self::$request->post('lng')->toString());
            // ตรวจสอบเมนูซ้ำ
            $search = $model->db()->first($table_menus, array(
              array('index_id', $menu->index_id),
              array('parent', $menu->parent),
              array('level', $menu->level),
              array('language', $lng)
            ));
            if ($search === false) {
              // ข้อมูลเดิม
              $old_lng = $menu->language;
              // แก้ไขรายการเดิมเป็นภาษาใหม่
              $menu->language = $lng;
              $model->db()->update($table_menus, $menu->id, $menu);
              unset($menu->id);
              // เพิ่มรายการใหม่จากรายการเดิม
              $menu->language = $old_lng;
              $model->db()->insert($table_menus, $menu);
              $ret['alert'] = Language::get('Copy successfully, you can edit this entry');
            } else {
              $ret['alert'] = Language::get('This entry is in selected language');
            }
          }
        }
      }
    } else {
      $ret['alert'] = Language::get('Unable to complete the transaction');
    }
    // คืนค่าเป็น JSON
    echo json_encode($ret);
  }

  /**
   * บันทึก
   */
  public function save()
  {
    $ret = array();
    // referer, session, member
    if (self::$request->initSession() && self::$request->isReferer() && $login = Login::isAdmin()) {
      if ($login['email'] == 'demo') {
        $ret['alert'] = Language::get('Unable to complete the transaction');
      } else {
        $input = false;
        // รับค่าจากการ POST
        $save = array(
          'language' => strtolower(self::$request->post('language')->topic()),
          'menu_text' => self::$request->post('menu_text')->topic(),
          'menu_tooltip' => self::$request->post('menu_tooltip')->topic(),
          'accesskey' => strtolower(self::$request->post('accesskey')->topic()),
          'alias' => self::$request->post('alias')->topic(),
          'parent' => strtoupper(self::$request->post('parent')->topic()),
          'published' => self::$request->post('published')->toInt(),
          'menu_url' => self::$request->post('menu_url')->url(),
          'menu_target' => self::$request->post('menu_target')->topic()
        );
        $id = self::$request->post('id')->toInt();
        $type = self::$request->post('type')->toInt();
        $toplvl = self::$request->post('menu_order')->toInt();
        $action = self::$request->post('action')->toInt();
        if ($action == 1 && preg_match('/^([a-z]+)_(([a-z]+)(_([a-z0-9]+))?|([0-9]+))$/', self::$request->post('index_id')->toString(), $match)) {
          if (empty($match[6])) {
            if (is_file(ROOT_PATH.'modules/'.$match[1].'/models/admin/init.php')) {
              include ROOT_PATH.'modules/'.$match[1].'/models/admin/init.php';
              $class = ucfirst($match[1]).'\Admin\Init\Model';
              if (method_exists($class, 'init')) {
                // module Initial
                $class::init($items);
              }
            }
            if (isset(Gcms::$module_menus[$match[1]])) {
              $action = 2;
              $save['menu_url'] = Gcms::$module_menus[$match[1]][$match[2]][1];
              $save['alias'] = $save['alias'] == '' ? Gcms::$module_menus[$match[1]][$match[2]][2] : $save['alias'];
            }
          } else {
            $save['index_id'] = $match[6];
          }
        }
        $model = new static;
        $table_menu = $model->getFullTableName('menus');
        if (!empty($id)) {
          $menu = $model->db()->first($table_menu, array('id', $id));
        } else {
          $menu = (object)array('id' => 0);
        }
        // ตรวจสอบค่าที่ส่งมา
        $input = false;
        if ($id > 0 && !$menu) {
          $ret['alert'] = Language::get('Unable to complete the transaction');
        } else {
          // accesskey
          if ($save['accesskey'] != '') {
            if (!preg_match('/[a-z0-9]{1,1}/', $save['accesskey'])) {
              $input = !$input ? 'accesskey' : $input;
            }
          }
          // menu order (top level)
          if ($type != 0 && $toplvl == 0) {
            $input = !$input ? 'menu_order' : $input;
          } elseif ($action == 1 && $save['index_id'] == 0) {
            $input = !$input ? 'menu_order' : $input;
          }
          // menu_url
          if ($action == 2 && $save['menu_url'] == '') {
            $input = !$input ? 'menu_url' : $input;
          }
          if ($action != 2) {
            $save['menu_url'] = '';
          }
          if ($action != 1) {
            $save['index_id'] = 0;
          }
        }
        if (!$input) {
          if ($type == 0) {
            // เป็นเมนูลำดับแรกสุด
            $save['menu_order'] = 1;
            $save['level'] = 0;
            $menu_order = 1;
            $toplvl = 0;
          } else {
            $save['level'] = $type - 1;
            $menu_order = 0;
          }
          $top_level = 0;
          // query menu ทั้งหมด, เรียงลำดับเมนูตามที่กำหนด
          $query = $model->db()->createQuery()
            ->select('id', 'level', 'menu_order')
            ->from('menus')
            ->where(array('parent', $save['parent']))
            ->order('menu_order');
          foreach ($query->toArray()->execute() AS $item) {
            if ($item['id'] != $menu->id) {
              $changed = false;
              $menu_order++;
              $top_level = $menu_order == 1 ? 0 : min($top_level + 1, $item['level']);
              if ($menu_order != $item['menu_order']) {
                // อัปเดท menu_order
                $item['menu_order'] = $menu_order;
                $changed = true;
              }
              if ($top_level != $item['level']) {
                // อัปเดท level
                $item['level'] = $top_level;
                $changed = true;
              }
              if ($changed) {
                $model->db()->update($table_menu, $item['id'], $item);
              }
              if ($toplvl == $item['id']) {
                $menu_order++;
                $save['menu_order'] = $menu_order;
                $save['level'] = min($item['level'] + 1, $save['level']);
              }
            }
          }
          // บันทึก
          if (empty($id)) {
            // ใหม่
            $id = $model->db()->insert($table_menu, $save);
          } else {
            // แก้ไข
            $model->db()->update($table_menu, $id, $save);
          }
          // ส่งค่ากลับ
          $ret['alert'] = Language::get('Saved successfully');
          $ret['location'] = self::$request->getUri()->postBack('index.php', array('module' => 'menus', 'id' => null, 'parent' => $save['parent']));
        } else {
          // คืนค่า input ตัวแรกที่ error
          $ret['input'] = $input;
        }
      }
    } else {
      $ret['alert'] = Language::get('Unable to complete the transaction');
    }
    // คืนค่าเป็น JSON
    echo json_encode($ret);
  }
}