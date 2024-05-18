CREATE TABLE `cron__tasks`
(
    `task`           VARCHAR(100) COLLATE utf8mb4_unicode_nopad_ci  NOT NULL COMMENT 'Function''s internal ID' PRIMARY KEY,
    `function`       VARCHAR(255) COLLATE utf8mb4_unicode_nopad_ci  NOT NULL COMMENT 'Actual function reference, that will be called by Cron processor',
    `object`         VARCHAR(255) COLLATE utf8mb4_unicode_nopad_ci  NULL COMMENT 'Optional object',
    `parameters`     VARCHAR(5000) COLLATE utf8mb4_unicode_nopad_ci NULL COMMENT 'Optional parameters used on initial object creation in JSON string',
    `allowedreturns` VARCHAR(5000) COLLATE utf8mb4_unicode_nopad_ci NULL COMMENT 'Optional allowed return values to be treated as ''true'' by Cron processor in JSON string',
    `maxTime`        INT UNSIGNED        DEFAULT 3600               NOT NULL COMMENT 'Maximum time allowed for the task to run. If exceeded, it will be terminated by PHP.',
    `system`         TINYINT(1) UNSIGNED DEFAULT 0                  NOT NULL COMMENT 'Flag indicating that task is system and can''t be deleted from Cron\Task class',
    `description`    VARCHAR(1000) COLLATE utf8mb4_unicode_nopad_ci NULL COMMENT 'Description of the task'
) COLLATE = utf8mb4_unicode_ci `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
