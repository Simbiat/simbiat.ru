USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `cron__event_types` (
  `type` varchar(30) NOT NULL COMMENT 'Type of the event',
  `description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Description of the event',
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='Different event types for logging and SSE output' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;