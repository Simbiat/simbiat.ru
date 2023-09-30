CREATE TABLE `sys__files`
(
    `fileid`    VARCHAR(128) COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'File hash, which is also its ID. Also used as file name on file system.' PRIMARY KEY,
    `userid`    INT UNSIGNED    DEFAULT 1                        NOT NULL COMMENT 'ID of the uploader',
    `name`      VARCHAR(128) COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Name of the file to be shown to humans',
    `extension` VARCHAR(25) COLLATE utf8mb4_uca1400_nopad_as_ci  NOT NULL COMMENT 'File extension',
    `mime`      VARCHAR(100) COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'MIME Type',
    `size`      BIGINT UNSIGNED DEFAULT 0                        NOT NULL COMMENT 'Size of the file in bytes',
    `added`     DATETIME(6)     DEFAULT CURRENT_TIMESTAMP(6)     NOT NULL COMMENT 'When file was added',
    CONSTRAINT `file_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE
) COMMENT 'List of file attachments' `PAGE_COMPRESSED` = 'ON';

CREATE INDEX `added` ON `sys__files` (`added`);

CREATE INDEX `mime` ON `sys__files` (`mime`);

CREATE INDEX `size` ON `sys__files` (`size` DESC);

CREATE INDEX `userid` ON `sys__files` (`userid`);
