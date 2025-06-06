USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__nameday` (
  `namedayid` smallint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Nameday ID as registered by the tracker',
  `nameday` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Nameday',
  PRIMARY KEY (`namedayid`),
  UNIQUE KEY `nameday` (`nameday`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Namedays as per lore' `PAGE_COMPRESSED`='ON';