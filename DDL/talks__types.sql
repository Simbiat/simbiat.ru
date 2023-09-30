CREATE TABLE `talks__types`
(
    `typeid`      TINYINT UNSIGNED AUTO_INCREMENT COMMENT 'Unique ID' PRIMARY KEY,
    `type`        VARCHAR(25) COLLATE utf8mb4_uca1400_nopad_ai_ci  NOT NULL COMMENT 'Type name',
    `description` VARCHAR(100) COLLATE utf8mb4_uca1400_nopad_ai_ci NULL COMMENT 'Description of the type',
    `icon`        VARCHAR(128) COLLATE utf8mb4_uca1400_nopad_as_ci NULL COMMENT 'Name of the default icon file',
    CONSTRAINT `section_type_to_file` FOREIGN KEY (`icon`) REFERENCES `sys__files` (`fileid`)
) COMMENT 'Types of forums' `PAGE_COMPRESSED` = 'ON';
