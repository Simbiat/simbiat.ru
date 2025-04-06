CREATE TABLE IF NOT EXISTS `uc__group_to_permission` (
  `groupid` int(10) unsigned NOT NULL COMMENT 'ID of the group',
  `permission` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Short name of permission, used as an ID',
  PRIMARY KEY (`groupid`,`permission`),
  KEY `permission_to_permission_group` (`permission`),
  CONSTRAINT `permission_to_group` FOREIGN KEY (`groupid`) REFERENCES `uc__groups` (`groupid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `permission_to_permission_group` FOREIGN KEY (`permission`) REFERENCES `uc__permissions` (`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs COMMENT='List of permissions assigned to user groups' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;