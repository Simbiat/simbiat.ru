CREATE TABLE `ffxiv__achievement`
(
    `achievementid` SMALLINT UNSIGNED                                NOT NULL COMMENT 'Achievement ID taken from Lodestone (https://eu.finalfantasyxiv.com/lodestone/character/characterid/achievement/detail/achievementid/)' PRIMARY KEY,
    `name`          VARCHAR(100) COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Name of achievement',
    `registered`    DATE        DEFAULT CURRENT_TIMESTAMP()          NOT NULL COMMENT 'When achievement was initially added to tracker',
    `updated`       DATETIME(6) DEFAULT CURRENT_TIMESTAMP(6)         NOT NULL ON UPDATE CURRENT_TIMESTAMP(6) COMMENT 'When achievement was last updated on the tracker',
    `category`      VARCHAR(30) COLLATE utf8mb4_uca1400_nopad_as_ci  NULL COMMENT 'Category of the achievement',
    `subcategory`   VARCHAR(30) COLLATE utf8mb4_uca1400_nopad_as_ci  NULL COMMENT 'Subcategory of the achievement',
    `icon`          VARCHAR(150) COLLATE utf8mb4_uca1400_nopad_as_ci NULL COMMENT 'Achievement icon without base URL (https://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/)',
    `howto`         TEXT COLLATE utf8mb4_uca1400_nopad_ai_ci         NULL COMMENT 'Instructions on getting achievements taken from Lodestone',
    `points`        TINYINT UNSIGNED                                 NULL COMMENT 'Amount of points assigned to character for getting the achievement',
    `title`         VARCHAR(50) COLLATE utf8mb4_uca1400_nopad_ai_ci  NULL COMMENT 'Optional title rewarded to character',
    `item`          VARCHAR(100) COLLATE utf8mb4_uca1400_nopad_ai_ci NULL COMMENT 'Optional item rewarded to character',
    `itemicon`      VARCHAR(150) COLLATE utf8mb4_uca1400_nopad_as_ci NULL COMMENT 'Icon for optional item without base URL (https://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/)',
    `itemid`        VARCHAR(11) COLLATE utf8mb4_uca1400_nopad_as_ci  NULL COMMENT 'ID of optional item taken from Lodestone (https://eu.finalfantasyxiv.com/lodestone/playguide/db/item/itemid/)',
    `dbid`          VARCHAR(11) COLLATE utf8mb4_uca1400_nopad_as_ci  NULL COMMENT 'ID of achievement in Lodestone database (https://eu.finalfantasyxiv.com/lodestone/playguide/db/achievement/dbid/)',
    CONSTRAINT `dbid` UNIQUE (`dbid`),
    CONSTRAINT `itemid` UNIQUE (`itemid`)
) COMMENT 'Achievements found on Lodestone' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE FULLTEXT INDEX `howto` ON `ffxiv__achievement` (`howto`);

CREATE FULLTEXT INDEX `name` ON `ffxiv__achievement` (`name`);

CREATE INDEX `name_order` ON `ffxiv__achievement` (`name`);

CREATE INDEX `updated` ON `ffxiv__achievement` (`updated` DESC);
