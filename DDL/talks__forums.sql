CREATE TABLE `talks__forums`
(
    `forumid`   INT UNSIGNED AUTO_INCREMENT COMMENT 'Forum ID' PRIMARY KEY,
    `name`      VARCHAR(50)                                     NOT NULL COMMENT 'Forum name',
    `parentid`  INT UNSIGNED                                    NULL COMMENT 'ID of the parent forum',
    `type`      TINYINT UNSIGNED    DEFAULT 1                   NOT NULL COMMENT 'Type of the forum',
    `system`    TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Flag indicating that forum is system one, thus should not be deleted.',
    `closed`    TIMESTAMP                                       NULL COMMENT 'Flag indicating if the forum is closed',
    `private`   TINYINT(1) UNSIGNED DEFAULT 1                   NOT NULL COMMENT 'Flag indicating if forum is private',
    `created`   TIMESTAMP           DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'When forum was created',
    `createdby` INT UNSIGNED                                    NULL COMMENT 'User ID of the creator',
    `updated`   TIMESTAMP           DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'When forum was updated',
    `updatedby` INT UNSIGNED                                    NULL COMMENT 'User ID of the last updater',
    `icon`      VARCHAR(50)                                     NULL COMMENT 'Icon override',
    CONSTRAINT `forum_created_by` FOREIGN KEY (`createdby`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT `forum_to_forum` FOREIGN KEY (`parentid`) REFERENCES `talks__forums` (`forumid`) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT `forum_updated_by` FOREIGN KEY (`updatedby`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE ON DELETE SET NULL
) COMMENT 'List of forums';

CREATE FULLTEXT INDEX `name` ON `talks__forums` (`name`);
