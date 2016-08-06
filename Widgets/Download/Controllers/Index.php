<?php
/*
 * @filesource Widgets/Download/Controllers/Index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Widgets\Download\Controllers;

use \Kotchasan\Text;
use \Gcms\Gcms;
use \Kotchasan\Grid;
use \Kotchasan\Date;

/**
 * Controller หลัก สำหรับแสดงผล Widget
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Index extends \Kotchasan\Controller
{

  /**
   * แสดงผล Widget
   *
   * @param array $query_string ข้อมูลที่ส่งมาจากการเรียก Widget
   * @return string
   */
  public function get($query_string)
  {
    if (preg_match('/^[a-z0-9]{3,}$/', $query_string['module']) && isset(Gcms::$install_modules[$query_string['module']])) {
      // module
      $index = Gcms::$install_modules[$query_string['module']];
      // ค่าที่ส่งมา
      $cat = isset($query_string['cat']) ? $query_string['cat'] : 0;
      $index->news_count = 10;
      $count = isset($query_string['count']) ? (int)$query_string['count'] : $index->news_count;
      $id = Text::rndname(10);
      // รายการ
      $listitem = Grid::create('download', $index->module, 'widgetitem');
      $widget = array('<div id="'.$id.'" class="document-list download"><div class="row listview">');
      // query ข้อมูล
      $bg = 'bg2';
      foreach (\Widgets\Download\Models\Index::get($index->module_id, $cat, $count) as $item) {
        $bg = $bg == 'bg1' ? 'bg2' : 'bg1';
        if (!empty($item->picture) && is_file(ROOT_PATH.DATA_FOLDER.'document/'.$item->picture)) {
          $thumb = WEB_URL.DATA_FOLDER.'document/'.$item->picture;
        } elseif (!empty($index->icon) && is_file(ROOT_PATH.DATA_FOLDER.'document/'.$index->icon)) {
          $thumb = WEB_URL.DATA_FOLDER.'document/'.$index->icon;
        } else {
          $thumb = WEB_URL.(isset($index->default_icon) ? $index->default_icon : 'modules/document/img/document-icon.png');
        }
        $listitem->add(array(
          '/{BG}/' => $bg,
          '/{ID}/' => $item->id,
          '/{NAME}/' => $item->name,
          '/{EXT}/' => $item->ext,
          '/{ICON}/' => WEB_URL.'/skin/ext/'.(is_file(ROOT_PATH.'skin/ext/'.$item->ext.'.png') ? $item->ext : 'file').'.png',
          '/{DETAIL}/' => $item->detail,
          '/{DATE}/' => Date::format($item->last_update),
          '/{DATEISO}/' => date(DATE_ISO8601, $item->last_update),
          '/{DOWNLOADS}/' => number_format($item->downloads),
          '/{SIZE}/' => Text::formatFileSize($item->size)
        ));
      }
      $widget[] = createClass('Kotchasan\View')->renderHTML($listitem->render());
      $widget[] = '</div></div>';
      $widget[] = '<script>initDownloadList("'.$id.'");</script>';
      return implode('', $widget);
    }
  }
}