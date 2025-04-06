CREATE TABLE IF NOT EXISTS `bic__accounts` (
  `Account` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Номер счёта',
  `BIC` int(9) unsigned zerofill NOT NULL COMMENT 'БИК участника',
  `AccountCBRBIC` int(9) unsigned zerofill DEFAULT NULL COMMENT 'БИК ПБР, обслуживающего счёт участника перевода',
  `RegulationAccountType` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Тип счета в соответствии с нормативом',
  `CK` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci DEFAULT NULL COMMENT 'Контрольный ключ',
  `DateIn` date NOT NULL DEFAULT current_timestamp() COMMENT 'Дата открытия счета',
  `DateOut` date DEFAULT NULL COMMENT 'Дата исключения информации о счете участника',
  PRIMARY KEY (`Account`,`BIC`) USING BTREE,
  KEY `account_to_type` (`RegulationAccountType`),
  KEY `account_to_cbr` (`AccountCBRBIC`),
  KEY `account_to_bic` (`BIC`),
  CONSTRAINT `account_to_bic` FOREIGN KEY (`BIC`) REFERENCES `bic__list` (`BIC`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `account_to_cbr` FOREIGN KEY (`AccountCBRBIC`) REFERENCES `bic__list` (`BIC`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `account_to_type` FOREIGN KEY (`RegulationAccountType`) REFERENCES `bic__acc_type` (`RegulationAccountType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Список счетов' `PAGE_COMPRESSED`='ON';