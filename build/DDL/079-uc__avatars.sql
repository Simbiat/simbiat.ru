USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `uc__avatars` (
  `user_id` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'User ID',
  `file_id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'File hash, which is also its ID. Also used as file name on file system.',
  `character_id` int(10) unsigned DEFAULT NULL COMMENT 'Character ID, if avatar is linked to FFXIV character',
  `current` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Flag to show if this avatar is the current one',
  PRIMARY KEY (`user_id`,`file_id`),
  KEY `avatar_to_ffxiv` (`character_id`),
  KEY `avatar_to_file` (`file_id`),
  CONSTRAINT `avatar_to_ffxiv` FOREIGN KEY (`character_id`) REFERENCES `ffxiv__character` (`character_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `avatar_to_file` FOREIGN KEY (`file_id`) REFERENCES `sys__files` (`file_id`),
  CONSTRAINT `avatar_to_user` FOREIGN KEY (`user_id`) REFERENCES `uc__users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC `PAGE_COMPRESSED`='ON';