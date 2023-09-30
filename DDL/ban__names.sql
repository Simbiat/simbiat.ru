CREATE TABLE `ban__names`
(
    `name`   VARCHAR(64) COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Banned (prohibited) name' PRIMARY KEY,
    `added`  DATETIME(6) DEFAULT CURRENT_TIMESTAMP(6)        NOT NULL COMMENT 'When name was banned',
    `reason` TEXT COLLATE utf8mb4_uca1400_nopad_ai_ci        NULL COMMENT 'Reason for the ban'
) COMMENT 'Banned user names' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `added` ON `ban__names` (`added`);
