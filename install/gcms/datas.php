TRUNCATE `{prefix}_board_q`;
TRUNCATE `{prefix}_board_r`;
TRUNCATE `{prefix}_category`;
TRUNCATE `{prefix}_comment`;
TRUNCATE `{prefix}_index`;
TRUNCATE `{prefix}_index_detail`;
TRUNCATE `{prefix}_menus`;
TRUNCATE `{prefix}_modules`;
TRUNCATE `{prefix}_tags`;
TRUNCATE `{prefix}_textlink`;
INSERT INTO `{prefix}_board_q` (`id`, `module_id`, `category_id`, `sender`, `member_id`, `email`, `ip`, `create_date`, `last_update`, `visited`, `comments`, `comment_id`, `commentator`, `commentator_id`, `comment_date`, `picture`, `pictureW`, `pictureH`, `hassubpic`, `can_reply`, `published`, `pin`, `locked`, `related`, `topic`, `detail`) VALUES ('1','4','2','','1','admin@goragod.com','171.5.250.37','1379218020','1430719076','262','1','3','hansa-hi@hotmail.com','0','1430719076','','0','0','0','0','0','0','0','','กระทู้แรกของคุณ','นี่คือ กระทู้แรกของคุณ');
INSERT INTO `{prefix}_board_r` (`id`, `module_id`, `index_id`, `detail`, `sender`, `member_id`, `email`, `ip`, `last_update`, `picture`, `pictureW`, `pictureH`) VALUES ('3','4','1','ความคิดเห็นจากบุคคลทั่วไป','','0','hansa-hi@hotmail.com','49.49.135.93','1430719076','','0','0');
INSERT INTO `{prefix}_category` (`id`, `module_id`, `category_id`, `group_id`, `config`, `c1`, `c2`, `topic`, `detail`, `icon`, `published`) VALUES ('1','3','1','0','can_reply=0','1','0','a:1:{s:0:"";s:36:"บทความทั่วไป";}','a:1:{s:0:"";s:76:"บทความทั่วไป แหล่งเรียนรู้";}','','1');
INSERT INTO `{prefix}_category` (`id`, `module_id`, `category_id`, `group_id`, `config`, `c1`, `c2`, `topic`, `detail`, `icon`, `published`) VALUES ('2','3','2','0','published=1\ncan_reply=0','1','1','a:1:{s:0:"";s:12:"ข่าว";}','a:1:{s:0:"";s:54:"ข่าวสารจากเว็บไซต์";}','','1');
INSERT INTO `{prefix}_category` (`id`, `module_id`, `category_id`, `group_id`, `config`, `c1`, `c2`, `topic`, `detail`, `icon`, `published`) VALUES ('3','4','1','0','can_post=0,2,1\ncan_view=-1,0,2,1\ncan_reply=0,2,1\nmoderator=2,1\nimg_upload_size=500\nimg_law=0\nimg_upload_type=jpg','0','0','a:1:{s:0:"";s:18:"ทั่วไป";}','a:1:{s:0:"";s:52:"ถาม-ตอบปัญหาทั่วไป";}','','1');
INSERT INTO `{prefix}_category` (`id`, `module_id`, `category_id`, `group_id`, `config`, `c1`, `c2`, `topic`, `detail`, `icon`, `published`) VALUES ('4','4','2','0','can_post=-1,0,2,1\ncan_view=-1,0,2,1\ncan_reply=-1,0,2,1\nmoderator=2,1\nimg_upload_size=500\nimg_law=0\nimg_upload_type=jpg','1','1','a:1:{s:0:"";s:27:"แจ้งปัญหา";}','a:1:{s:0:"";s:78:"แจ้งปัญหาการใช้งานเว็บไซต์";}','','1');
INSERT INTO `{prefix}_comment` (`id`, `module_id`, `index_id`, `detail`, `sender`, `member_id`, `email`, `ip`, `last_update`, `picture`, `pictureW`, `pictureH`) VALUES ('1','3','6','นี่คือความคิดเห็นแรกของคุณ','','1','admin@goragod.com','171.5.250.37','1430718388','','0','0');
INSERT INTO `{prefix}_comment` (`id`, `module_id`, `index_id`, `detail`, `sender`, `member_id`, `email`, `ip`, `last_update`, `picture`, `pictureW`, `pictureH`) VALUES ('2','3','7','<img alt="laugh" height="20" src="{WEBURL}ckeditor/plugins/smiley/images/teeth_smile.gif" title="laugh" width="20" />','','1','admin@goragod.com','171.5.250.37','1379219933','','0','0');
INSERT INTO `{prefix}_index` (`id`, `index`, `module_id`, `category_id`, `language`, `sender`, `member_id`, `email`, `ip`, `create_date`, `last_update`, `visited`, `visited_today`, `comments`, `comment_id`, `commentator`, `commentator_id`, `comment_date`, `picture`, `pictureW`, `pictureH`, `hassubpic`, `can_reply`, `show_news`, `published`, `pin`, `locked`, `published_date`, `alias`) VALUES ('1','1','1','0','','','1','','223.206.32.8','0','1416556163','7651','0','0','0','','0','0','','0','0','0','0','1','1','1','1','2013-06-22','GCMS Ajax CMS');
INSERT INTO `{prefix}_index` (`id`, `index`, `module_id`, `category_id`, `language`, `sender`, `member_id`, `email`, `ip`, `create_date`, `last_update`, `visited`, `visited_today`, `comments`, `comment_id`, `commentator`, `commentator_id`, `comment_date`, `picture`, `pictureW`, `pictureH`, `hassubpic`, `can_reply`, `show_news`, `published`, `pin`, `locked`, `published_date`, `alias`) VALUES ('2','1','2','0','','','1','','49.48.206.73','0','1367724891','521','0','0','0','','0','0','','0','0','0','0','1','1','0','0','2013-06-22','เกี่ยวกับเรา - ติดต่อเรา');
INSERT INTO `{prefix}_index` (`id`, `index`, `module_id`, `category_id`, `language`, `sender`, `member_id`, `email`, `ip`, `create_date`, `last_update`, `visited`, `visited_today`, `comments`, `comment_id`, `commentator`, `commentator_id`, `comment_date`, `picture`, `pictureW`, `pictureH`, `hassubpic`, `can_reply`, `show_news`, `published`, `pin`, `locked`, `published_date`, `alias`) VALUES ('3','1','3','0','','','0','','','0','0','0','0','0','0','','0','0','','0','0','0','0','1','1','0','0','2013-06-22','บทความ แหล่งเรียนรู้ :: Knowledge');
INSERT INTO `{prefix}_index` (`id`, `index`, `module_id`, `category_id`, `language`, `sender`, `member_id`, `email`, `ip`, `create_date`, `last_update`, `visited`, `visited_today`, `comments`, `comment_id`, `commentator`, `commentator_id`, `comment_date`, `picture`, `pictureW`, `pictureH`, `hassubpic`, `can_reply`, `show_news`, `published`, `pin`, `locked`, `published_date`, `alias`) VALUES ('4','1','4','0','','','0','','','0','0','1','0','0','0','','0','0','','0','0','0','0','1','1','0','0','2013-06-22','GCMS บอร์ด');
INSERT INTO `{prefix}_index` (`id`, `index`, `module_id`, `category_id`, `language`, `sender`, `member_id`, `email`, `ip`, `create_date`, `last_update`, `visited`, `visited_today`, `comments`, `comment_id`, `commentator`, `commentator_id`, `comment_date`, `picture`, `pictureW`, `pictureH`, `hassubpic`, `can_reply`, `show_news`, `published`, `pin`, `locked`, `published_date`, `alias`) VALUES ('5','0','3','1','','','1','','49.49.135.93','1379217780','1430720770','171','0','0','0','','0','0','','0','0','0','0','','1','0','0','2013-09-15','บทความแรกของคุณ');
INSERT INTO `{prefix}_index` (`id`, `index`, `module_id`, `category_id`, `language`, `sender`, `member_id`, `email`, `ip`, `create_date`, `last_update`, `visited`, `visited_today`, `comments`, `comment_id`, `commentator`, `commentator_id`, `comment_date`, `picture`, `pictureW`, `pictureH`, `hassubpic`, `can_reply`, `show_news`, `published`, `pin`, `locked`, `published_date`, `alias`) VALUES ('6','0','3','2','','','1','','49.49.135.93','1379217840','1430747275','188','1','1','5','hansa-hi@hotmail.com','0','1430718573','','0','0','0','1','news=1','1','0','0','2013-09-15','ข่าวแรกของคุณ');
INSERT INTO `{prefix}_index` (`id`, `index`, `module_id`, `category_id`, `language`, `sender`, `member_id`, `email`, `ip`, `create_date`, `last_update`, `visited`, `visited_today`, `comments`, `comment_id`, `commentator`, `commentator_id`, `comment_date`, `picture`, `pictureW`, `pictureH`, `hassubpic`, `can_reply`, `show_news`, `published`, `pin`, `locked`, `published_date`, `alias`) VALUES ('7','1','8','0','','','0','','','0','0','0','0','0','0','','0','0','','0','0','0','0','1','1','0','0','0000-00-00','');
INSERT INTO `{prefix}_index` (`id`, `index`, `module_id`, `category_id`, `language`, `sender`, `member_id`, `email`, `ip`, `create_date`, `last_update`, `visited`, `visited_today`, `comments`, `comment_id`, `commentator`, `commentator_id`, `comment_date`, `picture`, `pictureW`, `pictureH`, `hassubpic`, `can_reply`, `show_news`, `published`, `pin`, `locked`, `published_date`, `alias`) VALUES ('8','1','5','0','','','0','','','0','0','0','0','0','0','','0','0','','0','0','0','0','1','1','0','0','0000-00-00','');
INSERT INTO `{prefix}_index` (`id`, `index`, `module_id`, `category_id`, `language`, `sender`, `member_id`, `email`, `ip`, `create_date`, `last_update`, `visited`, `visited_today`, `comments`, `comment_id`, `commentator`, `commentator_id`, `comment_date`, `picture`, `pictureW`, `pictureH`, `hassubpic`, `can_reply`, `show_news`, `published`, `pin`, `locked`, `published_date`, `alias`) VALUES ('9','1','6','0','','','0','','','0','0','0','0','0','0','','0','0','','0','0','0','0','1','1','0','0','0000-00-00','');
INSERT INTO `{prefix}_index` (`id`, `index`, `module_id`, `category_id`, `language`, `sender`, `member_id`, `email`, `ip`, `create_date`, `last_update`, `visited`, `visited_today`, `comments`, `comment_id`, `commentator`, `commentator_id`, `comment_date`, `picture`, `pictureW`, `pictureH`, `hassubpic`, `can_reply`, `show_news`, `published`, `pin`, `locked`, `published_date`, `alias`) VALUES ('11','1','8','0','','','0','','','0','0','0','0','0','0','','0','0','','0','0','0','0','1','1','0','0','0000-00-00','');
INSERT INTO `{prefix}_index_detail` (`id`, `module_id`, `language`, `topic`, `description`, `detail`, `keywords`, `relate`) VALUES ('2','2','','เกี่ยวกับเรา - ติดต่อเรา','เกี่ยวกับเรา ติดต่อเรา About Us','{WIDGET_CONTACT_admin@demo.com}','เกี่ยวกับเรา ติดต่อเรา About Us','เกี่ยวกับเรา ติดต่อเรา About Us');
INSERT INTO `{prefix}_index_detail` (`id`, `module_id`, `language`, `topic`, `description`, `detail`, `keywords`, `relate`) VALUES ('3','3','','บทความ แหล่งเรียนรู้ :: Knowledge','บทความ แหล่งเรียนรู้ Knowledge goragod.com','','บทความ แหล่งเรียนรู้ Knowledge goragod.com','บทความ แหล่งเรียนรู้ Knowledge goragod.com');
INSERT INTO `{prefix}_index_detail` (`id`, `module_id`, `language`, `topic`, `description`, `detail`, `keywords`, `relate`) VALUES ('4','4','','GCMS บอร์ด','เว็บบอร์ด GCMS Board ถามตอปัญหาคาใจ www.gcms.in.th','','เว็บบอร์ด GCMS Board ถามตอปัญหาคาใจ www.gcms.in.th','เว็บบอร์ด GCMS Board ถามตอปัญหาคาใจ www.gcms.in.th');
INSERT INTO `{prefix}_index_detail` (`id`, `module_id`, `language`, `topic`, `description`, `detail`, `keywords`, `relate`) VALUES ('5','3','','บทความแรกของคุณ','นี่คือบทความแรกของคุณ คุณ สามารถ ลบ หรือ แก้ไขมันได้','นี่คือบทความแรกของคุณ คุณ สามารถ ลบ หรือ แก้ไขมันได้','บทความแรกของคุณ','บทความแรกของคุณ');
INSERT INTO `{prefix}_index_detail` (`id`, `module_id`, `language`, `topic`, `description`, `detail`, `keywords`, `relate`) VALUES ('6','3','','ข่าวแรกของคุณ','นี่คือข่าวแรกของคุณ คุณสามารถ ลบ หรือ แก้ไขได้','นี่คือข่าวแรกของคุณ คุณสามารถ ลบ หรือ แก้ไขได้','ข่าวแรกของคุณ','ข่าวแรกของคุณ');
INSERT INTO `{prefix}_index_detail` (`id`, `module_id`, `language`, `topic`, `description`, `detail`, `keywords`, `relate`) VALUES ('7','3','','ทดสอบครับ','ทดสอบครับ ทดสอบครับ','ทดสอบครับ ทดสอบครับ','ทดสอบครับ','ทดสอบครับ');
INSERT INTO `{prefix}_index_detail` (`id`, `module_id`, `language`, `topic`, `description`, `detail`, `keywords`, `relate`) VALUES ('8','5','','Download','','','','');
INSERT INTO `{prefix}_index_detail` (`id`, `module_id`, `language`, `topic`, `description`, `detail`, `keywords`, `relate`) VALUES ('9','6','','Gallery','','','','');
INSERT INTO `{prefix}_index_detail` (`id`, `module_id`, `language`, `topic`, `description`, `detail`, `keywords`, `relate`) VALUES ('11','8','','Video','','','','');
INSERT INTO `{prefix}_index_detail` (`id`, `module_id`, `language`, `topic`, `description`, `detail`, `keywords`, `relate`) VALUES ('1','1','','GCMS Ajax CMS','GCMS Ajax CMS','<h1>ยินดีต้อนรับสู่ GCMS</h1>\r\n\r\n<div>\r\n<p style="margin-bottom: 10px;">ขณะนี้คุณได้ติดตั้ง GCMS เป็นที่เรียบร้อยแล้ว และนี่คือหน้าแรกของคุณ</p>\r\n\r\n<p style="margin-bottom: 10px;">GCMS เป็น Ajax CMS สมบูรณ์แบบ ที่พัฒนาโดยคนไทยทั้งระบบ เพื่อให้การใช้งาน CMS แบบ Ajax เป็นเรื่องที่ง่ายขึ้น และแก้ปัญหาของ Ajax ที่เป็นที่กังวล ทั้งในเรื่องการรองรับหลายบราวเซอร์, การปิดการใช้งาน Javascript, การ Refresh หน้า, การ Bookmark, การใช้งานปุ่ม History ของบราวเซอร์ และที่สำคัญคือ SEO</p>\r\n\r\n<p style="margin-bottom: 10px;">GCMS เหมาะสมสำหรับการทำเว็บไซต์ทั่วไป ทั้งเว็บไซต์ส่วนบุคคล หน่วยงานราชการ หรือเว็บไซต์อื่นๆ ตั้งแต่ขนาดเล็ก ไปจนถึงเว็บไซต์ขนาดใหญ่ ด้วยการออกแบบที่เน้นอิสระในการปรับปรุงหน้าเว็บ ไม่มุ่งเน้นไปที่เว็บไซต์แบบใดแบบหนึ่งโดยเฉพาะ และมีคุณสมบัติเบื้องต้นต่อการออกแบบเว็บไซต์ทั่วไปได้ง่าย นอกจากนี้ GCMS ยังเหมาะสมเป็นอย่างยิ่ง สำหรับผู้ที่ชอบออกแบบเว็บด้วยตัวเอง ด้วยการใช้ GCMS เป็นพื้นฐานของเว็บเนื่องจาก GCMS มีระบบพื้นฐานครบถ้วน และออกแบบโมดูลตามความต้องการเพื่อใช้งานร่วมกับ GCMS ซึ่งทำให้ประหยัดเวลาในการออกแบบเว็บลงเป็นอย่างมาก</p>\r\n\r\n<p style="margin-bottom: 10px;">หากคุณมีข้อสงสัย ข้อเสนอแนะ หรือต้องการความช่วยเหลือเกี่ยวกับ GCMS คุณสามารถหาความช่วยเหลือนั้นได้ที่ <a href="http://www.goragod.com" rel="nofollow">http://www.goragod.com</a> และคุณสามารถแสดงความคิดเห็นของคุณต่อ GCMS รวมถึงปัญหาในการใช้งาน ตลอดจนข้อผิดพลาดต่างๆ ของ GCMS ได้ ความคิดเห็นของคุณจะช่วยผมในการพัฒนา GCMS ให้ดียิ่งขึ้นในโอกาศต่อไป</p>\r\n\r\n<p style="text-align: right; margin-bottom: 10px;">ด้วยความขอบคุณ<br>\r\n<strong>กรกฎ วิริยะ</strong></p>\r\n\r\n<p style="font-weight: bold; margin-bottom: 10px;">ข้อตกลงการนำ GCMS ไปใช้งาน</p>\r\n\r\n<ul style="padding: 0px 0px 0px 40px;">\r\n	<li>GCMS เป็น Ajax CMS ชนิด Open Source ที่แจกจ่ายให้กับทุกคนสามารถนำไปใช้งานได้ ฟรี โดยมีเงื่อนไขว่า <strong>ต้องติดข้อความหรือเครื่องหมายของผู้พัฒนาไว้เสมอ </strong>(<a href="http://www.goragod.com" rel="nofollow">http://www.goragod.com</a> และ <a href="http://www.webshopready.com" rel="nofollow">http://www.webshopready.com</a>)</li>\r\n	<li>ห้ามนำ GCMS หรือ ส่วนหนึ่งส่วนใดของ GCMS ไป จำหน่าย จ่าย แจก หรือ ใช้งานกับบุคคลที่สาม เว้นแต่จะได้รับความยินยอมจากผู้พัฒนา</li>\r\n	<li>คุณสามารถพัฒนาต่อยอด เพิ่มเติม แก้ไข หรือ ดัดแปลง GCMS ได้เพื่อการใช้งานส่วนบุคคล โดยต้องติดข้อความหรือโลโกของผู้พัฒนาไว้เสมอไม่ว่าจะเปลี่ยนแปลงไปมากน้อยแค่ไหนก็ตาม</li>\r\n	<li>คุณสามารถพัฒนา โมดูลเสริม หรือ วิดเจ็ท หรือ ส่วนประกอบอื่นใด (นอกจากที่มีแจกจ่ายโดยผู้พัฒนาอยู่แล้ว) เพื่อใช้หรือเพื่อจำหน่าย จ่าย แจกได้ โดยให้สิทธิ์เป็นของผู้พัฒนาเอง (ขายหรือแจกเฉพาะโมดูล)</li>\r\n	<li>หากคุณต้องการนำ GCMS ไปใช้กับบุคลที่สาม หรือ เพื่อขาย หรือต้องการนำเครื่องหมายหรือข้อความของผู้พัฒนาออก คุณสามารถติดต่อกับผู้พัฒนาได้โดยตรง ตามที่อยู่ใน <a href="http://www.goragod.com/about.html" rel="nofollow">http://www.goragod.com/about.html</a></li>\r\n</ul>\r\n</div>\r\n','ยินดีต้อนรับสู่ GCMS ขณะนี้คุณได้ติดตั้ง GCMS เป็นที่เรียบร้อยแล้ว และนี่คือหน้าแรกของคุณ GCMS เป็น Ajax CMS สมบูรณ์แบบ ที่พัฒนาโดยคนไทยทั้งระบบ','');
INSERT INTO `{prefix}_menus` (`id`, `index_id`, `parent`, `level`, `language`, `menu_text`, `menu_tooltip`, `accesskey`, `menu_order`, `menu_url`, `menu_target`, `alias`, `published`) VALUES ('1','1','MAINMENU','0','','หน้าหลัก','หน้าหลัก :: Home','','1','','','','1');
INSERT INTO `{prefix}_menus` (`id`, `index_id`, `parent`, `level`, `language`, `menu_text`, `menu_tooltip`, `accesskey`, `menu_order`, `menu_url`, `menu_target`, `alias`, `published`) VALUES ('2','0','MAINMENU','1','','GCMS','เว็บหลัก GCMS','','4','http://gcms.in.th','','','1');
INSERT INTO `{prefix}_menus` (`id`, `index_id`, `parent`, `level`, `language`, `menu_text`, `menu_tooltip`, `accesskey`, `menu_order`, `menu_url`, `menu_target`, `alias`, `published`) VALUES ('3','3','MAINMENU','0','','บทความ','บทความ แหล่งเรียนรู้','','2','','','','1');
INSERT INTO `{prefix}_menus` (`id`, `index_id`, `parent`, `level`, `language`, `menu_text`, `menu_tooltip`, `accesskey`, `menu_order`, `menu_url`, `menu_target`, `alias`, `published`) VALUES ('4','4','MAINMENU','0','','บอร์ด','เว็บบอร์ด :: GCMS Board','','3','','','','1');
INSERT INTO `{prefix}_menus` (`id`, `index_id`, `parent`, `level`, `language`, `menu_text`, `menu_tooltip`, `accesskey`, `menu_order`, `menu_url`, `menu_target`, `alias`, `published`) VALUES ('5','2','MAINMENU','0','','About Us','ติดต่อเรา :: About Us','','5','','','','1');
INSERT INTO `{prefix}_modules` (`id`, `owner`, `module`, `config`) VALUES ('1','index','home','');
INSERT INTO `{prefix}_modules` (`id`, `owner`, `module`, `config`) VALUES ('2','index','about','');
INSERT INTO `{prefix}_modules` (`id`, `owner`, `module`, `config`) VALUES ('3','document','knowledge','a:17:{s:10:"icon_width";i:600;s:11:"icon_height";i:400;s:10:"img_typies";a:2:{i:0;s:3:"jpg";i:1;s:4:"jpeg";}s:9:"published";i:1;s:13:"list_per_page";i:20;s:4:"sort";i:1;s:8:"new_date";i:604800;s:7:"viewing";i:0;s:16:"category_display";i:1;s:10:"news_count";i:10;s:9:"news_sort";i:1;s:9:"can_reply";a:3:{i:0;i:-1;i:1;i:0;i:2;i:1;}s:8:"can_view";a:3:{i:0;i:-1;i:1;i:0;i:2;i:1;}s:9:"can_write";a:1:{i:0;i:1;}s:9:"moderator";a:1:{i:0;i:1;}s:10:"can_config";a:1:{i:0;i:1;}s:12:"default_icon";s:37:"modules/document/img/default_icon.png";}');
INSERT INTO `{prefix}_modules` (`id`, `owner`, `module`, `config`) VALUES ('4','board','forum','a:17:{s:10:"icon_width";i:600;s:11:"icon_height";i:400;s:10:"img_typies";a:2:{i:0;s:3:"jpg";i:1;s:4:"jpeg";}s:13:"list_per_page";i:20;s:8:"new_date";i:604800;s:7:"viewing";i:0;s:16:"category_display";i:1;s:10:"news_count";i:10;s:15:"img_upload_type";a:1:{i:0;s:3:"jpg";}s:15:"img_upload_size";i:1024;s:7:"img_law";i:0;s:8:"can_post";a:3:{i:0;i:-1;i:1;i:0;i:2;i:1;}s:9:"can_reply";a:3:{i:0;i:-1;i:1;i:0;i:2;i:1;}s:8:"can_view";a:3:{i:0;i:-1;i:1;i:0;i:2;i:1;}s:9:"moderator";a:1:{i:0;i:1;}s:10:"can_config";a:1:{i:0;i:1;}s:12:"default_icon";s:34:"modules/board/img/default_icon.png";}');
INSERT INTO `{prefix}_modules` (`id`, `owner`, `module`, `config`) VALUES ('5','download','download','a:7:{s:11:"file_typies";a:8:{i:0;s:3:"doc";i:1;s:3:"ppt";i:2;s:4:"pptx";i:3;s:4:"docx";i:4;s:3:"rar";i:5;s:3:"zip";i:6;s:3:"jpg";i:7;s:3:"pdf";}s:11:"upload_size";i:2097152;s:13:"list_per_page";i:20;s:4:"sort";i:1;s:12:"can_download";a:3:{i:0;i:-1;i:1;i:0;i:2;i:1;}s:10:"can_upload";a:1:{i:0;i:1;}s:10:"can_config";a:1:{i:0;i:1;}}');
INSERT INTO `{prefix}_modules` (`id`, `owner`, `module`, `config`) VALUES ('6','gallery','gallery','a:10:{s:10:"icon_width";i:400;s:11:"icon_height";i:300;s:11:"image_width";i:800;s:10:"img_typies";a:2:{i:0;s:3:"jpg";i:1;s:4:"jpeg";}s:4:"rows";i:3;s:4:"cols";i:4;s:4:"sort";i:1;s:8:"can_view";a:3:{i:0;i:-1;i:1;i:0;i:2;i:1;}s:9:"can_write";a:1:{i:0;i:1;}s:10:"can_config";a:1:{i:0;i:1;}}');
INSERT INTO `{prefix}_modules` (`id`, `owner`, `module`, `config`) VALUES ('8','video','video','');
INSERT INTO `{prefix}_tags` (`id`, `tag`, `count`) VALUES ('1','GCMS','1');
INSERT INTO `{prefix}_textlink` (`id`, `text`, `url`, `publish_start`, `publish_end`, `logo`, `width`, `height`, `type`, `name`, `published`, `link_order`, `last_preview`, `description`, `template`, `target`) VALUES ('1','Goragod.com','http://www.goragod.com/','0','0','','0','0','list','list','1','0','0','','','');