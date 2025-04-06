CREATE TABLE IF NOT EXISTS `sys__logs` (
  `time` datetime(6) NOT NULL DEFAULT current_timestamp(6) COMMENT 'When action occurred',
  `type` tinyint(2) unsigned NOT NULL COMMENT 'Type of the action',
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Short description of the action',
  `userid` int(10) unsigned NOT NULL COMMENT 'Optional user ID, if available',
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci DEFAULT NULL COMMENT 'IP, if available',
  `useragent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci DEFAULT NULL COMMENT 'Full useragent, if available',
  `extra` text CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci DEFAULT NULL COMMENT 'Any extra information available for the entry',
  KEY `audit_userid` (`userid`),
  KEY `log_type` (`type`),
  KEY `time` (`time` DESC),
  CONSTRAINT `audit_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `log_type` FOREIGN KEY (`type`) REFERENCES `sys__log_types` (`typeid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Table storing logs' `PAGE_COMPRESSED`='ON';