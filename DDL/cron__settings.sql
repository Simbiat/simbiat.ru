CREATE TABLE `cron__settings`
(
    `setting`     VARCHAR(10) NOT NULL COMMENT 'Name of the setting' PRIMARY KEY,
    `value`       INT(10)     NULL COMMENT 'Value of the setting',
    `description` TEXT        NULL COMMENT 'Description of the setting'
) ENGINE = `InnoDB` `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
