CREATE TABLE `talks__threads`
(
    `threadid`   INT UNSIGNED AUTO_INCREMENT COMMENT 'Thread ID' PRIMARY KEY,
    `name`       VARCHAR(70)                                     NOT NULL COMMENT 'Thread name',
    `sectionid`  INT UNSIGNED                                    NOT NULL COMMENT 'Forum ID where the thread is located',
    `language`   VARCHAR(35)         DEFAULT 'en'                NOT NULL COMMENT 'Main language of the thread',
    `system`     TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Flag indicating that thread is system one, thus should not be deleted.',
    `pinned`     TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Flag to indicate if a thread needs to be shown above others in the list',
    `closed`     TIMESTAMP                                       NULL COMMENT 'Flag to indicate if a thread is closed',
    `private`    TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Flag to indicate if thread is private',
    `ogimage`    VARCHAR(128)                                    NULL COMMENT 'Optional file ID to be used as og:image',
    `created`    TIMESTAMP           DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'When thread was created',
    `createdby`  INT UNSIGNED        DEFAULT 1                   NOT NULL COMMENT 'User ID of the creator',
    `updated`    TIMESTAMP           DEFAULT CURRENT_TIMESTAMP() NOT NULL ON UPDATE CURRENT_TIMESTAMP() COMMENT 'When thread was updated',
    `updatedby`  INT UNSIGNED        DEFAULT 1                   NOT NULL COMMENT 'User ID of the updater',
    `lastpost`   TIMESTAMP           DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'Time of the last post',
    `lastpostby` INT UNSIGNED        DEFAULT 1                   NOT NULL COMMENT 'ID of the last poster',
    CONSTRAINT `ogimage_to_fileid` FOREIGN KEY (`ogimage`) REFERENCES `sys__files` (`fileid`) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT `thread_created_by` FOREIGN KEY (`createdby`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE,
    CONSTRAINT `thread_language` FOREIGN KEY (`language`) REFERENCES `sys__languages` (`tag`) ON UPDATE CASCADE,
    CONSTRAINT `thread_lastpost_by` FOREIGN KEY (`lastpostby`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE,
    CONSTRAINT `thread_to_forum` FOREIGN KEY (`sectionid`) REFERENCES `talks__sections` (`sectionid`) ON UPDATE CASCADE,
    CONSTRAINT `thread_updated_by` FOREIGN KEY (`updatedby`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE
) COMMENT 'List of threads' `PAGE_COMPRESSED` = 'ON';

CREATE INDEX `closed_desc` ON `talks__threads` (`closed` DESC);

CREATE INDEX `created_desc` ON `talks__threads` (`created` DESC);

CREATE INDEX `lastpost_desc` ON `talks__threads` (`lastpost` DESC);

CREATE FULLTEXT INDEX `name` ON `talks__threads` (`name`);

CREATE INDEX `name_sort` ON `talks__threads` (`name`);

CREATE INDEX `pinned` ON `talks__threads` (`pinned` DESC);
