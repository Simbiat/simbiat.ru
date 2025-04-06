CREATE TABLE IF NOT EXISTS `uc__user_to_permission` (
  `userid` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'User ID',
  `permission` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Short name of permission, used as an ID',
  PRIMARY KEY (`userid`,`permission`),
  KEY `permission_to_permission_user` (`permission`),
  CONSTRAINT `permission_to_permission_user` FOREIGN KEY (`permission`) REFERENCES `uc__permissions` (`permission`),
  CONSTRAINT `permission_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs COMMENT='Permissions assigned to users directly' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;