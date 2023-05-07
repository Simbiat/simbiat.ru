CREATE TABLE `sys__settings`
(
    `setting`     VARCHAR(100)  NOT NULL PRIMARY KEY,
    `value`       VARCHAR(5000) NULL,
    `description` TEXT          NULL
) `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
