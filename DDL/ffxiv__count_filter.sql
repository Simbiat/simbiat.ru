CREATE TABLE `ffxiv__count_filter`
(
    `countId` TINYINT(1) UNSIGNED                            NOT NULL COMMENT 'ID of filter by members count for groups'' search on Lodestone' PRIMARY KEY,
    `value`   VARCHAR(5) COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Value that is used Lodestone when filtering',
    CONSTRAINT `value` UNIQUE (`value`)
) COMMENT 'Filters by members count for groups'' search on Lodestone' `PAGE_COMPRESSED` = 'ON';
