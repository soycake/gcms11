<?php
/*
 * @filesource download/views/index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Download\Index;

use \Kotchasan\Template;
use \Kotchasan\Http\Request;
use \Gcms\Gcms;
use \Kotchasan\Date;
use \Kotchasan\Grid;
use \Kotchasan\Text;

/**
 * แสดงรายการบทความ
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{

  /**
   * แสดงรายการดาวน์โหลด
   *
   * @param Request $request
   * @param object $index ข้อมูลโมดูล
   * @return object
   */
  public function index(Request $request, $index)
  {
    // query ข้อมูล
    $index = \Download\Index\Model::getItems($request, $index);
    // รายการ
    $listitem = Grid::create($index->owner, $index->module, 'listitem');
    foreach ($index->items as $item) {
      $listitem->add(array(
        '/{ID}/' => $item->id,
        '/{NAME}/' => $item->name,
        '/{EXT}/' => $item->ext,
        '/{ICON}/' => WEB_URL.'skin/ext/'.(is_file(ROOT_PATH.'skin/ext/'.$item->ext.'.png') ? $item->ext : 'file').'.png',
        '/{DETAIL}/' => $item->detail,
        '/{DATE}/' => Date::format($item->last_update),
        '/{DATEISO}/' => date(DATE_ISO8601, $item->last_update),
        '/{DOWNLOADS}/' => number_format($item->downloads),
        '/{SIZE}/' => Text::formatFileSize($item->size)
      ));
    }
    // breadcrumb ของโมดูล
    if (Gcms::isHome($index->module)) {
      $index->canonical = WEB_URL.'index.php';
    } else {
      $index->canonical = Gcms::createUrl($index->module);
      $menu = Gcms::$menu->moduleMenu($index->module);
      if ($menu) {
        Gcms::$view->addBreadcrumb($index->canonical, $menu->menu_text, $menu->menu_tooltip);
      } elseif ($index->topic != '') {
        Gcms::$view->addBreadcrumb($index->canonical, $index->topic);
      }
    }
    // current URL
    $uri = \Kotchasan\Http\Uri::createFromUri($index->canonical);
    // template
    $template = Template::create($index->owner, $index->module, $listitem->hasItem() ? 'list' : 'empty');
    $template->add(array(
      '/{TOPIC}/' => $index->topic,
      '/{DETAIL}/' => $index->detail,
      '/{LIST}/' => $listitem->render(),
      '/{SPLITPAGE}/' => $uri->pagination($index->totalpage, $index->page),
      '/{MODULE}/' => $index->module
    ));
    // คืนค่า
    $index->detail = $template->render();
    return $index;
  }
}