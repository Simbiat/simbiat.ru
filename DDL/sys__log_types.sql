CREATE TABLE `sys__log_types`
(
    `typeid` TINYINT(2) UNSIGNED AUTO_INCREMENT COMMENT 'Type ID' PRIMARY KEY,
    `name`   VARCHAR(100) NOT NULL COMMENT 'Name of the type',
    CONSTRAINT `name` UNIQUE (`name`)
) COMMENT 'Definitions of types of logs';
