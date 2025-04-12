USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `cron__tasks` (
  `task` varchar(100) NOT NULL COMMENT 'Function''s internal ID',
  `function` varchar(255) NOT NULL COMMENT 'Actual function reference, that will be called by Cron processor',
  `object` varchar(255) DEFAULT NULL COMMENT 'Optional object',
  `parameters` varchar(5000) DEFAULT NULL COMMENT 'Optional parameters used on initial object creation in JSON string',
  `allowedreturns` varchar(5000) DEFAULT NULL COMMENT 'Optional allowed return values to be treated as ''true'' by Cron processor in JSON string',
  `maxTime` int(10) unsigned NOT NULL DEFAULT 3600 COMMENT 'Maximum time allowed for the task to run. If exceeded, it will be terminated by PHP.',
  `minFrequency` int(10) unsigned NOT NULL DEFAULT 60 COMMENT 'Minimal allowed frequency (in seconds) at which a task instance can run. Does not apply to one-time jobs.',
  `retry` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Custom number of seconds to reschedule a failed task instance for. 0 disables the functionality.',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT 'Whether a task (and thus all its instances) is enabled and should be run as per schedule',
  `system` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Flag indicating that task is system and can''t be deleted from Cron\\Task class',
  `description` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Description of the task',
  PRIMARY KEY (`task`),
  KEY `enabled` (`enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC `PAGE_COMPRESSED`='ON';