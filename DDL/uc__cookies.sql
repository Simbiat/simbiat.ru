CREATE TABLE `uc__cookies`
(
    `cookieid`  VARCHAR(256)                                    NOT NULL COMMENT 'Cookie ID' PRIMARY KEY,
    `userid`    INT UNSIGNED DEFAULT 1                          NOT NULL COMMENT 'ID of the user to which this cookie belongs',
    `validator` TEXT                                            NOT NULL COMMENT 'Encrypted validator string, to compare against cookie',
    `time`      DATETIME(6)  DEFAULT CURRENT_TIMESTAMP(6)       NOT NULL ON UPDATE CURRENT_TIMESTAMP(6) COMMENT 'Time of last update/use of the cookie',
    `ip`        VARCHAR(45) COLLATE utf8mb4_uca1400_nopad_as_ci NULL COMMENT 'Last IP, that used the cookie',
    `useragent` TEXT COLLATE utf8mb4_uca1400_nopad_ai_ci        NULL COMMENT 'Last UserAgent of the client, from which the cookie was used',
    CONSTRAINT `cookie_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE ON DELETE CASCADE
) `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `time` ON `uc__cookies` (`time` DESC);
