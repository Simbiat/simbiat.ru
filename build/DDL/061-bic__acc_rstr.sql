USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `bic__acc_rstr` (
  `Account` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Номер счёта',
  `AccRstr` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Код ограничения операций по счёту',
  `AccRstrDate` date NOT NULL DEFAULT current_timestamp() COMMENT 'Дата начала действия Ограничения операций по счёту',
  `DateOut` date DEFAULT NULL COMMENT 'Дата конца действия Ограничения операций по счёту',
  `SuccessorBIC` int(9) unsigned zerofill DEFAULT NULL COMMENT 'БИК преемника',
  PRIMARY KEY (`Account`) USING BTREE,
  KEY `acc_to_rstr` (`AccRstr`),
  KEY `acc_to_cbr` (`SuccessorBIC`),
  KEY `AccRstrDate` (`AccRstrDate` DESC),
  CONSTRAINT `acc_to_acc` FOREIGN KEY (`Account`) REFERENCES `bic__accounts` (`Account`),
  CONSTRAINT `acc_to_cbr` FOREIGN KEY (`SuccessorBIC`) REFERENCES `bic__list` (`BIC`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `acc_to_rstr` FOREIGN KEY (`AccRstr`) REFERENCES `bic__rstr` (`Rstr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Список ограничений наложенных на счета' `PAGE_COMPRESSED`='ON';