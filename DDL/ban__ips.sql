CREATE TABLE `ban__ips`
(
    `ip`     VARCHAR(45) COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Banned IP' PRIMARY KEY,
    `added`  DATETIME(6) DEFAULT CURRENT_TIMESTAMP(6)        NOT NULL COMMENT 'When IP was banned',
    `reason` TEXT COLLATE utf8mb4_uca1400_nopad_ai_ci        NULL COMMENT 'Reason for the ban'
) COMMENT 'Banned IPs' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `added` ON `ban__ips` (`added`);
