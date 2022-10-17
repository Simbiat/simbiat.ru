CREATE TABLE `ffxiv__city`
(
    `cityid` TINYINT(1) UNSIGNED AUTO_INCREMENT COMMENT 'City ID as registered by the tracker' PRIMARY KEY,
    `city`   VARCHAR(25) NOT NULL COMMENT 'Name of the starting city',
    `region` VARCHAR(25) NOT NULL COMMENT 'Name of the region the city is located in',
    CONSTRAINT `city` UNIQUE (`city`)
) COMMENT 'Known cities';
