CREATE TABLE `uc__sessions`
(
    `sessionid` VARCHAR(256)                                    NOT NULL COMMENT 'Session''s UID' PRIMARY KEY,
    `cookieid`  VARCHAR(256)                                    NULL COMMENT 'Cookie associated with the session',
    `time`      TIMESTAMP           DEFAULT CURRENT_TIMESTAMP() NOT NULL ON UPDATE CURRENT_TIMESTAMP() COMMENT 'Last time session was determined active',
    `userid`    INT UNSIGNED                                    NOT NULL COMMENT 'User ID, in case session is related to a user. Needed mainly for invalidation in case of complete user deletion.',
    `bot`       TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Whether session was determined to be a bot',
    `ip`        VARCHAR(45)                                     NULL COMMENT 'Session''s IP',
    `useragent` TEXT                                            NULL COMMENT 'UserAgent used in session',
    `username`  VARCHAR(64)                                     NULL COMMENT 'Name of either user (if logged in) or bot, if session belongs to one',
    `page`      VARCHAR(256)                                    NULL COMMENT 'Which page is being viewed at the moment',
    `data`      TEXT                                            NULL COMMENT 'Session''s data. Not meant for sensitive information.',
    CONSTRAINT `session_to_cookie` FOREIGN KEY (`cookieid`) REFERENCES `uc__cookies` (`cookieid`) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT `session_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE INDEX `bot` ON `uc__sessions` (`bot`);

CREATE INDEX `time` ON `uc__sessions` (`time` DESC);

CREATE INDEX `username` ON `uc__sessions` (`username`);

CREATE INDEX `viewing` ON `uc__sessions` (`page`);
