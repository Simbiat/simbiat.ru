CREATE TABLE IF NOT EXISTS `bic__bic_rstr` (
  `BIC` int(9) unsigned zerofill NOT NULL COMMENT 'БИК участника',
  `Rstr` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Код ограничения, наложенного на участника',
  `RstrDate` date NOT NULL DEFAULT current_timestamp() COMMENT 'Дата начала действия ограничения участника',
  `DateOut` date DEFAULT NULL COMMENT 'Дата окончания действия ограничения участника',
  PRIMARY KEY (`BIC`,`Rstr`,`RstrDate`),
  KEY `rstr_to_rstr` (`Rstr`),
  KEY `RstrDate` (`RstrDate` DESC),
  CONSTRAINT `rstr_to_bic` FOREIGN KEY (`BIC`) REFERENCES `bic__list` (`BIC`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rstr_to_rstr` FOREIGN KEY (`Rstr`) REFERENCES `bic__rstr` (`Rstr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Список ограничений наложенных на участника' `PAGE_COMPRESSED`='ON';