CREATE TABLE `talks__likes`
(
    `postid`    INT UNSIGNED                  NOT NULL COMMENT 'Post ID',
    `userid`    INT UNSIGNED                  NOT NULL COMMENT 'User ID',
    `likevalue` TINYINT(1) UNSIGNED DEFAULT 1 NOT NULL COMMENT '"Value" of the like. 0 (false) means "dislike", anything else (true) means "like"',
    PRIMARY KEY (`postid`, `userid`)
) COMMENT 'Table for (dis)likes of posts';
