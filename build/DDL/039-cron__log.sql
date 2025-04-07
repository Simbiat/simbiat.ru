USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `cron__log` (
  `time` datetime(6) NOT NULL DEFAULT current_timestamp(6) COMMENT 'Time the error occurred',
  `type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_nopad_ci NOT NULL DEFAULT 'Status' COMMENT 'Event type',
  `runby` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_nopad_ci DEFAULT NULL COMMENT 'Indicates process that was running a task',
  `sse` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Flag to indicate whether task was being ran by SSE call',
  `task` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_nopad_ci DEFAULT NULL COMMENT 'Optional task ID',
  `arguments` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_nopad_ci DEFAULT NULL COMMENT 'Optional task arguments',
  `instance` int(10) unsigned DEFAULT NULL COMMENT 'Instance number of the task',
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_nopad_ci NOT NULL COMMENT 'Message provided by the event',
  KEY `time` (`time`),
  KEY `time_desc` (`time` DESC) USING BTREE,
  KEY `type` (`type`) USING BTREE,
  KEY `runby` (`runby`) USING BTREE,
  KEY `task` (`task`) USING BTREE,
  CONSTRAINT `cron_log_to_event_type` FOREIGN KEY (`type`) REFERENCES `cron__event_types` (`type`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cron_log_to_tasks` FOREIGN KEY (`task`) REFERENCES `cron__tasks` (`task`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC `PAGE_COMPRESSED`='ON';