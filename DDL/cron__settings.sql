CREATE TABLE `cron__settings`
(
    `setting`     VARCHAR(10) COLLATE utf8mb4_unicode_nopad_ci NOT NULL COMMENT 'Name of the setting' PRIMARY KEY,
    `value`       INT(10)                                      NULL COMMENT 'Value of the setting',
    `description` TEXT COLLATE utf8mb4_unicode_nopad_ci        NULL COMMENT 'Description of the setting'
) COLLATE = utf8mb4_unicode_ci `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
