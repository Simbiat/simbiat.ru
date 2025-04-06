CREATE TABLE IF NOT EXISTS `cron__event_types` (
  `type` varchar(30) NOT NULL COMMENT 'Type of the event',
  `description` varchar(100) NOT NULL COMMENT 'Description of the event',
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_nopad_ci COMMENT='Different event types for logging and SSE output' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;