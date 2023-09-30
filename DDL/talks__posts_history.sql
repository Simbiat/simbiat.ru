CREATE TABLE `talks__posts_history`
(
    `postid` INT UNSIGNED                                 NOT NULL COMMENT 'Post ID',
    `time`   DATETIME(6)  DEFAULT CURRENT_TIMESTAMP(6)    NOT NULL COMMENT 'Time of the change',
    `userid` INT UNSIGNED DEFAULT 1                       NOT NULL COMMENT 'ID of the user, who edited text',
    `text`   LONGTEXT COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Text of the post',
    CONSTRAINT `history_to_post` FOREIGN KEY (`postid`) REFERENCES `talks__posts` (`postid`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `history_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE
) COMMENT 'Posts'' history' `PAGE_COMPRESSED` = 'ON';

CREATE INDEX `time` ON `talks__posts_history` (`time` DESC);
