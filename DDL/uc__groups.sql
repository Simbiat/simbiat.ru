CREATE TABLE `uc__groups`
(
    `groupid`     INT UNSIGNED AUTO_INCREMENT COMMENT 'ID of the group' PRIMARY KEY,
    `groupname`   VARCHAR(25) COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Human-ready group name',
    `description` TEXT COLLATE utf8mb4_uca1400_nopad_ai_ci        NOT NULL COMMENT 'Description of the group',
    CONSTRAINT `groupname` UNIQUE (`groupname`)
) `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
