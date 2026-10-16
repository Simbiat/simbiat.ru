USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__server` (
  `server_id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Server ID as registered by the tracker',
  `server` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Server name',
  `data_center` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Data center name',
  PRIMARY KEY (`server_id`),
  UNIQUE KEY `server` (`server`,`data_center`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='List of servers/data centers' `PAGE_COMPRESSED`='ON';