CREATE TABLE `talks__alt_link_types`
(
    `type` VARCHAR(25)                                 NOT NULL COMMENT 'Type (or rather name) of alternative source' PRIMARY KEY,
    `icon` VARCHAR(100) DEFAULT '/img/icons/Earth.svg' NOT NULL COMMENT 'Icon for the source'
) COMMENT '"Types" of alternative links for forum threads';
