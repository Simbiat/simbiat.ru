CREATE TABLE `ffxiv__freecompany_rank`
(
    `freecompanyid` VARCHAR(20) COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Free Company ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/freecompany/freecompanyid/)',
    `rankid`        TINYINT(2) UNSIGNED                             NOT NULL COMMENT 'ID calculated based on rank icon on Lodestone',
    `rankname`      VARCHAR(15) COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Name of the rank as reported by Lodestone',
    PRIMARY KEY (`freecompanyid`, `rankid`),
    CONSTRAINT `fcranks_freecompany` FOREIGN KEY (`freecompanyid`) REFERENCES `ffxiv__freecompany` (`freecompanyid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Rank names used by companies' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `rankid` ON `ffxiv__freecompany_rank` (`rankid`);
