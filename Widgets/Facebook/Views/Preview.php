<?php
/*
 * Widgets/Facebook/preview.php
 *
 * @author Goragod Wiriya <admin@goragod.com>
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */
// load Kotchasan
include '../../../load.php';
// Initial Kotchasan Framework
$cfg = Gcms\Config::create();
// หน้าเว็บ Facebook
$facebook = array();
$facebook[] = '<!DOCTYPE html>';
$facebook[] = '<html>';
$facebook[] = '<head>';
$facebook[] = '<title>Facebook</title>';
$facebook[] = '<meta charset=utf-8>';
$facebook[] = '<style>';
$facebook[] = '#fb-root{display: none}';
$facebook[] = '.fb_iframe_widget, .fb_iframe_widget span, .fb_iframe_widget span iframe[style] {width: 100% !important;}';
$facebook[] = '</style>';
$facebook[] = '</head>';
$facebook[] = '<body>';
$facebook[] = '<div id=fb-root></div>';
$facebook[] = '<div class="fb-page"';
$facebook[] = ' data-href="https://www.facebook.com/'.$cfg->facebook_page['user'].'/"';
if ($cfg->facebook_page['height'] > 0) {
  $facebook[] = ' data-height="'.$cfg->facebook_page['height'].'"';
}
$facebook[] = ' data-tabs="timeline"';
$facebook[] = ' data-width="500"';
$facebook[] = ' data-show-facepile="'.($cfg->facebook_page['show_facepile'] == 1 ? 'true' : 'false').'"';
$facebook[] = ' data-small-header="'.($cfg->facebook_page['small_header'] == 1 ? 'true' : 'false').'"';
$facebook[] = ' data-hide-cover="'.($cfg->facebook_page['hide_cover'] == 1 ? 'false' : 'true').'"></div>';
$facebook[] = '<script>';
$facebook[] = '(function(d, id) {';
$facebook[] = 'var js = d.createElement("script");';
$facebook[] = 'js.id = id;';
$facebook[] = 'js.src = "//connect.facebook.net/th_TH/sdk.js#xfbml=1&version=v2.7&appId='.(empty($cfg->facebook_appId) ? '' : $cfg->facebook_appId).'";';
$facebook[] = 'd.getElementsByTagName("head")[0].appendChild(js);';
$facebook[] = '}(document, "facebook-jssdk"));';
$facebook[] = '</script>';
$facebook[] = '</body>';
$facebook[] = '</html>';
echo implode("\n", $facebook);
