<?php
/*
 * @filesource index/views/database.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Database;

use \Kotchasan\Html;
use \Kotchasan\Language;
use \Gcms\Gcms;

/**
 * Database
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{

  /**
   * module=database
   * ฟอร์ม export
   *
   * @param object $db Database Object
   * @return string
   */
  public function export($db)
  {
    $form = Html::create('form', array(
        'id' => 'export_frm',
        'class' => 'paper',
        'autocomplete' => 'off',
        'action' => 'index.php/index/model/database/export',
        'target' => '_export'
    ));
    $fieldset = $form->add('fieldset', array(
      'titleClass' => 'icon-export',
      'title' => '{LNG_Backup database}'
    ));
    $fieldset->add('div', array(
      'class' => 'subtitle',
      'innerHTML' => '{LNG_When you press the button below. GCMS will create <em>:dbname.sql</em> file for save on your computer. This file contains all the information in the database. You can use it to restore your system, or used to move data to another site.}'
    ));
    $structure = Language::get('Structure');
    $datas = Language::get('Datas');
    $content = array();
    $content[] = '<div class=item>';
    $content[] = '<table class="responsive database fullwidth"><tbody id=language_tbl>';
    $content[] = '<tr><td class=tablet></td><td colspan=3 class=left><a href="javascript:setSelect(\'language_tbl\',true)">{LNG_Select all}</a>&nbsp;|&nbsp;<a href="javascript:setSelect(\'language_tbl\',false)">{LNG_Clear selectd}</a></td></tr>';
    foreach ($db->showTables() as $table) {
      if (preg_match('/^'.$db->getSetting('prefix').'_(.*?)$/', $table['Name'], $match)) {
        $tr = '<tr>';
        $tr .= '<th>'.$table['Name'].'</th>';
        $tr .= '<td><label class=nowrap><input type=checkbox name='.$table['Name'].'[] value=sturcture checked>&nbsp;'.$structure.'</label></td>';
        $tr .= '<td><label class=nowrap><input type=checkbox name='.$table['Name'].'[] value=datas checked>&nbsp;'.$datas.'</label></td>';
        $tr .= '</tr>';
        $content[] = $tr;
      }
    }
    $content[] = '<tr><td class=tablet></td><td colspan=3 class=left><a href="javascript:setSelect(\'language_tbl\',true)">{LNG_Select all}</a>&nbsp;|&nbsp;<a href="javascript:setSelect(\'language_tbl\',false)">{LNG_Clear selectd}</a></td></tr>';
    $content[] = '</tbody></table>';
    $content[] = '</div>';
    $fieldset->appendChild(implode("\n", $content));
    $fieldset = $form->add('fieldset', array(
      'class' => 'submit'
    ));
    // submit
    $fieldset->add('submit', array(
      'class' => 'button ok large',
      'value' => '{LNG_Export}'
    ));
    Gcms::$view->setContents(array(
      '/:dbname/' => $db->getSetting('dbname'),
      '/:size/' => ini_get('upload_max_filesize')
      ), false);
    return $form->render();
  }

  /**
   * ฟอร์ม import
   *
   * @param object $db Database Object
   * @return string
   */
  public function import($db)
  {
    $form = Html::create('form', array(
        'id' => 'import_frm',
        'autocomplete' => 'off',
        'action' => 'index.php/index/model/database/import',
        'onsubmit' => 'doFormSubmit',
        'confirmsubmit' => 'doCustomConfirm("{LNG_Do you want to import the database?}")',
        'ajax' => true
    ));
    $fieldset = $form->add('fieldset', array(
      'titleClass' => 'icon-import',
      'title' => '{LNG_Import data from databases or to recover data from a previously backed up}'
    ));
    $fieldset->add('file', array(
      'id' => 'import_file',
      'labelClass' => 'g-input icon-upload',
      'itemClass' => 'item',
      'label' => '{LNG_Select a file to import (less than :size)}',
      'comment' => '{LNG_Browse the database file (<em>:dbname.sql</em>) that you back it up from this system only.}'
    ));
    $fieldset = $form->add('fieldset', array(
      'class' => 'submit'
    ));
    // submit
    $fieldset->add('submit', array(
      'class' => 'button ok large',
      'value' => '{LNG_Import}'
    ));
    $form->add('aside', array(
      'class' => 'warning',
      'innerHTML' => '{LNG_<strong>Warning</strong> : Import database will replace your database with data from uploaded file. Therefore, you should make sure that the database file of GCMS. (unsupported database version 3 or lower.) If you are unsure. Please back up this database again before any action}'
    ));
    return $form->render();
  }
}