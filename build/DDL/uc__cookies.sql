CREATE TABLE `uc__cookies` (
  `cookieid` varchar(256) NOT NULL COMMENT 'Cookie ID',
  `userid` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'ID of the user to which this cookie belongs',
  `validator` text NOT NULL COMMENT 'Encrypted validator string, to compare against cookie',
  `time` datetime(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6) COMMENT 'Time of last update/use of the cookie',
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci DEFAULT NULL COMMENT 'Last IP, that used the cookie',
  `useragent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci DEFAULT NULL COMMENT 'Last UserAgent of the client, from which the cookie was used',
  PRIMARY KEY (`cookieid`) USING BTREE,
  KEY `cookie_to_user` (`userid`),
  KEY `time` (`time` DESC),
  CONSTRAINT `cookie_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC `PAGE_COMPRESSED`='ON';