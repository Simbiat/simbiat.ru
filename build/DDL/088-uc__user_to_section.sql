USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `uc__user_to_section` (
  `user_id` int(10) unsigned NOT NULL COMMENT 'User ID',
  `blog` int(10) unsigned DEFAULT NULL COMMENT 'ID of the personal blog',
  `knowledgebase` int(10) unsigned DEFAULT NULL COMMENT 'ID of the personal knowledgebase',
  `changelog` int(10) unsigned DEFAULT NULL COMMENT 'ID of the personal changelog',
  PRIMARY KEY (`user_id`),
  KEY `blog_to_sections` (`blog`),
  KEY `changelog_to_sections` (`changelog`),
  KEY `kb_to_sections` (`knowledgebase`),
  CONSTRAINT `blog_to_sections` FOREIGN KEY (`blog`) REFERENCES `talks__sections` (`section_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `changelog_to_sections` FOREIGN KEY (`changelog`) REFERENCES `talks__sections` (`section_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `kb_to_sections` FOREIGN KEY (`knowledgebase`) REFERENCES `talks__sections` (`section_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `special_section_to_user` FOREIGN KEY (`user_id`) REFERENCES `uc__users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='Junction table for special sections created by the user' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;