USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__red_flags` (
  `character_id` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `echo_takedown` datetime(6) DEFAULT NULL COMMENT 'Date when echo-takedown was found in bio',
  `echo_takedown_value` varchar(22) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Value of identified echo-takedown code',
  `echo_takedown_bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Bio text snapshot where echo-takedown was found',
  `echo_claim` datetime(6) DEFAULT NULL COMMENT 'Date when echo-claim was found in bio',
  `echo_claim_value` varchar(19) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Value of identified echo-claim code',
  `echo_claim_bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Bio text snapshot where echo-claim was found',
  `echo_verify` datetime(6) DEFAULT NULL COMMENT 'Date when echo-verify was found in bio',
  `echo_verify_value` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Value of identified echo verification code',
  `echo_verify_bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Bio text snapshot where echo-verify was found',
  `echo_10` datetime(6) DEFAULT NULL COMMENT 'Date when potential EchoVault 10 character code was found in bio',
  `echo_10_value` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Value of identified potential EchoVault 10 character code',
  `echo_10_bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Bio text snapshot where potential EchoVault 10 character code was found',
  `echo_409` datetime(6) DEFAULT NULL COMMENT 'Date when character was checked and got 409 response from EchoVault',
  `echo_409_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Evidence of 409 from EchoVault',
  PRIMARY KEY (`character_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Collection of red flag for characters that may be involve in malicious actions, including, but not limited to, stalking.' `PAGE_COMPRESSED`='ON';