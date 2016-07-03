<?php
/*
 * @filesource board/views/view.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Board\View;

use \Kotchasan\Language;
use \Kotchasan\Template;
use \Kotchasan\Http\Request;
use \Gcms\Gcms;
use \Kotchasan\Login;
use \Kotchasan\Antispam;
use \Board\Index\Controller;
use \Kotchasan\Date;
use \Kotchasan\Grid;

/**
 * แสดงกระทู้
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{

  /**
   * แสดงกระทู้
   *
   * @param Request $request
   * @param object $index ข้อมูลโมดูล
   * @return object
   */
  public function index(Request $request, $index)
  {
    // ค่าที่ส่งมา
    $search = preg_replace('/[+\s]+/u', ' ', $request->get('q')->text());
    // อ่านรายการที่เลือก
    $story = \Board\View\Model::get((int)$index->module_id, $index->id);
    if (empty($story)) {
      // 404
      return createClass('Index\PageNotFound\Controller')->init($request, 'board');
    } else {
      // login
      $login = $request->session('login', array('id' => 0, 'status' => -1, 'email' => '', 'password' => ''))->all();
      // สมาชิก true
      $isMember = $login['status'] > -1;
      // แสดงความคิดเห็นได้
      $canReply = !empty($story->can_reply);
      // ผู้ดูแล
      $moderator = Gcms::canConfig($login, $index, 'moderator');
      // สถานะสมาชิกที่สามารถเปิดดูกระทู้ได้
      $canView = Gcms::canConfig($login, $index, 'can_view');
      // dir ของรูปภาพอัปโหลด
      $imagedir = ROOT_PATH.DATA_FOLDER.'board/';
      $imageurl = WEB_URL.DATA_FOLDER.'board/';
      // รูปภาพ
      if (!empty($story->picture) && is_file($imagedir.$story->picture)) {
        $story->image_src = $imageurl.$story->picture;
      }
      if ($canView || $index->viewing == 1) {
        if ($canReply) {
          // antispam
          $antispam = new Antispam();
          // รายการแสดงความคิดเห็น
          $listitem = Grid::create($index->owner, $index->module, 'commentitem');
          foreach (\Board\Comment\Model::get($story) as $no => $item) {
            // moderator และ เจ้าของ สามารถแก้ไขความคิดเห็นได้
            $canEdit = $moderator || ($isMember && $login['id'] == $item->member_id);
            // รูปภาพของความคิดเห็น
            $picture = $item->picture != '' && is_file($imagedir.$item->picture) ? '<div><figure><img src="'.$imageurl.$item->picture.'" alt="'.$index->topic.'"></figure></div>' : '';
            $listitem->add(array(
              '/(edit-{QID}-{RID}-{NO}-{MODULE})/' => $canEdit ? '\\1' : 'hidden',
              '/(delete-{QID}-{RID}-{NO}-{MODULE})/' => $moderator ? '\\1' : 'hidden',
              '/{DETAIL}/' => $picture.Gcms::highlightSearch(Gcms::showDetail(str_replace(array('{', '}'), array('&#x007B;', '&#x007D;'), nl2br($item->detail)), $canView, true, true), $search),
              '/{UID}/' => $item->member_id,
              '/{DISPLAYNAME}/' => $item->name,
              '/{STATUS}/' => $item->status,
              '/{DATE}/' => Date::format($item->last_update),
              '/{DATEISO}/' => date(DATE_ISO8601, $item->last_update),
              '/{IP}/' => Gcms::showip($item->ip),
              '/{NO}/' => $no + 1,
              '/{RID}/' => $item->id
            ));
          }
          Gcms::$view->setContents(array(
            '/:size/' => $index->img_upload_size,
            '/:type/' => implode(', ', $index->img_upload_type)
            ), false);
        }
        // แก้ไขกระทู้ (mod หรือ ตัวเอง)
        $canEdit = $moderator || ($isMember && $login['id'] == $story->member_id);
        // รูปภาพในกระทู้
        $picture = empty($story->image_src) ? '' : '<div><figure><img src="'.$story->image_src.'" alt="'.$story->topic.'"></figure></div>';
        // เนื้อหา
        $detail = Gcms::showDetail(str_replace(array('{', '}'), array('&#x007B;', '&#x007D;'), nl2br($story->detail)), $canView, true, true);
        $replace = array(
          '/(edit-{QID}-0-0-{MODULE})/' => $canEdit ? '\\1' : 'hidden',
          '/(delete-{QID}-0-0-{MODULE})/' => $moderator ? '\\1' : 'hidden',
          '/(quote-{QID}-([0-9]+)-([0-9]+)-{MODULE})/' => !$canReply || $story->locked == 1 ? 'hidden' : '\\1',
          '/(pin-{QID}-0-0-{MODULE})/' => $moderator ? '\\1' : 'hidden',
          '/(lock-{QID}-0-0-{MODULE})/' => $moderator ? '\\1' : 'hidden',
          '/{COMMENTLIST}/' => isset($listitem) ? $listitem->render() : '',
          '/{REPLYFORM}/' => $canReply && $story->locked == 0 ? Template::load($index->owner, $index->module, 'reply') : '',
          '/<MEMBER>(.*)<\/MEMBER>/s' => $isMember ? '' : '$1',
          '/<UPLOAD>(.*)<\/UPLOAD>/s' => empty($index->img_upload_type) ? '' : '$1',
          '/{TOPIC}/' => $story->topic,
          '/{DETAIL}/' => $picture.Gcms::HighlightSearch($detail, $search),
          '/{DATE}/' => Date::format($story->create_date),
          '/{DATEISO}/' => date(DATE_ISO8601, $story->create_date),
          '/{COMMENTS}/' => number_format($story->comments),
          '/{VISITED}/' => number_format($story->visited),
          '/{DISPLAYNAME}/' => $story->name,
          '/{STATUS}/' => $story->status,
          '/{UID}/' => (int)$story->member_id,
          '/{LOGIN_PASSWORD}/' => $login['password'],
          '/{LOGIN_EMAIL}/' => $login['email'],
          '/{QID}/' => $story->id,
          '/{MODULE}/' => $index->module,
          '/{MODULEID}/' => $story->module_id,
          '/{ANTISPAM}/' => isset($antispam) ? $antispam->getId() : '',
          '/{ANTISPAMVAL}/' => isset($antispam) && Login::isAdmin() ? $antispam->getValue() : '',
          '/{DELETE}/' => $moderator ? '{LNG_Delete}' : '{LNG_Removal request}',
          '/{PIN}/' => $story->pin == 0 ? 'un' : '',
          '/{LOCK}/' => $story->locked == 0 ? 'un' : '',
          '/{PIN_TITLE}/' => $story->pin == 1 ? '{LNG_Unpin}' : '{LNG_Pin}',
          '/{LOCK_TITLE}/' => $story->locked == 1 ? '{LNG_Unlock}' : '{LNG_Lock}'
        );
        $story->detail = Template::create($index->owner, $index->module, 'view')->add($replace)->render();
      } else {
        // not login
        $replace = array(
          '/{TOPIC}/' => $story->topic,
          '/{DETAIL}/' => '<div class=error>'.Language::get('Members Only').'</div>'
        );
        $story->detail = Template::create($index->owner, $index->module, 'error')->add($replace)->render();
      }
      // breadcrumb ของโมดูล
      if (!Gcms::isHome($index->module)) {
        $menu = Gcms::$menu->moduleMenu($index->module);
        if ($menu) {
          Gcms::$view->addBreadcrumb(Gcms::createUrl($index->module), $menu->menu_text, $menu->menu_tooltip);
        }
      }
      // breadcrumb ของหมวดหมู่
      if (!empty($story->category)) {
        Gcms::$view->addBreadcrumb(Gcms::createUrl($index->module, '', $story->category_id), Gcms::ser2Str($story->category), Gcms::ser2Str($story->cat_tooltip));
      }
      // breadcrumb ของหน้า
      $story->canonical = Controller::url($index->module, $story->category_id, $story->id);
      Gcms::$view->addBreadcrumb($story->canonical, $story->topic);
      // คืนค่า
      $story->description = Gcms::html2txt($detail);
      $story->keywords = $story->topic;
      $story->module = $index->module;
      return $story;
    }
  }
}