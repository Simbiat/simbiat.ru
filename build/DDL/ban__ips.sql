CREATE TABLE `ban__ips` (
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Banned IP',
  `added` datetime(6) NOT NULL DEFAULT current_timestamp(6) COMMENT 'When IP was banned',
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci DEFAULT NULL COMMENT 'Reason for the ban',
  PRIMARY KEY (`ip`),
  KEY `added` (`added`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Banned IPs' `PAGE_COMPRESSED`='ON';