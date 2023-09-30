CREATE TABLE `uc__permissions`
(
    `permission`  VARCHAR(25) COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Short name of permission, used as an ID' PRIMARY KEY,
    `description` TEXT COLLATE utf8mb4_uca1400_nopad_ai_ci        NOT NULL COMMENT 'Description of the permission'
) COMMENT 'List of permissions' `PAGE_COMPRESSED` = 'ON';
