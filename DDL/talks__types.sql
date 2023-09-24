CREATE TABLE `talks__types`
(
    `typeid`      TINYINT UNSIGNED AUTO_INCREMENT COMMENT 'Unique ID' PRIMARY KEY,
    `type`        VARCHAR(25)  NOT NULL COMMENT 'Type name',
    `description` VARCHAR(100) NULL COMMENT 'Description of the type',
    `icon`        VARCHAR(128) NULL COMMENT 'Name of the default icon file',
    CONSTRAINT `section_type_to_file` FOREIGN KEY (`icon`) REFERENCES `sys__files` (`fileid`) ON UPDATE CASCADE
) COMMENT 'Types of forums' ENGINE = `InnoDB` `PAGE_COMPRESSED` = 'ON';
