CREATE TABLE `uc__cookies`
(
    `cookieid`  VARCHAR(256)                             NOT NULL COMMENT 'Cookie ID' PRIMARY KEY,
    `userid`    INT UNSIGNED DEFAULT 1                   NOT NULL COMMENT 'ID of the user to which this cookie belongs',
    `validator` TEXT                                     NOT NULL COMMENT 'Encrypted validator string, to compare against cookie',
    `time`      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP() NOT NULL ON UPDATE CURRENT_TIMESTAMP() COMMENT 'Time of last update/use of the cookie',
    `ip`        VARCHAR(45)                              NULL COMMENT 'Last IP, that used the cookie',
    `useragent` TEXT                                     NULL COMMENT 'Last UserAgent of the client, from which the cookie was used',
    CONSTRAINT `cookie_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = `InnoDB` `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `time` ON `uc__cookies` (`time` DESC);
