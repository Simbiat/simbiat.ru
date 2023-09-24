CREATE TABLE `ban__ips`
(
    `ip`     VARCHAR(45)                           NOT NULL COMMENT 'Banned IP' PRIMARY KEY,
    `added`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'When IP was banned',
    `reason` TEXT                                  NULL COMMENT 'Reason for the ban'
) COMMENT 'Banned IPs' ENGINE = `InnoDB` `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `added` ON `ban__ips` (`added`);
