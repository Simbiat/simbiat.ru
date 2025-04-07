USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `bic__swift` (
  `BIC` int(9) unsigned zerofill NOT NULL COMMENT 'БИК код',
  `SWBIC` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Банковский идентификационный код, присвоенный SWIFT',
  `DefaultSWBIC` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Признак использования БИК (СВИФТ), «по умолчанию»',
  `DateIn` date DEFAULT current_timestamp() COMMENT 'Дата добавления кода',
  `DateOut` date DEFAULT NULL COMMENT 'Дата удаления кода',
  PRIMARY KEY (`BIC`,`SWBIC`) USING BTREE,
  KEY `SWBIC` (`SWBIC`),
  KEY `DefaultSWBIC` (`DefaultSWBIC` DESC),
  KEY `DateOut` (`DateOut` DESC),
  CONSTRAINT `bic_swift` FOREIGN KEY (`BIC`) REFERENCES `bic__list` (`BIC`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Коды SWIFT' `PAGE_COMPRESSED`='ON';