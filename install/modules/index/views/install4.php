<?php
/*
 * @filesource index/views/install.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Install;

use \Kotchasan\Http\Request;

/**
 * ติดตั้ง
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{

  /**
   * step 4
   *
   * @return string
   */
  public function render(Request $request)
  {
    $content = array();
    if (defined('INSTALL')) {
      $_SESSION['db_username'] = $request->post('db_username')->username();
      $_SESSION['db_password'] = $request->post('db_password')->topic();
      $_SESSION['db_server'] = $request->post('db_server')->url();
      $_SESSION['db_name'] = $request->post('db_name')->filter('a-z0-9_');
      $_SESSION['prefix'] = $request->post('prefix')->filter('a-z0-9');
      $_SESSION['typ'] = $request->post('typ')->topic();
      $_SESSION['newdb'] = $request->post('newdb')->toInt();
      if ($_SESSION['newdb'] == 1) {
        $db = \Kotchasan\Database::create(array(
            'username' => $_SESSION['db_username'],
            'password' => $_SESSION['db_password'],
            'hostname' => $_SESSION['db_server'],
            'prefix' => $_SESSION['prefix']
        ));
        if ($db->connection()) {
          $sql = 'CREATE DATABASE IF NOT EXISTS `'.$_SESSION['db_name'].'` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci';
          $db->query($sql);
          $db->close();
        }
      }
      $db = \Kotchasan\Database::create(array(
          'username' => $_SESSION['db_username'],
          'password' => $_SESSION['db_password'],
          'dbname' => $_SESSION['db_name'],
          'hostname' => $_SESSION['db_server'],
          'prefix' => $_SESSION['prefix']
      ));
      if (!$db->connection()) {
        return createClass('Index\Dberror\View')->render($request);
      }
      $content[] = '<h2>{TITLE}</h2>';
      $content[] = '<p>การติดตั้งได้ดำเนินการเสร็จเรียบร้อยแล้ว หากคุณต้องการความช่วยเหลือ คุณสามารถ ติดต่อสอบถามได้ที่ <a href="http://www.goragod.com" target="_blank">http://www.goragod.com</a> หรือ <a href="http://gcms.in.th" target="_blank">http://gcms.in.th</a></p>';
      $content[] = '<ul>';
      // config
      self::$cfg->password_key = \Kotchasan\Text::rndname(10, '1234567890');
      self::$cfg->version = self::$cfg->new_version;
      unset(self::$cfg->new_version);
      $f = \Gcms\Config::save(self::$cfg, ROOT_PATH.'settings/config.php');
      $content[] = '<li class="'.($f ? 'correct' : 'incorrect').'">Update file <b>config.php</b> ...</li>';
      $cfg = include(ROOT_PATH.'settings/database.php');
      $cfg['mysql']['username'] = $_SESSION['db_username'];
      $cfg['mysql']['password'] = $_SESSION['db_password'];
      $cfg['mysql']['dbname'] = $_SESSION['db_name'];
      $cfg['mysql']['hostname'] = $_SESSION['db_server'];
      $cfg['mysql']['prefix'] = $_SESSION['prefix'];
      $f = \Gcms\Config::save($cfg, ROOT_PATH.'settings/database.php');
      $content[] = '<li class="'.($f ? 'correct' : 'incorrect').'">Update file <b>database.php</b> ...</li>';
      $datas = array();
      $datas[] = 'sitemap: '.WEB_URL.'sitemap.xml';
      $datas[] = 'User-agent: *';
      $datas[] = 'Disallow: /Gcms/';
      $datas[] = 'Disallow: /Kotchasan/';
      $datas[] = 'Disallow: /ckeditor/';
      $datas[] = 'Disallow: /admin/';
      $datas[] = 'Disallow: /skin/fonts/';
      $f = @fopen(ROOT_PATH.'robots.txt', 'wb');
      fwrite($f, implode("\n", $datas));
      fclose($f);
      $content[] = '<li class="'.($f ? 'correct' : 'incorrect').'">Update file <b>robots.txt</b> ...</li>';
      // .htaccess
      $base_path = str_replace('install/', '', BASE_PATH);
      $datas = array();
      $datas[] = '<IfModule mod_rewrite.c>';
      $datas[] = 'RewriteEngine On';
      $datas[] = 'RewriteBase /';
      $datas[] = 'RewriteRule ^(feed|menu|sitemap|BingSiteAuth)\.(xml|rss)$ '.$base_path.'$1.php [L,QSA]';
      $datas[] = 'RewriteRule ^(.*).rss$ '.$base_path.'feed.php?module=$1 [L,QSA]';
      $datas[] = 'RewriteCond %{REQUEST_FILENAME} !-f';
      $datas[] = 'RewriteCond %{REQUEST_FILENAME} !-d';
      $datas[] = 'RewriteRule . '.$base_path.'index.php [L,QSA]';
      $datas[] = '</IfModule>';
      $datas[] = '<IfModule mod_expires.c>';
      $datas[] = 'ExpiresActive On';
      $datas[] = '<FilesMatch "\.(ico|tpl|eot|svg|ttf|woff)$">';
      $datas[] = 'ExpiresDefault "access plus 1 year"';
      $datas[] = '</FilesMatch>';
      $datas[] = 'ExpiresByType image/x-icon "access plus 1 year"';
      $datas[] = 'ExpiresByType image/jpeg "access plus 1 year"';
      $datas[] = 'ExpiresByType image/png "access plus 1 year"';
      $datas[] = 'ExpiresByType image/gif "access plus 1 year"';
      $datas[] = 'ExpiresByType application/x-shockwave-flash "access plus 1 year"';
      $datas[] = 'ExpiresByType text/html "access plus 3600 seconds"';
      $datas[] = 'ExpiresByType application/xhtml+xml "access plus 3600 seconds"';
      $datas[] = 'ExpiresByType text/javascript "access plus 1 month"';
      $datas[] = 'ExpiresByType text/css "access plus 1 month"';
      $datas[] = '</IfModule>';
      $datas[] = '<FilesMatch "\.(ico|jpg|jpeg|png|gif|swf|tpl|eot|svg|ttf|woff|js|css)$">';
      $datas[] = 'FileETag MTime Size';
      $datas[] = '</FilesMatch>';
      $f = @fopen(ROOT_PATH.'.htaccess', 'wb');
      if ($f) {
        fwrite($f, implode("\n", $datas));
        fclose($f);
      }
      $content[] = '<li class='.($f ? 'correct' : 'incorrect').'>Update file <b>.htaccess</b> ...</li>';
      $content[] = '</ul>';
      $content[] = '<p class=warning>กรุณาลบโฟลเดอร์ <em>install/</em> ออกจาก Server ของคุณ';
      $content[] = '<p>คุณควรปรับ chmod ให้โฟลเดอร์ <em>'.DATA_FOLDER.'</em> เป็น 755 ก่อนดำเนินการต่อ (ถ้าคุณได้ทำการปรับ chmod ด้วยตัวเอง)</p>';
      $content[] = '<p>เมื่อเรียบร้อยแล้ว กรุณา<b>เข้าระบบผู้ดูแล</b>เพื่อตั้งค่าที่จำเป็นอื่นๆโดยใช้ขื่ออีเมล์ <em>'.$_SESSION['email'].'</em> และ รหัสผ่าน <em>'.$_SESSION['password'].'</em></p>';
      $content[] = '<p><a href="'.WEB_URL.'/admin/index.php?module=system" class="button large admin">เข้าระบบผู้ดูแล</a></p>';
    }
    return (object)array(
        'title' => 'ติดตั้ง GCMS เวอร์ชั่น '.self::$cfg->version.' เรียบร้อย',
        'content' => implode('', $content)
    );
  }
}