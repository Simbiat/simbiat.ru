CREATE TABLE `seo__pageviews`
(
    `page`    VARCHAR(256)                                NOT NULL COMMENT 'Page URL',
    `referer` VARCHAR(256)    DEFAULT ''                  NOT NULL COMMENT 'Value of Referer HTTP header',
    `ip`      VARCHAR(45)     DEFAULT ''                  NOT NULL COMMENT 'IP of unique visitor',
    `os`      VARCHAR(100)    DEFAULT ''                  NOT NULL COMMENT 'OS version used by visitor',
    `client`  VARCHAR(100)    DEFAULT ''                  NOT NULL COMMENT 'Client version used by visitor',
    `first`   TIMESTAMP       DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'Time of first visit',
    `last`    TIMESTAMP       DEFAULT CURRENT_TIMESTAMP() NOT NULL ON UPDATE CURRENT_TIMESTAMP() COMMENT 'Time of last visit',
    `views`   BIGINT UNSIGNED DEFAULT 1                   NOT NULL COMMENT 'Number of views',
    PRIMARY KEY (`page`, `referer`, `ip`, `os`, `client`)
) COMMENT 'Views statistics per page' ENGINE = `InnoDB` `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `client` ON `seo__pageviews` (`client`);

CREATE INDEX `first` ON `seo__pageviews` (`first`);

CREATE INDEX `ip` ON `seo__pageviews` (`ip`);

CREATE INDEX `os` ON `seo__pageviews` (`os`);

CREATE INDEX `referer` ON `seo__pageviews` (`referer`);
