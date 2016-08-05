ALTER TABLE `school_user` ADD `ban` INT( 11 ) UNSIGNED NOT NULL ;
ALTER TABLE `school_eventcalendar` ADD `end_date` DATETIME NOT NULL AFTER `begin_date` ;
