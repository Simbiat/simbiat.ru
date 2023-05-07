CREATE TABLE `seo__visitors`
(
    `ip`     VARCHAR(45)                                 NOT NULL COMMENT 'IP of unique visitor',
    `os`     VARCHAR(100)    DEFAULT ''                  NOT NULL COMMENT 'OS version used by visitor',
    `client` VARCHAR(100)    DEFAULT ''                  NOT NULL COMMENT 'Client version used by visitor',
    `first`  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'Time of first visit',
    `last`   TIMESTAMP       DEFAULT CURRENT_TIMESTAMP() NOT NULL ON UPDATE CURRENT_TIMESTAMP() COMMENT 'Time of last visit',
    `views`  BIGINT UNSIGNED DEFAULT 1                   NOT NULL COMMENT 'Number of viewed pages',
    PRIMARY KEY (`ip`, `os`, `client`)
) COMMENT 'Views statistics per user' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `client` ON `seo__visitors` (`client`);

CREATE INDEX `first` ON `seo__visitors` (`first`);

CREATE INDEX `os` ON `seo__visitors` (`os`);
