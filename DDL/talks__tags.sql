CREATE TABLE `talks__tags`
(
    `tagid` INT UNSIGNED AUTO_INCREMENT COMMENT 'Tag ID' PRIMARY KEY,
    `tag`   VARCHAR(25) COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Tag',
    CONSTRAINT `tag` UNIQUE (`tag`)
) COMMENT 'List of tags' `PAGE_COMPRESSED` = 'ON';
