CREATE TABLE `ffxiv__freecompany_ranking`
(
    `freecompanyid` VARCHAR(20) COLLATE utf8mb4_uca1400_nopad_as_ci  NOT NULL COMMENT 'Free Company ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/freecompany/freecompanyid/)',
    `date`          DATE                 DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'Date of the ranking as identified by tracker',
    `weekly`        SMALLINT(3) UNSIGNED DEFAULT 500                 NOT NULL COMMENT 'Weekly ranking as reported by Lodestone',
    `monthly`       SMALLINT(3) UNSIGNED DEFAULT 500                 NOT NULL COMMENT 'Monthly ranking as reported by Lodestone',
    `members`       SMALLINT(3) UNSIGNED DEFAULT 1                   NOT NULL COMMENT 'Number of registered members at the date of rank update',
    PRIMARY KEY (`freecompanyid`, `date`),
    CONSTRAINT `fc_ranking_id` FOREIGN KEY (`freecompanyid`) REFERENCES `ffxiv__freecompany` (`freecompanyid`)
) COMMENT 'Companies'' weekly and monthly rankings linked to members count' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `date` ON `ffxiv__freecompany_ranking` (`date` DESC);
