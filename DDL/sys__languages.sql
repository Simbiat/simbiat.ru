CREATE TABLE `sys__languages`
(
    `tag`  VARCHAR(35)  NOT NULL COMMENT 'Language tag as per RFC 5646',
    `name` VARCHAR(100) NOT NULL COMMENT 'Human-readable name',
    CONSTRAINT `name` UNIQUE (`name`),
    CONSTRAINT `tag` UNIQUE (`tag`)
) COMMENT 'List of language tags' ENGINE = `InnoDB` `PAGE_COMPRESSED` = 'ON';
