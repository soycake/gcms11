<?php
/*
 * @filesource Widgets/Facebook/Views/Settings.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Widgets\Facebook\Views;

/**
 * Facebook Page
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Index extends \Kotchasan\View
{

  /**
   * Facebook Page
   *
   * @return string
   */
  public static function render($query_string)
  {
    $facebook = array();
    $facebook[] = '<div class="fb-page"';
    $facebook[] = ' data-href="https://www.facebook.com/'.$query_string['user'].'/"';
    if (!empty($query_string['height'])) {
      $facebook[] = ' data-height="'.$query_string['height'].'"';
    }
    $facebook[] = ' data-tabs="timeline"';
    $facebook[] = ' data-width="500"';
    $facebook[] = ' data-show-facepile="'.(empty($query_string['show_facepile']) ? 'false' : 'true').'"';
    $facebook[] = ' data-small-header="'.(empty($query_string['small_header']) ? 'false' : 'true').'"';
    $facebook[] = ' data-hide-cover="'.(empty($query_string['hide_cover']) ? 'true' : 'false').'"></div>';
    $facebook[] = '<script>';
    $facebook[] = '(function(d, id) {';
    $facebook[] = 'if (d.getElementById(id)) return;';
    $facebook[] = 'if (d.getElementById("fb-root") === null) {';
    $facebook[] = 'var div = d.createElement("div");';
    $facebook[] = 'div.id="fb-root";';
    $facebook[] = 'd.body.appendChild(div);';
    $facebook[] = '}';
    $facebook[] = 'var js = d.createElement("script");';
    $facebook[] = 'js.id = id;';
    $facebook[] = 'js.src = "//connect.facebook.net/th_TH/sdk.js#xfbml=1&version=v2.7&appId='.(empty(self::$cfg->facebook_appId) ? '' : self::$cfg->facebook_appId).'";';
    $facebook[] = 'd.getElementsByTagName("head")[0].appendChild(js);';
    $facebook[] = '}(document, "facebook-jssdk"));';
    $facebook[] = '</script>';
    return implode("\n", $facebook);
  }
}