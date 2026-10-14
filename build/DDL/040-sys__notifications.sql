USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `sys__notifications` (
  `uuid` uuid NOT NULL COMMENT 'ID of the notification',
  `created` timestamp(6) NOT NULL DEFAULT current_timestamp(6) COMMENT 'When notification was created',
  `user_id` int(10) unsigned NOT NULL COMMENT 'ID of the user notification is meant for',
  `type` tinyint(3) unsigned NOT NULL COMMENT 'Type of notification',
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Text of notification',
  `email` varchar(254) DEFAULT NULL COMMENT 'If not null and valid email, means the notification is meant to be sent to this email',
  `push` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Whether notification is supposed to be shown in UI',
  `attempts` tinyint(2) unsigned NOT NULL DEFAULT 0 COMMENT 'Number of attempts for sending an email',
  `last_attempt` timestamp(6) NULL DEFAULT NULL COMMENT 'Time of last attempt to send the email',
  `sent` timestamp(6) NULL DEFAULT NULL COMMENT 'If not null - when notification was sent. If null - means that it was not sent by email and needs to be retried.',
  `is_read` timestamp(6) NULL DEFAULT NULL COMMENT 'If not null - means that notification was read',
  PRIMARY KEY (`uuid`),
  KEY `user_id` (`user_id`),
  KEY `email` (`email`),
  KEY `push` (`push`),
  KEY `sent` (`sent`),
  KEY `is_read` (`is_read`),
  KEY `created` (`created`),
  KEY `type` (`type`),
  KEY `last_attempt` (`last_attempt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_ci COMMENT='List of notifications' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;