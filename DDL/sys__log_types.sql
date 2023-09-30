CREATE TABLE `sys__log_types`
(
    `typeid` TINYINT(2) UNSIGNED AUTO_INCREMENT COMMENT 'Type ID' PRIMARY KEY,
    `name`   VARCHAR(100) COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Name of the type',
    CONSTRAINT `name` UNIQUE (`name`)
) COMMENT 'Definitions of types of logs' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
