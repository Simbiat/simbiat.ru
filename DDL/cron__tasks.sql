CREATE TABLE `cron__tasks`
(
    `task`           VARCHAR(100)              NOT NULL COMMENT 'Function''s internal ID' PRIMARY KEY,
    `function`       VARCHAR(255)              NOT NULL COMMENT 'Actual function reference, that will be called by Cron processor',
    `object`         VARCHAR(255)              NULL COMMENT 'Optional object',
    `parameters`     VARCHAR(5000)             NULL COMMENT 'Optional parameters used on initial object creation in JSON string',
    `allowedreturns` VARCHAR(5000)             NULL COMMENT 'Optional allowed return values to be treated as ''true'' by Cron processor in JSON string',
    `maxTime`        INT UNSIGNED DEFAULT 3600 NOT NULL COMMENT 'Maximum time allowed for the task to run. If exceeded, it will be terminated by PHP.',
    `description`    VARCHAR(1000)             NULL COMMENT 'Description of the task'
) ENGINE = `InnoDB` `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
