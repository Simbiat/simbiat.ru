CREATE TABLE `ffxiv__achievement`
(
    `achievementid` SMALLINT UNSIGNED                     NOT NULL COMMENT 'Achievement ID taken from Lodestone (https://eu.finalfantasyxiv.com/lodestone/character/characterid/achievement/detail/achievementid/)' PRIMARY KEY,
    `name`          VARCHAR(100)                          NOT NULL COMMENT 'Name of achievement',
    `registered`    DATE      DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'When achievement was initially added to tracker',
    `updated`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP() NOT NULL ON UPDATE CURRENT_TIMESTAMP() COMMENT 'When achievement was last updated on the tracker',
    `category`      VARCHAR(30)                           NULL COMMENT 'Category of the achievement',
    `subcategory`   VARCHAR(30)                           NULL COMMENT 'Subcategory of the achievement',
    `icon`          VARCHAR(150)                          NULL COMMENT 'Achievement icon without base URL (https://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/)',
    `howto`         TEXT                                  NULL COMMENT 'Instructions on getting achievements taken from Lodestone',
    `points`        TINYINT UNSIGNED                      NULL COMMENT 'Amount of points assigned to character for getting the achievement',
    `title`         VARCHAR(50)                           NULL COMMENT 'Optional title rewarded to character',
    `item`          VARCHAR(100)                          NULL COMMENT 'Optional item rewarded to character',
    `itemicon`      VARCHAR(150)                          NULL COMMENT 'Icon for optional item without base URL (https://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/)',
    `itemid`        VARCHAR(11)                           NULL COMMENT 'ID of optional item taken from Lodestone (https://eu.finalfantasyxiv.com/lodestone/playguide/db/item/itemid/)',
    `dbid`          VARCHAR(11)                           NULL COMMENT 'ID of achievement in Lodestone database (https://eu.finalfantasyxiv.com/lodestone/playguide/db/achievement/dbid/)',
    CONSTRAINT `dbid` UNIQUE (`dbid`),
    CONSTRAINT `itemid` UNIQUE (`itemid`)
) COMMENT 'Achievements found on Lodestone';

CREATE FULLTEXT INDEX `howto` ON `ffxiv__achievement` (`howto`);

CREATE FULLTEXT INDEX `name` ON `ffxiv__achievement` (`name`);

CREATE INDEX `name_order` ON `ffxiv__achievement` (`name`);

CREATE INDEX `updated` ON `ffxiv__achievement` (`updated` DESC);
