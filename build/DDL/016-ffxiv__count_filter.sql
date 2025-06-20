USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__count_filter` (
  `count_id` tinyint(1) unsigned NOT NULL COMMENT 'ID of filter by members count for groups'' search on Lodestone',
  `value` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Value that is used Lodestone when filtering',
  PRIMARY KEY (`count_id`),
  UNIQUE KEY `value` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='Filters by members count for groups'' search on Lodestone' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;