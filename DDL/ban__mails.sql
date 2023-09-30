CREATE TABLE `ban__mails`
(
    `mail`   VARCHAR(320) COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Banned e-mail' PRIMARY KEY,
    `added`  DATETIME(6) DEFAULT CURRENT_TIMESTAMP(6)         NOT NULL COMMENT 'When e-mail was banned',
    `reason` TEXT COLLATE utf8mb4_uca1400_nopad_ai_ci         NULL COMMENT 'Reason for the ban'
) COMMENT 'Banned e-mail addresses' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `added` ON `ban__mails` (`added`);
