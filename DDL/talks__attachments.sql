CREATE TABLE `talks__attachments`
(
    `postid` INT UNSIGNED                  NOT NULL COMMENT 'Post ID',
    `fileid` VARCHAR(128)                  NOT NULL COMMENT 'File hash, which is also its ID. Also used as file name on file system.',
    `inline` TINYINT(1) UNSIGNED DEFAULT 0 NOT NULL COMMENT 'Flag indicating inline file (mainly images inserted through editor)',
    PRIMARY KEY (`postid`, `fileid`),
    CONSTRAINT `attachment_to_file` FOREIGN KEY (`fileid`) REFERENCES `sys__files` (`fileid`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `attachment_to_post` FOREIGN KEY (`postid`) REFERENCES `talks__posts` (`postid`) ON UPDATE CASCADE ON DELETE CASCADE
);
