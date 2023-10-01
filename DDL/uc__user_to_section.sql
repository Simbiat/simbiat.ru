CREATE TABLE `uc__user_to_section`
(
    `userid`        INT UNSIGNED NOT NULL COMMENT 'User ID' PRIMARY KEY,
    `blog`          INT UNSIGNED NULL COMMENT 'ID of the personal blog',
    `knowledgebase` INT UNSIGNED NULL COMMENT 'ID of the personal knowledgebase',
    `changelog`     INT UNSIGNED NULL COMMENT 'ID of the personal changelog',
    CONSTRAINT `blog_to_sections` FOREIGN KEY (`blog`) REFERENCES `talks__sections` (`sectionid`) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT `changelog_to_sections` FOREIGN KEY (`changelog`) REFERENCES `talks__sections` (`sectionid`) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT `kb_to_sections` FOREIGN KEY (`knowledgebase`) REFERENCES `talks__sections` (`sectionid`) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT `special_section_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Junction table for special sections created by the user' `PAGE_COMPRESSED` = 'ON';
