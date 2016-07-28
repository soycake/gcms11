<?php
/*
 * @filesource gallery/views/view.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Gallery\View;

use \Kotchasan\Template;
use \Kotchasan\Http\Request;
use \Gcms\Gcms;
use \Kotchasan\Login;
use \Gallery\Index\Controller;
use \Kotchasan\Date;
use \Kotchasan\Grid;

/**
 * แสดงรูปภาพในอัลบัม
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{

  /**
   * แสดงรูปภาพในอัลบัม
   *
   * @param Request $request
   * @param object $index ข้อมูลโมดูล
   * @return object
   */
  public function index(Request $request, $index)
  {
    // ลิสต์ข้อมูล
    $index = \Gallery\View\Model::get($request, $index);
    if (empty($index) || empty($index->items)) {
      // 404
      return createClass('Index\PageNotFound\Controller')->init($request, 'gallery');
    } else {
      // login
      $login = Login::isMember();
      // breadcrumb ของโมดูล
      if (Gcms::isHome($index->module)) {
        $index->canonical = WEB_URL.'index.php';
      } else {
        $index->canonical = Controller::url($index->module, $index->id);
        $menu = Gcms::$menu->moduleMenu($index->module);
        if ($menu) {
          Gcms::$view->addBreadcrumb($index->canonical, $menu->menu_text, $menu->menu_tooltip);
        }
      }
      // current URL
      $uri = \Kotchasan\Http\Uri::createFromUri($index->canonical);
      if (Gcms::canConfig($login, $index, 'can_view')) {
        // รายการ
        $listitem = Grid::create($index->owner, $index->module, 'listitem');
        foreach ($index->items as $item) {
          // image
          if (is_file(ROOT_PATH.DATA_FOLDER.'gallery/'.$item->image)) {
            $thumb = WEB_URL.DATA_FOLDER.'gallery/'.str_replace('image', 'thumb', $item->image);
            $img = WEB_URL.DATA_FOLDER.'gallery/'.$item->image;
          } else {
            $thumb = WEB_URL.'modules/gallery/img/noimage.jpg';
            $img = WEB_URL.'modules/gallery/img/noimage.jpg';
          }
          $listitem->add(array(
            '/{ID}/' => $item->id,
            '/{SRC}/' => $thumb,
            '/{URL}/' => $img
          ));
        }
        // template
        $template = Template::create($index->owner, $index->module, 'list');
        $template->add(array(
          '/{LIST}/' => $listitem->render(),
          '/{TOPIC}/' => $index->topic,
          '/{DETAIL}/' => nl2br($index->detail),
          '/{SPLITPAGE}/' => $uri->pagination($index->totalpage, $index->page),
          '/{MODULE}/' => $index->module,
          '/{COLS}/' => $index->cols,
          '/{WIDTH}/' => $index->icon_width,
          '/{HEIGHT}/' => $index->icon_height,
          '/{VISITED}/' => $index->visited,
          '/{LASTUPDATE}/' => Date::format($index->last_update, 'd M Y')
        ));
        // คืนค่า
        $index->detail = $template->render();
      } else {
        // not login
        $replace = array(
          '/{TOPIC}/' => $index->topic,
          '/{DETAIL}/' => '<div class=error>{LNG_Members Only}</div>'
        );
        $index->detail = Template::create($index->owner, $index->module, 'error')->add($replace)->render();
      }
      return $index;
    }
  }
}