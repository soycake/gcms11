<?php
/*
 * @filesource personnel/views/admin/setup.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Personnel\Admin\Setup;

use \Kotchasan\DataTable;
use \Kotchasan\Language;
use \Gcms\Gcms;

/**
 * module=personnel-setup
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
  private $uri;

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
    $this->uri = self::$request->getUri();
    // ตาราง
    $table = new DataTable(array(
      /* Model */
      'model' => 'Personnel\Admin\Setup\Model',
      /* รายการต่อหน้า */
      'perPage' => self::$request->cookie('personnel_perPage', 30)->toInt(),
      /* ฟิลด์ที่กำหนด (หากแตกต่างจาก Model) */
      'fields' => array(
        'id',
        'name',
        'picture',
        'category_id',
        'order',
        'email',
        'position',
        'phone',
        'module_id'
      ),
      /* query where */
      'defaultFilters' => array(
        array('module_id', (int)$index->module_id)
      ),
      /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
      'onRow' => array($this, 'onRow'),
      /* คอลัมน์ที่ไม่ต้องแสดงผล */
      'hideColumns' => array('id', 'module_id'),
      /* ตั้งค่าการกระทำของของตัวเลือกต่างๆ ด้านล่างตาราง ซึ่งจะใช้ร่วมกับการขีดถูกเลือกแถว */
      'action' => 'index.php/personnel/model/admin/setup/action?mid='.$index->module_id,
      'actionCallback' => 'indexActionCallback',
      'actionConfirm' => 'confirmAction',
      'actions' => array(
        array(
          'id' => 'action',
          'class' => 'ok',
          'text' => '{LNG_With selected}',
          'options' => array(
            'delete' => '{LNG_Delete}'
          )
        )
      ),
      /* คอลัมน์ที่สามารถค้นหาได้ */
      'searchColumns' => array('name', 'email', 'phone', 'position'),
      /* ตัวเลือกการแสดงผลที่ส่วนหัว */
      'filters' => array(
        'category_id' => array(
          'name' => 'cat',
          'text' => '{LNG_Personnel groups}',
          'options' => $this->categories,
          'default' => 0,
          'value' => self::$request->get('cat')->toInt()
        )
      ),
      /* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
      'headers' => array(
        'name' => array(
          'text' => '{LNG_Name} {LNG_Surname}'
        ),
        'picture' => array(
          'text' => '{LNG_Image}',
        ),
        'category_id' => array(
          'text' => '{LNG_Category}',
          'class' => 'center'
        ),
        'order' => array(
          'text' => '{LNG_Sort}',
          'class' => 'center'
        ),
        'email' => array(
          'text' => '{LNG_Email}'
        ),
        'position' => array(
          'text' => '{LNG_Position}'
        ),
        'phone' => array(
          'text' => '{LNG_Phone}',
          'class' => 'center'
        )
      ),
      /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
      'cols' => array(
        'category_id' => array(
          'class' => 'center'
        ),
        'phone' => array(
          'class' => 'center'
        ),
        'order' => array(
          'class' => 'center'
        )
      ),
      /* ปุ่มแสดงในแต่ละแถว */
      'buttons' => array(
        'edit' => array(
          'class' => 'icon-edit button green',
          'href' => $this->uri->createBackUri(array('module' => 'personnel-write', 'id' => ':id')),
          'text' => '{LNG_Edit}'
        )
      ),
      /* ปุ่มเพิ่ม */
      'addNew' => array(
        'class' => 'button green icon-plus',
        'href' => $this->uri->createBackUri(array('module' => 'personnel-write', 'mid' => $index->module_id)),
        'text' => '{LNG_Add New} {LNG_Personnel}'
      )
    ));
    // save cookie
    setcookie('personnel_perPage', $table->perPage, time() + 3600 * 24 * 365, '/');
    $table->script('initPersonnel();');
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
    $item['phone'] = "<a href='tel:$item[phone]'>$item[phone]</a>";
    $item['email'] = "<a href='".$this->uri->createBackUri(array('module' => 'sendmail', 'to' => $item['email']))."'>$item[email]</a>";
    $thumb = is_file(ROOT_PATH.DATA_FOLDER.'personnel/'.$item['picture']) ? WEB_URL.DATA_FOLDER.'personnel/'.$item['picture'] : '../modules/video/img/nopicture.jpg';
    $item['picture'] = '<img src="'.$thumb.'" style="max-height:50px" alt=thumbnail>';
    $item['category_id'] = isset($this->categories[$item['category_id']]) ? $this->categories[$item['category_id']] : '{LNG_Uncategorized}';
    $item['order'] = '<label><input type=text size=5 id=order_'.$item['module_id'].'_'.$item['id'].' value="'.$item['order'].'"></label>';
    return $item;
  }
}