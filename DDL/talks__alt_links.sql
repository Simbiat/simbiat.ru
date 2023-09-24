CREATE TABLE `talks__alt_links`
(
    `threadid` INT UNSIGNED NOT NULL COMMENT 'Thread ID, to which link relates to',
    `url`      VARCHAR(255) NOT NULL COMMENT 'Respective alternative URL',
    `type`     VARCHAR(25)  NOT NULL COMMENT 'Type (or rather name) of alternative source',
    PRIMARY KEY (`threadid`, `type`),
    CONSTRAINT `alt_link_to_thread` FOREIGN KEY (`threadid`) REFERENCES `talks__threads` (`threadid`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `alt_link_to_type` FOREIGN KEY (`type`) REFERENCES `talks__alt_link_types` (`type`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Alternative representation of the threads on other websites' ENGINE = `InnoDB` `PAGE_COMPRESSED` = 'ON';
