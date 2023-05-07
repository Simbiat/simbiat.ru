CREATE TABLE `talks__likes`
(
    `postid`    INT UNSIGNED           NOT NULL COMMENT 'Post ID',
    `userid`    INT UNSIGNED DEFAULT 1 NOT NULL COMMENT 'User ID',
    `likevalue` TINYINT(1)   DEFAULT 1 NOT NULL COMMENT '"Value" of the like. Negative values are counted as a "dislike", and positive values as a "like"',
    PRIMARY KEY (`postid`, `userid`),
    CONSTRAINT `likes_to_post` FOREIGN KEY (`postid`) REFERENCES `talks__posts` (`postid`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `likes_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Table for (dis)likes of posts' `PAGE_COMPRESSED` = 'ON';
