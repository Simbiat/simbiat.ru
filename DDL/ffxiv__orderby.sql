CREATE TABLE `ffxiv__orderby`
(
    `orderID`     TINYINT(1) UNSIGNED NOT NULL COMMENT 'ID based on filters from Lodestone' PRIMARY KEY,
    `Description` VARCHAR(100)        NOT NULL COMMENT 'Description of the order'
) COMMENT 'ORDER BY options for searches on Lodestone';
