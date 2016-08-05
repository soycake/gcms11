<?php
/*
 * @filesource download/models/download.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Download\Download;

use \Kotchasan\Http\Request;
use \Kotchasan\Login;
use \Kotchasan\Language;
use \Kotchasan\Text;

/**
 * อ่านข้อมูลโมดูล
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * ดาวน์โหลดไฟล์
   *
   * @param Request $request
   * @return Object
   */
  public function action(Request $request)
  {
    if ($request->initSession() && $request->isReferer()) {
      // login
      $login = Login::isMember();
      // ค่าที่ส่งมา
      $action = $request->post('action')->toString();
      $id = $request->post('id')->toString();
      if (($action == 'download' || $action == 'downloading') && preg_match('/[a-z]+_([0-9]+)/', $request->post('id')->toString(), $match)) {
        // ไฟล์ดเวน์โหลด
        $download = $this->get($match[1]);
        // สถานะสมาชิก guest = -1
        $status = $login ? $login['status'] : -1;
        // ตรวจสอบข้อมูล
        $ret = array();
        if (!$download || !is_file(ROOT_PATH.$download->file)) {
          $ret['alert'] = Language::get('Sorry, Item not found It&#39;s may be deleted');
        } elseif (!in_array($status, $download->can_download)) {
          $ret['alert'] = Language::get('Can not be performed this request. Because they do not find the information you need or you are not allowed');
        } elseif ($action === 'download') {
          $ret['confirm'] = Language::get('Do you want to download the file ?');
        } elseif ($action === 'downloading') {
          // อัปเดทดาวน์โหลด
          $download->downloads++;
          $this->db()->update($this->getTableName('download'), (int)$download->id, array('downloads' => $download->downloads));
          // URL สำหรับดาวน์โหลด
          $fid = Text::rndname(32);
          $_SESSION[$fid]['file'] = ROOT_PATH.$download->file;
          $_SESSION[$fid]['size'] = $download->size;
          $_SESSION[$fid]['name'] = $download->name.'.'.$download->ext;
          // คืนค่า URL สำหรับดาวน์โหลด
          $ret['href'] = WEB_URL.'modules/download/filedownload.php?id='.$fid;
          $ret['downloads'] = number_format($download->downloads);
          $ret['id'] = $download->id;
        }
        $ret['action'] = $action;
        // คืนค่าเป็น JSON
        echo json_encode($ret);
      }
    }
  }

  /**
   * ตรวจสอบไฟล์ที่เลือก
   *
   * @param int $id
   * @return object
   */
  public function get($id)
  {
    $search = $this->db()->createQuery()
      ->from('download D')
      ->join('modules M', 'INNER', array('M.id', 'D.module_id'))
      ->where(array('D.id', (int)$id))
      ->cacheOn()
      ->toArray()
      ->first('D.*', 'M.config');
    if ($search) {
      $config = @unserialize($search['config']);
      unset($search['config']);
      foreach ($config as $key => $value) {
        $search[$key] = $value;
      }
      return (object)$search;
    }
    return null;
  }
}