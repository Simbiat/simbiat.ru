CREATE TABLE `ban__mails`
(
    `mail`   VARCHAR(320)                          NOT NULL COMMENT 'Banned e-mail' PRIMARY KEY,
    `added`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'When e-mail was banned',
    `reason` TEXT                                  NULL COMMENT 'Reason for the ban'
) COMMENT 'Banned e-mail addresses' ENGINE = `InnoDB` `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `added` ON `ban__mails` (`added`);
