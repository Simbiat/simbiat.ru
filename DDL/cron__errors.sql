CREATE TABLE `cron__errors`
(
    `task`      VARCHAR(100) DEFAULT ''''''              NOT NULL COMMENT 'Optional task ID',
    `arguments` VARCHAR(255) DEFAULT ''''''              NOT NULL COMMENT 'Optional task arguments',
    `time`      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP() NOT NULL ON UPDATE CURRENT_TIMESTAMP() COMMENT 'Time the error occured',
    `text`      TEXT                                     NOT NULL COMMENT 'Error for the text',
    PRIMARY KEY (`task`, `arguments`),
    CONSTRAINT `errors_to_arguments` FOREIGN KEY (`arguments`) REFERENCES `cron__schedule` (`arguments`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `errors_to_tasks` FOREIGN KEY (`task`) REFERENCES `cron__tasks` (`task`) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE INDEX `time` ON `cron__errors` (`time`);
