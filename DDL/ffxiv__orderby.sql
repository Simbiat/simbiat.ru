CREATE TABLE `ffxiv__orderby`
(
    `orderID`     TINYINT(1) UNSIGNED                              NOT NULL COMMENT 'ID based on filters from Lodestone' PRIMARY KEY,
    `Description` VARCHAR(100) COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Description of the order'
) COMMENT 'ORDER BY options for searches on Lodestone' `PAGE_COMPRESSED` = 'ON';
