USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `uc__user_to_permission` (
  `user_id` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'User ID',
  `permission` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Short name of permission, used as an ID',
  PRIMARY KEY (`user_id`,`permission`),
  KEY `permission_to_permission_user` (`permission`),
  CONSTRAINT `permission_to_permission_user` FOREIGN KEY (`permission`) REFERENCES `uc__permissions` (`permission`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `permission_to_user` FOREIGN KEY (`user_id`) REFERENCES `uc__users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='Permissions assigned to users directly' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;