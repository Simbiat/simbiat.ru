CREATE TABLE `talks__posts`
(
    `postid`    INT UNSIGNED AUTO_INCREMENT COMMENT 'Post ID' PRIMARY KEY,
    `threadid`  INT UNSIGNED                                    NOT NULL COMMENT 'ID of a thread the post belongs to',
    `replyto`   INT UNSIGNED                                    NULL COMMENT 'Indicates an ID of a post, that this is a reply to (required to build chains)',
    `system`    TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Flag indicating that post is system one, thus should not be deleted.',
    `locked`    TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Flag to indicate if post is locked from editing',
    `created`   TIMESTAMP           DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'When post was created',
    `createdby` INT UNSIGNED        DEFAULT 1                   NOT NULL COMMENT 'User ID of the creator',
    `updated`   TIMESTAMP           DEFAULT CURRENT_TIMESTAMP() NOT NULL ON UPDATE CURRENT_TIMESTAMP() COMMENT 'When post was updated',
    `updatedby` INT UNSIGNED        DEFAULT 1                   NOT NULL COMMENT 'User ID of the last updater',
    `text`      LONGTEXT                                        NOT NULL COMMENT 'Text of the post',
    CONSTRAINT `post_created_by` FOREIGN KEY (`createdby`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE,
    CONSTRAINT `post_to_post` FOREIGN KEY (`replyto`) REFERENCES `talks__posts` (`postid`) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT `post_to_thread` FOREIGN KEY (`threadid`) REFERENCES `talks__threads` (`threadid`) ON UPDATE CASCADE,
    CONSTRAINT `post_updated_by` FOREIGN KEY (`updatedby`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE
) COMMENT 'List of all posts' `PAGE_COMPRESSED` = 'ON';

CREATE INDEX `created_asc` ON `talks__posts` (`created`);

CREATE INDEX `created_desc` ON `talks__posts` (`created` DESC);

CREATE FULLTEXT INDEX `text` ON `talks__posts` (`text`);
