USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `uc__user_to_group` (
  `user_id` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'User ID',
  `group_id` int(10) unsigned NOT NULL DEFAULT 2 COMMENT 'Group ID',
  PRIMARY KEY (`user_id`,`group_id`),
  KEY `groupid` (`group_id`),
  CONSTRAINT `group_to_user` FOREIGN KEY (`user_id`) REFERENCES `uc__users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `groupid` FOREIGN KEY (`group_id`) REFERENCES `uc__groups` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC `PAGE_COMPRESSED`='ON';