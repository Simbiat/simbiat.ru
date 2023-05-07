CREATE TABLE `bic__settings`
(
    `setting`     VARCHAR(10)  NOT NULL COMMENT 'Name of the setting' PRIMARY KEY,
    `value`       VARCHAR(100) NOT NULL COMMENT 'Value of the setting',
    `description` VARCHAR(250) NULL COMMENT 'Optional description of the setting'
) COMMENT 'List of settings for BIC Tracker' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
