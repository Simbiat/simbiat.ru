CREATE TABLE `talks__tags`
(
    `tagid` INT UNSIGNED AUTO_INCREMENT COMMENT 'Tag ID' PRIMARY KEY,
    `tag`   VARCHAR(25) NOT NULL COMMENT 'Tag',
    CONSTRAINT `tag` UNIQUE (`tag`)
) COMMENT 'List of tags';
