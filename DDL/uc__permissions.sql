CREATE TABLE `uc__permissions`
(
    `permission`  VARCHAR(25) NOT NULL COMMENT 'Short name of permission, used as an ID' PRIMARY KEY,
    `description` TEXT        NOT NULL COMMENT 'Description of the permission'
) COMMENT 'List of permissions' ENGINE = `InnoDB` `PAGE_COMPRESSED` = 'ON';
