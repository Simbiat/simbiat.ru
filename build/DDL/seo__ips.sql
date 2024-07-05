CREATE TABLE `seo__ips` (
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'IP address',
  `country` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Country name',
  `city` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'City name',
  PRIMARY KEY (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs COMMENT='IP to country and city based on ipinfo.io' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;