USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `sys__logs` (
  `time` datetime(6) NOT NULL DEFAULT current_timestamp(6) COMMENT 'When action occurred',
  `type` tinyint(3) unsigned NOT NULL COMMENT 'Type of the action',
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Short description of the action',
  `user_id` int(10) unsigned NOT NULL COMMENT 'Optional user ID, if available',
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci DEFAULT NULL COMMENT 'IP, if available',
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Full useragent, if available',
  `extra` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Any extra information available for the entry',
  KEY `audit_userid` (`user_id`),
  KEY `log_type` (`type`),
  KEY `time` (`time` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Table storing logs' `PAGE_COMPRESSED`='ON';