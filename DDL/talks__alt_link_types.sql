CREATE TABLE `talks__alt_link_types`
(
    `type`  VARCHAR(25) COLLATE utf8mb4_uca1400_nopad_as_ci                                 NOT NULL COMMENT 'Type (or rather name) of alternative source' PRIMARY KEY,
    `icon`  VARCHAR(100) COLLATE utf8mb4_uca1400_nopad_as_ci DEFAULT '/img/icons/Earth.svg' NOT NULL COMMENT 'Icon for the source',
    `regex` VARCHAR(255)                                                                    NOT NULL COMMENT 'Regex to validate the domain when adding links. Do not include ''www'' and remember to escape dots with ''\''!'
)
comment '"Types" OF alternative links FOR forum threads' `PAGE_COMPRESSED` = 'ON';
