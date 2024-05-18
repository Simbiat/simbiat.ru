CREATE TABLE `cron__schedule`
(
    `task`        VARCHAR(100) COLLATE utf8mb4_unicode_nopad_ci    NOT NULL COMMENT 'Task ID',
    `arguments`   VARCHAR(255) COLLATE utf8mb4_unicode_nopad_ci    NOT NULL COMMENT 'Optional arguments in JSON string',
    `instance`    INT UNSIGNED        DEFAULT 1                    NOT NULL COMMENT 'Instance number of the task',
    `system`      TINYINT(1) UNSIGNED DEFAULT 0                    NOT NULL COMMENT 'Flag indicating whether a task instance is system one and can''t be deleted from Cron\Schedule class',
    `frequency`   INT UNSIGNED        DEFAULT 0                    NOT NULL COMMENT 'Frequency to run a task in seconds',
    `dayofmonth`  VARCHAR(255) COLLATE utf8mb4_unicode_nopad_ci    NULL COMMENT 'Optional limit to run only on specific days of the month. Expects array of integers in JSON string.',
    `dayofweek`   VARCHAR(60) COLLATE utf8mb4_unicode_nopad_ci     NULL COMMENT 'Optional limit to run only on specific days of the week. Expects array of integers in JSON string.',
    `priority`    TINYINT UNSIGNED    DEFAULT 0                    NOT NULL COMMENT 'Priority of the task',
    `message`     VARCHAR(100) COLLATE utf8mb4_unicode_nopad_ci    NULL COMMENT 'Optional message, that will be shown if launched outside of CLI',
    `status`      TINYINT(1) UNSIGNED DEFAULT 0                    NOT NULL COMMENT 'Flag showing whether the task is running or not',
    `runby`       VARCHAR(30) COLLATE utf8mb4_unicode_nopad_ci     NULL COMMENT 'If not NULL, indicates, that a task is queued for a run by a process.',
    `sse`         TINYINT(1) UNSIGNED DEFAULT 0                    NOT NULL COMMENT 'Flag to indicate whether task is being ran by SSE call.',
    `registered`  DATETIME(6)         DEFAULT CURRENT_TIMESTAMP(6) NOT NULL COMMENT 'When the task was initially registered.',
    `updated`     DATETIME(6)         DEFAULT CURRENT_TIMESTAMP(6) NOT NULL COMMENT 'When the task schedule was updated.',
    `nextrun`     DATETIME(6)         DEFAULT CURRENT_TIMESTAMP(6) NOT NULL COMMENT 'Next expected time for the task to be run.',
    `lastrun`     DATETIME(6)                                      NULL COMMENT 'Time of the last run attempt',
    `lastsuccess` DATETIME(6)                                      NULL COMMENT 'Time of the last successful run',
    `lasterror`   DATETIME(6)                                      NULL COMMENT 'Time of the last error',
    PRIMARY KEY (`task`, `arguments`, `instance`),
    CONSTRAINT `schedule_to_task` FOREIGN KEY (`task`) REFERENCES `cron__tasks` (`task`) ON UPDATE CASCADE ON DELETE CASCADE
) COLLATE = utf8mb4_unicode_ci `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `arguments` ON `cron__schedule` (`arguments`);

CREATE INDEX `lastrun` ON `cron__schedule` (`lastrun`);

CREATE INDEX `nextrun` ON `cron__schedule` (`nextrun`);

CREATE INDEX `priority` ON `cron__schedule` (`priority`);

CREATE INDEX `runby` ON `cron__schedule` (`runby`);

CREATE INDEX `status` ON `cron__schedule` (`status`);
