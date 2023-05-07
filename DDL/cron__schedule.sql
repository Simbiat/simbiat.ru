CREATE TABLE `cron__schedule`
(
    `task`        VARCHAR(100)                                    NOT NULL COMMENT 'Task ID',
    `arguments`   VARCHAR(255)        DEFAULT ''                  NOT NULL COMMENT 'Optional arguments in JSON string',
    `frequency`   INT UNSIGNED        DEFAULT 0                   NOT NULL COMMENT 'Frequency to run a task in seconds',
    `dayofmonth`  VARCHAR(255)                                    NULL COMMENT 'Optional limit to run only on specific days of the month. Expects array of integers in JSON string.',
    `dayofweek`   VARCHAR(60)                                     NULL COMMENT 'Optional limit to run only on specific days of the week. Expects array of integers in JSON string.',
    `priority`    TINYINT UNSIGNED    DEFAULT 0                   NOT NULL COMMENT 'Priority of the task',
    `message`     VARCHAR(100)                                    NULL COMMENT 'Optional message, that will be shown if launched outside of CLI',
    `status`      TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Flag showing whether the job is running or not',
    `runby`       VARCHAR(30)                                     NULL COMMENT 'If not NULL, indicates, that a job is queued for a run by a process.',
    `sse`         TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Flag to indicate whether job is being ran by SSE call.',
    `registered`  TIMESTAMP           DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'When the job was initially registered',
    `updated`     TIMESTAMP           DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'When the job schedule was updated',
    `nextrun`     TIMESTAMP           DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'Next expected time for the job to be run',
    `lastrun`     TIMESTAMP                                       NULL COMMENT 'Time of the last run attempt',
    `lastsuccess` TIMESTAMP                                       NULL COMMENT 'Time of the last successful run',
    `lasterror`   TIMESTAMP                                       NULL COMMENT 'Time of the last error',
    PRIMARY KEY (`task`, `arguments`),
    CONSTRAINT `schedule_to_task` FOREIGN KEY (`task`) REFERENCES `cron__tasks` (`task`) ON UPDATE CASCADE ON DELETE CASCADE
) `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `arguments` ON `cron__schedule` (`arguments`);

CREATE INDEX `lastrun` ON `cron__schedule` (`lastrun`);

CREATE INDEX `nextrun` ON `cron__schedule` (`nextrun`);

CREATE INDEX `priority` ON `cron__schedule` (`priority`);

CREATE INDEX `runby` ON `cron__schedule` (`runby`);

CREATE INDEX `status` ON `cron__schedule` (`status`);
