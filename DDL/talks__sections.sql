CREATE TABLE `talks__sections`
(
    `sectionid`   INT UNSIGNED AUTO_INCREMENT COMMENT 'Forum ID' PRIMARY KEY,
    `name`        VARCHAR(64)                                     NOT NULL COMMENT 'Forum name',
    `description` VARCHAR(100)                                    NULL COMMENT 'Optional description of the forum',
    `parentid`    INT UNSIGNED                                    NULL COMMENT 'ID of the parent forum',
    `sequence`    INT(2) UNSIGNED     DEFAULT 0                   NOT NULL COMMENT 'Optional order for sorting. The higher the value, the higher in the list a forum will be. After that name sorting is expected.',
    `type`        TINYINT UNSIGNED    DEFAULT 1                   NOT NULL COMMENT 'Type of the forum',
    `system`      TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Flag indicating that forum is system one, thus should not be deleted.',
    `closed`      TIMESTAMP                                       NULL COMMENT 'Flag indicating if the forum is closed',
    `private`     TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Flag indicating if forum is private',
    `created`     TIMESTAMP           DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'When forum was created',
    `createdby`   INT UNSIGNED        DEFAULT 1                   NOT NULL COMMENT 'User ID of the creator',
    `updated`     TIMESTAMP           DEFAULT CURRENT_TIMESTAMP() NOT NULL ON UPDATE CURRENT_TIMESTAMP() COMMENT 'When forum was updated',
    `updatedby`   INT UNSIGNED        DEFAULT 1                   NOT NULL COMMENT 'User ID of the last updater',
    `icon`        VARCHAR(128)                                    NULL COMMENT 'Icon override',
    CONSTRAINT `forum_created_by` FOREIGN KEY (`createdby`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE,
    CONSTRAINT `forum_to_forum` FOREIGN KEY (`parentid`) REFERENCES `talks__sections` (`sectionid`) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT `forum_to_type` FOREIGN KEY (`type`) REFERENCES `talks__types` (`typeid`) ON UPDATE CASCADE,
    CONSTRAINT `forum_updated_by` FOREIGN KEY (`updatedby`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE,
    CONSTRAINT `section_to_file` FOREIGN KEY (`icon`) REFERENCES `sys__files` (`fileid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'List of forums' ENGINE = `InnoDB` `PAGE_COMPRESSED` = 'ON';

CREATE FULLTEXT INDEX `description` ON `talks__sections` (`description`);

CREATE FULLTEXT INDEX `name` ON `talks__sections` (`name`);

CREATE INDEX `name_sort` ON `talks__sections` (`name`);

CREATE INDEX `sequence` ON `talks__sections` (`sequence` DESC);
