CREATE TABLE IF NOT EXISTS `uc__user_to_group` (
  `userid` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'User ID',
  `groupid` int(10) unsigned NOT NULL DEFAULT 2 COMMENT 'Group ID',
  PRIMARY KEY (`userid`,`groupid`),
  KEY `groupid` (`groupid`),
  CONSTRAINT `group_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `groupid` FOREIGN KEY (`groupid`) REFERENCES `uc__groups` (`groupid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC `PAGE_COMPRESSED`='ON';