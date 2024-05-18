CREATE TABLE `cron__log`
(
    `time`      DATETIME(6)                                  DEFAULT CURRENT_TIMESTAMP(6) NOT NULL COMMENT 'Time the error occurred',
    `type`      VARCHAR(30) COLLATE utf8mb4_unicode_nopad_ci DEFAULT 'Status'             NOT NULL COMMENT 'Event type',
    `runby`     VARCHAR(30) COLLATE utf8mb4_unicode_nopad_ci                              NULL COMMENT 'Indicates process that was running a task',
    `sse`       TINYINT(1) UNSIGNED                          DEFAULT 0                    NOT NULL COMMENT 'Flag to indicate whether task was being ran by SSE call',
    `task`      VARCHAR(100) COLLATE utf8mb4_unicode_nopad_ci                             NULL COMMENT 'Optional task ID',
    `arguments` VARCHAR(255) COLLATE utf8mb4_unicode_nopad_ci                             NULL COMMENT 'Optional task arguments',
    `instance`  INT UNSIGNED                                                              NULL COMMENT 'Instance number of the task',
    `message`   TEXT COLLATE utf8mb4_unicode_nopad_ci                                     NOT NULL COMMENT 'Error for the text',
    CONSTRAINT `errors_to_tasks` FOREIGN KEY (`task`) REFERENCES `cron__tasks` (`task`) ON UPDATE CASCADE ON DELETE CASCADE
) COLLATE = utf8mb4_unicode_ci `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `time` ON `cron__log` (`time`);
