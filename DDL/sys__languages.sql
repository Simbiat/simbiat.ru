CREATE TABLE `sys__languages`
(
    `tag`  VARCHAR(35) COLLATE utf8mb4_uca1400_nopad_as_ci  NOT NULL COMMENT 'Language tag as per RFC 5646',
    `name` VARCHAR(100) COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Human-readable name',
    CONSTRAINT `name` UNIQUE (`name`),
    CONSTRAINT `tag` UNIQUE (`tag`)
) COMMENT 'List of language tags' `PAGE_COMPRESSED` = 'ON';
