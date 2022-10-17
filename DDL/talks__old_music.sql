CREATE TABLE `talks__old_music`
(
    `fileid`    INT UNSIGNED AUTO_INCREMENT COMMENT 'File ID',
    `postid`    INT UNSIGNED                                NOT NULL COMMENT 'ID of the post to which the file is attached',
    `name`      VARCHAR(128)                                NOT NULL COMMENT 'Name of the file to be shown to humans',
    `mime`      VARCHAR(100)                                NOT NULL COMMENT 'MIME Type',
    `size`      BIGINT UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Size of the file in bytes',
    `hash`      VARCHAR(256)                                NOT NULL COMMENT 'File hash. Also used as file name on file system.',
    `added`     TIMESTAMP       DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'When file was added',
    `downloads` INT UNSIGNED    DEFAULT 0                   NOT NULL COMMENT 'Number of downloads',
    CONSTRAINT `fileid` UNIQUE (`fileid`),
    CONSTRAINT `hash` UNIQUE (`hash`),
    CONSTRAINT `file_to_post` FOREIGN KEY (`postid`) REFERENCES `talks__posts` (`postid`) ON UPDATE CASCADE
) COMMENT 'List of file attachments';

CREATE INDEX `mime` ON `talks__old_music` (`mime`);
