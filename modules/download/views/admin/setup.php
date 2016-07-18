<?php
/*
 * @filesource download/views/admin/setup.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Download\Admin\Setup;

use \Kotchasan\DataTable;
use \Kotchasan\Language;
use \Kotchasan\Date;
use \Gcms\Gcms;
use \Kotchasan\Text;

/**
 * module=download-setup
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{
  /**
   * ข้อมูลโมดูล
   *
   * @var object
   */
  private $categories;

  /**
   * ตารางรายการบทความ
   *
   * @param object $index
   * @return string
   */
  public function render($index)
  {
    $this->categories = array(0 => '{LNG_all items}');
    foreach (\Index\Category\Model::categories((int)$index->module_id) as $item) {
      $this->categories[$item['category_id']] = Gcms::ser2Str($item, 'topic');
    }
    // Uri
    $uri = self::$request->getUri();
    // ตาราง
    $table = new DataTable(array(
      /* Model */
      'model' => 'Download\Admin\Setup\Model',
      /* รายการต่อหน้า */
      'perPage' => self::$request->cookie('download_perPage', 30)->toInt(),
      /* ฟิลด์ที่กำหนด (หากแตกต่างจาก Model) */
      'fields' => array(
        'id',
        'name',
        'ext',
        'detail',
        'category_id',
        'size',
        'last_update',
        'downloads',
        'file'
      ),
      /* query where */
      'defaultFilters' => array(
        array('A.module_id', (int)$index->module_id)
      ),
      /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
      'onRow' => array($this, 'onRow'),
      /* คอลัมน์ที่ไม่ต้องแสดงผล */
      'hideColumns' => array('ext', 'id', 'file'),
      /* ตั้งค่าการกระทำของของตัวเลือกต่างๆ ด้านล่างตาราง ซึ่งจะใช้ร่วมกับการขีดถูกเลือกแถว */
      'action' => 'index.php/download/model/admin/setup/action?mid='.$index->module_id,
      'actionCallback' => 'indexActionCallback',
      'actionConfirm' => 'confirmAction',
      'actions' => array(
        array(
          'id' => 'action',
          'class' => 'ok',
          'text' => Language::get('With selected'),
          'options' => array(
            'delete' => Language::get('Delete')
          )
        )
      ),
      /* คอลัมน์ที่สามารถค้นหาได้ */
      'searchColumns' => array('name', 'ext', 'detail', 'file'),
      /* ตัวเลือกการแสดงผลที่ส่วนหัว */
      'filters' => array(
        'category_id' => array(
          'name' => 'cat',
          'text' => '{LNG_Category}',
          'options' => $this->categories,
          'default' => 0,
          'value' => self::$request->get('cat')->toInt()
        )
      ),
      /* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
      'headers' => array(
        'name' => array(
          'text' => '{LNG_File Name}'
        ),
        'detail' => array(
          'text' => '{LNG_Description}',
        ),
        'category_id' => array(
          'text' => '{LNG_Category}',
          'class' => 'center'
        ),
        'size' => array(
          'text' => '{LNG_File size}',
          'class' => 'center'
        ),
        'last_update' => array(
          'text' => '{LNG_Last updated}',
          'class' => 'center'
        ),
        'downloads' => array(
          'text' => '{LNG_Download}',
          'class' => 'center'
        )
      ),
      /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
      'cols' => array(
        'category_id' => array(
          'class' => 'center'
        ),
        'size' => array(
          'class' => 'center'
        ),
        'last_update' => array(
          'class' => 'center date'
        ),
        'downloads' => array(
          'class' => 'center visited'
        )
      ),
      /* ปุ่มแสดงในแต่ละแถว */
      'buttons' => array(
        'edit' => array(
          'class' => 'icon-edit button green',
          'href' => $uri->createBackUri(array('module' => 'download-write', 'id' => ':id')),
          'text' => '{LNG_Edit}'
        )
      ),
      /* ปุ่มเพิ่ม */
      'addNew' => array(
        'class' => 'button green icon-plus',
        'href' => $uri->createBackUri(array('module' => 'download-write', 'mid' => $index->module_id)),
        'text' => '{LNG_Add New} {LNG_Download file}'
      )
    ));
    // save cookie
    setcookie('download_perPage', $table->perPage, time() + 3600 * 24 * 365, '/');
    return $table->render();
  }

  /**
   * จัดรูปแบบการแสดงผลในแต่ละแถว
   *
   * @param array $item
   * @return array
   */
  public function onRow($item)
  {
    $item['name'] = "<a href='".WEB_URL."$item[file]' target=_blank>$item[name].$item[ext]</a>";
    $item['size'] = Text::formatFileSize($item['size']);
    $item['category_id'] = isset($this->categories[$item['category_id']]) ? $this->categories[$item['category_id']] : '{LNG_Uncategorized}';
    $item['last_update'] = Date::format($item['last_update'], 'd M Y H:i');
    return $item;
  }
}