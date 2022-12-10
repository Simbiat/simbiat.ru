CREATE TABLE `sys__files`
(
    `fileid`    VARCHAR(128)                                NOT NULL COMMENT 'File hash, which is also its ID. Also used as file name on file system.' PRIMARY KEY,
    `userid`    INT UNSIGNED                                NOT NULL COMMENT 'ID of the uploader',
    `name`      VARCHAR(128)                                NOT NULL COMMENT 'Name of the file to be shown to humans',
    `extension` VARCHAR(25)                                 NOT NULL COMMENT 'File extension',
    `mime`      VARCHAR(100)                                NOT NULL COMMENT 'MIME Type',
    `size`      BIGINT UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Size of the file in bytes',
    `added`     TIMESTAMP       DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'When file was added',
    CONSTRAINT `file_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE
) COMMENT 'List of file attachments';

CREATE INDEX `added` ON `sys__files` (`added`);

CREATE INDEX `mime` ON `sys__files` (`mime`);

CREATE INDEX `size` ON `sys__files` (`size` DESC);

CREATE INDEX `userid` ON `sys__files` (`userid`);
