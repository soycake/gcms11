<?php
/*
 * @filesource index/views/memberstatus.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Memberstatus;

use \Kotchasan\Html;
use \Kotchasan\Language;

/**
 * ฟอร์ม Memberstatus
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{

  /**
   * module=memberstatus
   *
   * @return string
   */
  public function render()
  {
    $section = Html::create('div', array(
        'class' => 'subtitle',
        'innerHTML' => Language::get('Status of membership, the first item (0) means end users and 1 represents the administrator. (The first two items are the items necessary), you can modify the ability of each member of the modules again.')
    ));
    $list = $section->add('ol', array(
      'class' => 'editinplace_list',
      'id' => 'config_status'
    ));
    foreach (self::$cfg->member_status as $s => $item) {
      $row = $list->add('li', array(
        'id' => 'config_status_'.$s
      ));
      if ($s > 1) {
        $row->add('span', array(
          'id' => 'config_status_delete_'.$s,
          'class' => 'icon-delete',
          'title' => Language::get('Delete')
        ));
      } else {
        $row->add('span');
      }
      $row->add('span', array(
        'id' => 'config_status_color_'.$s,
        'title' => self::$cfg->color_status[$s]
      ));
      $row->add('span', array(
        'id' => 'config_status_name_'.$s,
        'innerHTML' => $item,
        'title' => Language::get('click to edit')
      ));
    }
    $div = $section->add('div', array(
      'class' => 'submit'
    ));
    $a = $div->add('a', array(
      'class' => 'button add large',
      'id' => 'config_status_add'
    ));
    $a->add('span', array(
      'class' => 'icon-plus',
      'innerHTML' => Language::get('Add New').' '.Language::get('Member status')
    ));
    $section->script('initEditInplace("config_status", "memberstatus");');
    $section->script('$E("config_status_color_0").focus();');
    return $section->render();
  }
}