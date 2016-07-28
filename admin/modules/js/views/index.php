<?php
/*
 * @filesource js/views/index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Js\Index;

use \Kotchasan\Language;

/**
 * Generate JS file
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\KBase
{

  /**
   * สร้างไฟล์ js
   */
  public function index()
  {
    // default js
    $js = array();
    $js[] = file_get_contents(ROOT_PATH.'js/gajax.js');
    $js[] = file_get_contents(ROOT_PATH.'js/ddpanel.js');
    $js[] = file_get_contents(ROOT_PATH.'js/ddmenu.js');
    $js[] = file_get_contents(ROOT_PATH.'js/graphs.js');
    $js[] = file_get_contents(ROOT_PATH.'js/clock.js');
    $js[] = file_get_contents(ROOT_PATH.'js/editinplace.js');
    $js[] = file_get_contents(ROOT_PATH.'js/table.js');
    $js[] = file_get_contents(ROOT_PATH.'js/sorttable.js');
    $js[] = file_get_contents(ROOT_PATH.'js/common.js');
    $js[] = file_get_contents(APP_PATH.'modules/js/views/admin.js');
    $js[] = file_get_contents(APP_PATH.'modules/js/views/fb.js');
    $lng = Language::name();
    $data_folder = Language::languageFolder();
    if (is_file($data_folder.$lng.'.js')) {
      $js[] = file_get_contents($data_folder.$lng.'.js');
    }
    // js ของโมดูล
    $dir = ROOT_PATH.'modules/';
    $f = @opendir($dir);
    if ($f) {
      while (false !== ($text = readdir($f))) {
        if ($text != "." && $text != "..") {
          if (is_dir($dir.$text)) {
            if (is_file($dir.$text.'/admin.js')) {
              $js[] = file_get_contents($dir.$text.'/admin.js');
            }
          }
        }
      }
      closedir($f);
    }
    // js ของ Widgets
    $dir = ROOT_PATH.'Widgets/';
    $f = @opendir($dir);
    if ($f) {
      while (false !== ($text = readdir($f))) {
        if ($text != "." && $text != "..") {
          if (is_dir($dir.$text)) {
            if (is_file($dir.$text.'/admin.js')) {
              $js[] = file_get_contents($dir.$text.'/admin.js');
            }
          }
        }
      }
      closedir($f);
    }
    $languages = Language::getItems(array(
        'MONTH_SHORT',
        'MONTH_LONG',
        'DATE_LONG',
        'DATE_SHORT',
        'YEAR_OFFSET'
    ));
    $js[] = 'var WEB_URL = "'.WEB_URL.'";';
    $js[] = 'Date.monthNames = ["'.implode('", "', $languages['MONTH_SHORT']).'"];';
    $js[] = 'Date.longMonthNames = ["'.implode('", "', $languages['MONTH_LONG']).'"];';
    $js[] = 'Date.longDayNames = ["'.implode('", "', $languages['DATE_LONG']).'"];';
    $js[] = 'Date.dayNames = ["'.implode('", "', $languages['DATE_SHORT']).'"];';
    $js[] = 'Date.yearOffset = '.(int)$languages['YEAR_OFFSET'].';';
    // compress javascript
    $patt = array('#/\*(?:[^*]*(?:\*(?!/))*)*\*/#u', '#[\r\t]#', '#\n//.*\n#', '#;//.*\n#', '#[\n]#', '#[\s]{2,}#');
    $replace = array('', '', '', ";\n", '', ' ');
    // Response
    $response = new \Kotchasan\Http\Response;
    // cache 1 month
    $expire = 2592000;
    $response->withHeaders(array(
        'Content-type' => 'application/javascript; charset=utf-8',
        'Cache-Control' => 'max-age='.$expire.', must-revalidate, public',
        'Expires' => gmdate('D, d M Y H:i:s', time() + $expire).' GMT',
        'Last-Modified' => gmdate('D, d M Y H:i:s', time() - $expire).' GMT'
      ))
      ->setContent(preg_replace($patt, $replace, implode("\n", $js)))
      ->send();
  }
}