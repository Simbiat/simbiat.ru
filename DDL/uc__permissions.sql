CREATE TABLE `uc__permissions` (
  `permission` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Short name of permission, used as an ID',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Description of the permission',
  PRIMARY KEY (`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs COMMENT='List of permissions' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;