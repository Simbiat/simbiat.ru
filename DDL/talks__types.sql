CREATE TABLE `talks__types`
(
    `typeid`      TINYINT UNSIGNED AUTO_INCREMENT COMMENT 'Unique ID' PRIMARY KEY,
    `type`        VARCHAR(25)  NOT NULL COMMENT 'Type name',
    `description` VARCHAR(100) NULL COMMENT 'Description of the type',
    `icon`        VARCHAR(50)  NULL COMMENT 'Name of the default icon file'
) COMMENT 'Types of forums';
