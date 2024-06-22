CREATE TABLE `cron__settings` (
  `setting` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_nopad_ci NOT NULL COMMENT 'Name of the setting',
  `value` int(10) DEFAULT NULL COMMENT 'Value of the setting',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_nopad_ci DEFAULT NULL COMMENT 'Description of the setting',
  PRIMARY KEY (`setting`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC `PAGE_COMPRESSED`='ON';