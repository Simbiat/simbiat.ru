CREATE TABLE `uc__sessions`
(
    `sessionid` VARCHAR(256)                                     NOT NULL COMMENT 'Session''s UID' PRIMARY KEY,
    `cookieid`  VARCHAR(256)                                     NULL COMMENT 'Cookie associated with the session',
    `time`      DATETIME(6)         DEFAULT CURRENT_TIMESTAMP(6) NOT NULL ON UPDATE CURRENT_TIMESTAMP(6) COMMENT 'Last time session was determined active',
    `userid`    INT UNSIGNED        DEFAULT 1                    NOT NULL COMMENT 'User ID',
    `bot`       TINYINT(1) UNSIGNED DEFAULT 0                    NOT NULL COMMENT 'Whether session was determined to be a bot',
    `ip`        VARCHAR(45) COLLATE utf8mb4_uca1400_nopad_as_ci  NULL COMMENT 'Session''s IP',
    `useragent` TEXT COLLATE utf8mb4_uca1400_nopad_ai_ci         NULL COMMENT 'UserAgent used in session',
    `username`  VARCHAR(64) COLLATE utf8mb4_uca1400_nopad_ai_ci  NULL COMMENT 'Name of either user (if logged in) or bot, if session belongs to one',
    `page`      VARCHAR(256) COLLATE utf8mb4_uca1400_nopad_ai_ci NULL COMMENT 'Which page is being viewed at the moment',
    `data`      TEXT                                             NULL COMMENT 'Session''s data. Not meant for sensitive information.',
    CONSTRAINT `session_to_cookie` FOREIGN KEY (`cookieid`) REFERENCES `uc__cookies` (`cookieid`),
    CONSTRAINT `session_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE ON DELETE CASCADE
) `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `bot` ON `uc__sessions` (`bot`);

CREATE INDEX `time` ON `uc__sessions` (`time` DESC);

CREATE INDEX `username` ON `uc__sessions` (`username`);

CREATE INDEX `viewing` ON `uc__sessions` (`page`);
