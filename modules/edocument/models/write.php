<?php
/*
 * @filesource edocument/models/write.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Edocument\Write;

use \Kotchasan\Http\Request;
use \Kotchasan\Language;
use \Gcms\Gcms;
use \Kotchasan\Login;
use \Kotchasan\ArrayTool;
use \Kotchasan\File;
use \Kotchasan\Antispam;
use \Kotchasan\Text;
use \Gcms\Email;

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
   * query ข้อมูลสำหรับการบันทึก
   *
   * @param int $module_id
   * @param int $id 0 สำหรับรายการใหม่, > 0 สำหรับการแก้ไข
   * @return JSON
   */
  public static function getForSave($module_id, $id)
  {
    // model
    $model = new static;
    $query = $model->db()->createQuery();
    if (empty($id)) {
      // ใหม่ ตรวจสอบโมดูล
      $query->select('M.id module_id', 'M.module', 'M.config', $model->buildNext('id', 'edocument'))
        ->from('modules M')
        ->where(array(array('M.id', $module_id), array('M.owner', 'edocument')));
    } else {
      // แก้ไข ตรวจสอบรายการที่เลือก
      $query->select('A.id', 'A.file', 'A.sender_id', 'A.module_id', 'M.module', 'M.config')
        ->from('edocument A')
        ->join('modules M', 'INNER', array(array('M.id', 'A.module_id'), array('M.owner', 'edocument')))
        ->where(array(array('A.id', $id), array('A.module_id', $module_id)));
    }
    $result = $query->limit(1)->toArray()->execute();
    if (sizeof($result) == 1) {
      $result = ArrayTool::unserialize($result[0]['config'], $result[0]);
      unset($result['config']);
      return (object)$result;
    }
    return null;
  }

  /**
   * อ่านข้อมูลรายการที่เลือก
   *
   * @param object $index Object ข้อมูลโมดูล
   * @param int $id ID 0 ใหม่, > 0 แก้ไข
   * @param boolean $new true คืนค่า ID ถัดไป, false (default) คืนค่า $id ที่ส่งเข้ามา
   * @return object|null คืนค่าข้อมูล object ไม่พบคืนค่า null
   */
  public static function getForWrite($index, $id, $new = false)
  {
    // model
    $model = new static;
    $query = $model->db()->createQuery();
    if (empty($id)) {
      $query->from('index_detail D')
        ->join('index I', 'INNER', array(array('I.index', 1), array('I.id', 'D.id'), array('I.module_id', 'D.module_id'), array('I.language', 'D.language')))
        ->where(array(array('I.module_id', (int)$index->module_id), array('D.language', array(Language::name(), ''))));
      $select = array('D.topic title', 'D.keywords', 'D.description', $model->buildNext('id', 'edocument'));
    } else {
      $query->from('edocument P')
        ->join('index_detail D', 'INNER', array(array('D.module_id', 'P.module_id'), array('D.language', array('', Language::name()))))
        ->join('index I', 'INNER', array(array('I.id', 'D.id'), array('I.module_id', 'D.module_id'), array('I.index', '1'), array('I.language', 'D.language')))
        ->where(array(array('P.id', $id), array('P.module_id', (int)$index->module_id)));
      $select = array('P.*', 'D.topic title', 'D.description', 'D.keywords');
    }
    $search = $query->toArray()->first($select);
    if ($search) {
      foreach ($search as $key => $value) {
        $index->$key = $value;
      }
      // login
      $login = Login::isMember();
      $login = $login ? array('id' => (int)$login['id'], 'status' => $login['status']) : array('id' => 0, 'status' => -1);
      if ($id > 0) {
        // แก้ไข
        if ($index->id == $login['id'] || in_array($login['status'], $index->moderator)) {
          $reciever = @unserialize($search['reciever']);
          $index->reciever = is_array($reciever) ? $reciever : array();
        } else {
          $index = null;
        }
      } elseif (in_array($login['status'], $index->can_upload)) {
        // ใหม่
        $index->reciever = array();
        $index->document_no = sprintf($index->format_no, $search['id']);
        $index->id = 0;
      } else {
        $index = null;
      }
      return $index;
    }
    return null;
  }

  /**
   * บันทึก
   */
  public function save(Request $request)
  {
    $ret = array();
    // referer, session, member
    if ($request->initSession() && $request->isReferer() && $login = Login::isMember()) {
      if ($login['email'] == 'demo') {
        $ret['alert'] = Language::get('Unable to complete the transaction');
      } else {
        // ค่าที่ส่งมา
        $save = array(
          'document_no' => $request->post('document_no')->topic(),
          'reciever' => $request->post('reciever', array())->toInt(),
          'topic' => $request->post('topic')->topic(),
          'detail' => $request->post('detail')->textarea()
        );
        $id = self::$request->post('id')->toInt();
        // ตรวจสอบรายการที่เลือก
        $index = self::getForSave($request->post('module_id')->toInt(), $id);
        // antispam
        $antispam = new Antispam($request->post('antispamid')->toString());
        if (!$antispam->valid($request->post('antispam')->toString())) {
          // Antispam ไม่ถูกต้อง
          $ret['ret_antispam'] = 'this';
        } elseif (!$index || !Gcms::canConfig($login, $index, 'can_upload')) {
          // ไม่พบ หรือไม่สามารถอัปโหลดได้
          $ret['alert'] = Language::get('Can not be performed this request. Because they do not find the information you need or you are not allowed');
        } elseif ($id > 0 && !($login['id'] == $index->sender_id || Gcms::canConfig($login, $index, 'moderator'))) {
          // แก้ไข ไม่ใช่เจ้าของหรือ moderator
          $ret['alert'] = Language::get('Can not be performed this request. Because they do not find the information you need or you are not allowed');
        } else {
          $error = false;
          // document_no
          if ($save['document_no'] == '') {
            $ret['ret_document_no'] = 'this';
            $error = true;
          } else {
            // ค้นหาเลขที่เอกสารซ้ำ
            $search = $this->db()->first($this->getFullTableName('edocument'), array('document_no', $save['document_no']));
            if ($search && ($id == 0 || $id != $search->id)) {
              $ret['ret_document_no'] = str_replace(':name', Language::get('Document number'), Language::get('This :name already exist'));
              $error = true;
            } else {
              $ret['ret_document_no'] = '';
            }
          }
          // reciever
          if (empty($save['reciever'])) {
            $ret['ret_reciever'] = 'this';
            $error = true;
          } else {
            $ret['ret_reciever'] = '';
          }
          // detail
          if ($save['detail'] == '') {
            $ret['ret_detail'] = 'this';
            $error = true;
          } else {
            $ret['ret_detail'] = '';
          }
          if (!$error) {
            // อัปโหลดไฟล์
            foreach (self::$request->getUploadedFiles() as $item => $file) {
              /* @var $file UploadedFile */
              if ($file->hasUploadFile()) {
                $dir = ROOT_PATH.DATA_FOLDER.'edocument/';
                if (!File::makeDirectory($dir)) {
                  // ไดเรคทอรี่ไม่สามารถสร้างได้
                  $ret['ret_'.$item] = sprintf(Language::get('Directory %s cannot be created or is read-only.'), DATA_FOLDER.'edocument/');
                  $error = true;
                } elseif (!$file->validFileExt($index->file_typies)) {
                  // ชนิดของไฟล์ไม่ถูกต้อง
                  $ret['ret_'.$item] = Language::get('The type of file is invalid');
                  $error = true;
                } elseif ($file->getSize() > $index->upload_size) {
                  // ขนาดของไฟล์ใหญ่เกินไป
                  $ret['ret_'.$item] = Language::get('The file size larger than the limit');
                  $error = true;
                } else {
                  $save['ext'] = $file->getClientFileExt();
                  $file_name = str_replace('.'.$save['ext'], '', $file->getClientFilename());
                  if ($file_name == '' && $save['topic'] == '') {
                    $ret['ret_topic'] = 'this';
                    $error = true;
                  } else {
                    // อัปโหลด
                    $save['file'] = Text::rndname(10).'.'.$save['ext'];
                    while (file_exists($dir.$save['file'])) {
                      $save['file'] = Text::rndname(10).'.'.$save['ext'];
                    }
                    try {
                      $file->moveTo($dir.$save['file']);
                      $save['size'] = $file->getSize();
                      if ($save['topic'] == '') {
                        $save['topic'] = $file_name;
                      }
                      if (!empty($index->file) && $save['file'] != $index->file) {
                        @unlink($dir.$index->file);
                      }
                    } catch (\Exception $exc) {
                      // ไม่สามารถอัปโหลดได้
                      $ret['ret_'.$item] = Language::get($exc->getMessage());
                      $error = true;
                    }
                  }
                }
              } elseif ($id == 0) {
                // ใหม่ ต้องมีไฟล์
                $ret['ret_'.$item] = Language::get('Please select file');
                $error = true;
              }
            }
          }
          if (!$error) {
            $save['last_update'] = time();
            $reciever = $save['reciever'];
            $save['reciever'] = serialize($reciever);
            if ($id == 0) {
              // ใหม่
              $save['module_id'] = $index->module_id;
              $save['downloads'] = 0;
              $save['sender_id'] = $login['id'];
              $this->db()->insert($this->getFullTableName('edocument'), $save);
            } else {
              // แก้ไข
              $this->db()->update($this->getFullTableName('edocument'), $id, $save);
            }
            if ($request->post('send_mail')->toInt() == 1) {
              $query = $this->db()->createQuery()->select('fname', 'lname', 'email')->from('user')->where(array('status', $reciever));
              foreach ($query->toArray()->execute() as $item) {
                // ส่งอีเมล์
                $replace = array(
                  '/%FNAME%/' => $item['fname'],
                  '/%LNAME%/' => $item['lname'],
                  '/%URL%/' => WEB_URL.'index.php?module='.$index->module
                );
                Email::send(1, 'edocument', $replace, $item['email']);
              }
              $ret['alert'] = Language::get('Save and email completed');
            } else {
              $ret['alert'] = Language::get('Saved successfully');
            }
            $ret['location'] = WEB_URL.'index.php?module='.$index->module;
            // เคลียร์ antispam
            $antispam->delete();
          }
        }
      }
    } else {
      $ret['alert'] = Language::get('Can not be performed this request. Because they do not find the information you need or you are not allowed');
    }
    // คืนค่าเป็น JSON
    echo json_encode($ret);
  }
}