CREATE TABLE `uc__avatars`
(
    `userid`      INT UNSIGNED        DEFAULT 1 NOT NULL COMMENT 'User ID',
    `fileid`      VARCHAR(128)                  NOT NULL COMMENT 'File hash, which is also its ID. Also used as file name on file system.',
    `characterid` INT UNSIGNED                  NULL COMMENT 'Character ID, if avatar is linked to FFXIV character',
    `current`     TINYINT(1) UNSIGNED DEFAULT 0 NOT NULL COMMENT 'Flag to show if this avatar is the current one',
    PRIMARY KEY (`userid`, `fileid`),
    CONSTRAINT `avatar_to_ffxiv` FOREIGN KEY (`characterid`) REFERENCES `ffxiv__character` (`characterid`) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT `avatar_to_file` FOREIGN KEY (`fileid`) REFERENCES `sys__files` (`fileid`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `avatar_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE ON DELETE CASCADE
);
