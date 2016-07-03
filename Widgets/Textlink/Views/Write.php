<?php
/*
 * @filesource Widgets/Textlink/Views/Settings.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Widgets\Textlink\Views;

use \Kotchasan\Language;
use \Kotchasan\Html;

/**
 * โมดูลสำหรับจัดการการตั้งค่าเริ่มต้น
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Write extends \Kotchasan\View
{

  /**
   * module=Textlink-Write
   *
   * @param object $index
   * @return string
   */
  public function render($index)
  {
    // form
    $form = Html::create('form', array(
        'id' => 'setup_frm',
        'class' => 'setup_frm',
        'autocomplete' => 'off',
        'action' => 'index.php/Widgets/Textlink/Models/Write/save',
        'onsubmit' => 'doFormSubmit',
        'ajax' => true
    ));
    $fieldset = $form->add('fieldset', array(
      'title' => Language::get('Config')
    ));
    // name
    $groups = $fieldset->add('groups-table', array(
      'label' => Language::get('Name'),
      'id' => 'name',
      'comment' => Language::get('Enter the name of Textlink english lowercase letters and numbers. Used for grouping similar position.'),
    ));
    $groups->add('text', array(
      'id' => 'name',
      'labelClass' => 'g-input icon-edit',
      'itemClass' => 'width',
      'maxlength' => 11,
      'value' => $index->name
    ));
    $groups->add('em', array(
      'id' => 'name_demo',
      'class' => 'width',
      'innerHTML' => '{WIDGET_TEXTLINK}'
    ));
    // description
    $fieldset->add('text', array(
      'id' => 'description',
      'labelClass' => 'g-input icon-file',
      'itemClass' => 'item',
      'comment' => Language::get('Notes or short description of the link'),
      'maxlength' => 49,
      'value' => $index->description
    ));
    // type
    $lng = Language::get('TEXTLINK_TYPIES');
    $styles = array();
    foreach (include (ROOT_PATH.'Widgets/Textlink/styles.php') as $key => $value) {
      $styles[$key] = $lng[$key];
    }
    $fieldset->add('select', array(
      'id' => 'type',
      'labelClass' => 'g-input icon-file',
      'itemClass' => 'item',
      'label' => Language::get('Type'),
      'comment' => Language::get('Select the type of link you want, or select the top entry . If you want to link this on their own , such as Adsense.'),
      'options' => $styles,
      'value' => $index->type
    ));
    // template
    $fieldset->add('textarea', array(
      'id' => 'template',
      'labelClass' => 'g-input icon-file',
      'itemClass' => 'item',
      'label' => Language::get('Template'),
      'comment' => Language::get('Fill HTML code for this link You can choose to enter the code from another source , such as Adsense code or description of the links below.'),
      'rows' => 5,
      'placeholder' => '<HTML>'
    ));
    $fieldset = $form->add('fieldset', array(
      'title' => Language::get('Detail')
    ));
    // text
    $fieldset->add('text', array(
      'id' => 'text',
      'labelClass' => 'g-input icon-edit',
      'itemClass' => 'item',
      'label' => Language::get('Text'),
      'comment' => Language::get('Message on the link can be used to force &lt;br&gt; a new text line'),
      'value' => $index->text
    ));
    // url
    $fieldset->add('text', array(
      'id' => 'url',
      'labelClass' => 'g-input icon-world',
      'itemClass' => 'item',
      'label' => Language::get('URL'),
      'comment' => Language::get('Links for this item, which will open this page when click on it'),
      'value' => $index->url
    ));
    // target
    $fieldset->add('select', array(
      'id' => 'target',
      'labelClass' => 'g-input icon-forward',
      'itemClass' => 'item',
      'label' => Language::get('The opening page of links'),
      'comment' => Language::get('Determine how to turn the page when a link is clicked'),
      'options' => Language::get('MENU_TARGET'),
      'value' => $index->target
    ));
    // logo
    if (!empty($index->logo) && is_file(ROOT_PATH.DATA_FOLDER.'image/'.$index->logo)) {
      $img = WEB_URL.DATA_FOLDER.'image/'.$index->logo;
    } else {
      $img = WEB_URL.'skin/img/blank.gif';
    }
    $fieldset->add('file', array(
      'id' => 'logo',
      'labelClass' => 'g-input icon-upload',
      'itemClass' => 'item',
      'label' => Language::get('Image'),
      'comment' => Language::get('Upload an image for the link (If you have). Type jpg, gif, png only, uploaded the image should be the same size.'),
      'dataPreview' => 'imgLogo',
      'previewSrc' => $img
    ));
    $fieldset = $form->add('fieldset', array(
      'title' => Language::get('Publish this item')
    ));
    // publish_start
    $fieldset->add('date', array(
      'id' => 'publish_start',
      'labelClass' => 'g-input icon-calendar',
      'itemClass' => 'item',
      'label' => Language::get('Published date'),
      'value' => date('Y-m-d', $index->publish_start)
    ));
    // publish_end
    $groups = $fieldset->add('groups-table', array(
      'label' => Language::get('Published close'),
      'id' => 'publish_end',
      'comment' => Language::get('The date of the start and end of the link. (Links are performed within a given time automatically.)'),
    ));
    $groups->add('date', array(
      'id' => 'publish_end',
      'labelClass' => 'g-input icon-calendar',
      'itemClass' => 'width',
      'disabled' => $index->publish_end == 0,
      'value' => date('Y-m-d', $index->publish_end)
    ));
    $groups->add('checkbox', array(
      'id' => 'dateless',
      'itemClass' => 'width',
      'label' => Language::get('Dateless'),
      'checked' => $index->publish_end == 0,
      'value' => 1
    ));
    $fieldset = $form->add('fieldset', array(
      'class' => 'submit'
    ));
    // submit
    $fieldset->add('submit', array(
      'class' => 'button ok large',
      'value' => Language::get('Save')
    ));
    // id
    $fieldset->add('hidden', array(
      'id' => 'id',
      'value' => $index->id
    ));
    $form->script('initTextlinkWrite();');
    return $form->render();
  }
}