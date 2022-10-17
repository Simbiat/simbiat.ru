CREATE TABLE `ffxiv__estate`
(
    `estateid` SMALLINT UNSIGNED AUTO_INCREMENT COMMENT 'Estate ID as registered by the tracker' PRIMARY KEY,
    `cityid`   TINYINT(2) UNSIGNED DEFAULT 5 NOT NULL COMMENT 'City ID as registered by the tracker',
    `area`     VARCHAR(20)                   NOT NULL COMMENT 'Estate area name',
    `ward`     TINYINT UNSIGNED              NOT NULL COMMENT 'Ward number',
    `plot`     TINYINT UNSIGNED              NOT NULL COMMENT 'Plot number',
    `size`     TINYINT(1) UNSIGNED           NOT NULL COMMENT 'Size of the house, where 1 is for small, 2 is for medium and 3 is for large',
    CONSTRAINT `address` UNIQUE (`area`, `ward`, `plot`),
    CONSTRAINT `estate_cityid` FOREIGN KEY (`cityid`) REFERENCES `ffxiv__city` (`cityid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'List of estates';
