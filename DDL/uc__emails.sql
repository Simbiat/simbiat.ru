CREATE TABLE `uc__emails`
(
    `email`      VARCHAR(100)                  NOT NULL COMMENT 'Email address' PRIMARY KEY,
    `userid`     INT UNSIGNED        DEFAULT 1 NOT NULL COMMENT 'User ID',
    `subscribed` TINYINT(1) UNSIGNED DEFAULT 0 NOT NULL COMMENT 'Flag indicating, that this mail should receive notifications',
    `activation` TEXT                          NULL COMMENT 'Encrypted activation code',
    CONSTRAINT `email_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = `InnoDB` `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `subscribed` ON `uc__emails` (`subscribed` DESC);

CREATE INDEX `userid` ON `uc__emails` (`userid`);
