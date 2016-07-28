<?php
/*
 * @filesource personnel/views/lists.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Personnel\Lists;

use \Kotchasan\Template;
use \Kotchasan\Http\Request;
use \Gcms\Gcms;
use \Kotchasan\Grid;
use \Personnel\Index\Controller;

/**
 * แสดงรายการสมาชิก
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{

  /**
   * แสดงรายการบุคคลากร
   *
   * @param Request $request
   * @param object $index ข้อมูลโมดูล
   * @return object
   */
  public function index(Request $request, $index)
  {
    // ลิสต์ข้อมูล
    $index = \Personnel\Lists\Model::getItems($request, $index);
    // หมวดหมู่บุคลากร
    $categories = \Index\Category\Model::all((int)$index->module_id);
    // รายการ
    $listitem = Grid::create($index->owner, $index->module, 'item');
    foreach ($index->items as $item) {
      // image
      $img = str_replace('image', 'thumb', $item->image);
      if (is_file(ROOT_PATH.DATA_FOLDER.'personnel/'.$img)) {
        $img = WEB_URL.DATA_FOLDER.'personnel/'.$img;
      } else {
        $img = WEB_URL.'modules/personnel/img/noimage.jpg';
      }
      $listitem->add(array(
        '/{ID}/' => $item->id,
        '/{SRC}/' => $img,
        '/{URL}/' => Controller::url($index->module, $item->id),
        '/{TOPIC}/' => $item->topic
      ));
    }
    // breadcrumb ของโมดูล
    if (Gcms::isHome($index->module)) {
      $index->canonical = WEB_URL.'index.php'.(empty($qs) ? '' : '?'.implode('&amp;', $qs));
    } else {
      $index->canonical = Gcms::createUrl($index->module, '', 0, 0, (empty($qs) ? '' : implode('&', $qs)));
      $menu = Gcms::$menu->moduleMenu($index->module);
      if ($menu) {
        Gcms::$view->addBreadcrumb($index->canonical, $menu->menu_text, $menu->menu_tooltip);
      }
    }
    // current URL
    $uri = \Kotchasan\Http\Uri::createFromUri($index->canonical);
    // template
    $template = Template::create($index->owner, $index->module, 'main');
    $template->add(array(
      '/{LIST}/' => $listitem->hasItem() ? $listitem->render() : '<div class="error center">{LNG_Sorry, no information available for this item.}</div>',
      '/{COLS}/' => $index->cols,
      '/{TOPIC}/' => $index->topic,
      '/{DETAIL}/' => Gcms::showDetail($index->detail, true, false),
      '/{SPLITPAGE}/' => $uri->pagination($index->totalpage, $index->page),
      '/{MODULE}/' => $index->module,
      '/{WIDTH}/' => $index->icon_width,
      '/{HEIGHT}/' => $index->icon_height,
    ));
    // คืนค่า
    $index->detail = $template->render();
    return $index;
  }
}