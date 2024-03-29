CREATE TABLE `bic__accounts`
(
    `Account`               VARCHAR(20) COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Номер счёта',
    `BIC`                   INT(9) UNSIGNED ZEROFILL                        NOT NULL COMMENT 'БИК участника',
    `AccountCBRBIC`         INT(9) UNSIGNED ZEROFILL                        NULL COMMENT 'БИК ПБР, обслуживающего счёт участника перевода',
    `RegulationAccountType` VARCHAR(4) COLLATE utf8mb4_uca1400_nopad_as_ci  NOT NULL COMMENT 'Тип счета в соответствии с нормативом',
    `CK`                    VARCHAR(2) COLLATE utf8mb4_uca1400_nopad_ai_ci  NULL COMMENT 'Контрольный ключ',
    `DateIn`                DATE DEFAULT CURRENT_TIMESTAMP()                NOT NULL COMMENT 'Дата открытия счета',
    `DateOut`               DATE                                            NULL COMMENT 'Дата исключения информации о счете участника',
    PRIMARY KEY (`Account`, `BIC`),
    CONSTRAINT `account_to_bic` FOREIGN KEY (`BIC`) REFERENCES `bic__list` (`BIC`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `account_to_cbr` FOREIGN KEY (`AccountCBRBIC`) REFERENCES `bic__list` (`BIC`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `account_to_type` FOREIGN KEY (`RegulationAccountType`) REFERENCES `bic__acc_type` (`RegulationAccountType`)
) COMMENT 'Список счетов' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
