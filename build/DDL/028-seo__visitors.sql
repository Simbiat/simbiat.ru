USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `seo__visitors` (
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'IP of unique visitor',
  `os` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'OS version used by visitor',
  `client` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Client version used by visitor',
  `first` datetime(6) NOT NULL DEFAULT current_timestamp(6) COMMENT 'Time of first visit',
  `last` datetime(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6) COMMENT 'Time of last visit',
  `views` bigint(20) unsigned NOT NULL DEFAULT 1 COMMENT 'Number of viewed pages',
  PRIMARY KEY (`ip`,`os`,`client`) USING BTREE,
  KEY `first` (`first`),
  KEY `os` (`os`),
  KEY `client` (`client`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Views statistics per user' `PAGE_COMPRESSED`='ON';