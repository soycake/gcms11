<?php
/*
 * @filesource edocument/models/admin/write.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Edocument\Admin\Write;

use \Kotchasan\Http\Request;
use \Kotchasan\Language;
use \Gcms\Gcms;
use \Kotchasan\Login;
use \Kotchasan\ArrayTool;
use \Kotchasan\File;
use \Kotchasan\Http\UploadedFile;
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
   * อ่านข้อมูลรายการที่เลือก
   *
   * @param int $module_id ของโมดูล
   * @param int $id ID
   * @param boolean $new true คืนค่า ID ถัดไป, false (default) คืนค่า $id ที่ส่งเข้ามา
   * @return object|null คืนค่าข้อมูล object ไม่พบคืนค่า null
   */
  public static function get($module_id, $id, $new = false)
  {
    // model
    $model = new static;
    $query = $model->db()->createQuery();
    if (empty($id)) {
      // ใหม่ ตรวจสอบโมดูล
      if ($new) {
        $query->select($model->buildNext('id', 'edocument'), 'M.id module_id', 'M.owner', 'M.module', 'M.config');
      } else {
        $query->select('0 id', 'M.id module_id', 'M.owner', 'M.module', 'M.config');
      }
      $query->from('modules M')
        ->where(array(
          array('M.id', $module_id),
          array('M.owner', 'edocument'),
      ));
    } else {
      // แก้ไข ตรวจสอบรายการที่เลือก
      $query->select('A.*', 'M.owner', 'M.module', 'M.config')
        ->from('edocument A')
        ->join('modules M', 'INNER', array(array('M.id', 'A.module_id'), array('M.owner', 'edocument')))
        ->where(array('A.id', $id));
    }
    $result = $query->limit(1)->toArray()->execute();
    if (sizeof($result) == 1) {
      $result = ArrayTool::unserialize($result[0]['config'], $result[0], empty($id));
      unset($result['config']);
      if (empty($id) && $new) {
        $result['document_no'] = sprintf($result['format_no'], $result['id']);
        $result['id'] = 0;
      }
      return (object)$result;
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
        $index = self::get($request->post('module_id')->toInt(), $id);
        if (!$index || !Gcms::canConfig($login, $index, 'can_upload')) {
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
            $ret['location'] = $request->getUri()->postBack('index.php', array('mid' => $index->module_id, 'module' => 'edocument-setup'));
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