USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `uc__avatars` (
  `userid` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'User ID',
  `fileid` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'File hash, which is also its ID. Also used as file name on file system.',
  `characterid` int(10) unsigned DEFAULT NULL COMMENT 'Character ID, if avatar is linked to FFXIV character',
  `current` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Flag to show if this avatar is the current one',
  PRIMARY KEY (`userid`,`fileid`),
  KEY `avatar_to_ffxiv` (`characterid`),
  KEY `avatar_to_file` (`fileid`),
  CONSTRAINT `avatar_to_ffxiv` FOREIGN KEY (`characterid`) REFERENCES `ffxiv__character` (`characterid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `avatar_to_file` FOREIGN KEY (`fileid`) REFERENCES `sys__files` (`fileid`),
  CONSTRAINT `avatar_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC `PAGE_COMPRESSED`='ON';