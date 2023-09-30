CREATE TABLE `bic__list`
(
    `BIC`        INT(9) UNSIGNED ZEROFILL                                                    NOT NULL COMMENT 'Банковский идентификационный код (БИК)' PRIMARY KEY,
    `DateIn`     DATE                                           DEFAULT CURRENT_TIMESTAMP()  NULL COMMENT 'Дата включения в состав участников перевода',
    `DateOut`    DATE                                                                        NULL COMMENT 'Дата исключения информации об Участнике',
    `Updated`    DATETIME(6)                                    DEFAULT CURRENT_TIMESTAMP(6) NULL COMMENT 'Дата изменения записи',
    `NameP`      VARCHAR(160) COLLATE utf8mb4_uca1400_nopad_ai_ci                            NOT NULL COMMENT 'Наименование участника расчетов',
    `EnglName`   VARCHAR(140) COLLATE utf8mb4_uca1400_nopad_ai_ci                            NULL COMMENT 'Наименование участника на английском языке',
    `XchType`    TINYINT(1)                                     DEFAULT 0                    NOT NULL COMMENT 'Участник обмена (0 - не участник)',
    `PtType`     VARCHAR(2) COLLATE utf8mb4_uca1400_nopad_ai_ci DEFAULT '99'                 NOT NULL COMMENT 'Код типа участника расчетов',
    `Srvcs`      VARCHAR(1) COLLATE utf8mb4_uca1400_nopad_ai_ci                              NULL COMMENT 'Доступные сервисы перевода денежных средств',
    `UID`        VARCHAR(10) COLLATE utf8mb4_uca1400_nopad_as_ci                             NULL COMMENT 'Уникальный идентификатор составителя ЭС; УИС',
    `PrntBIC`    INT(9) UNSIGNED                                                             NULL COMMENT 'БИК головной организации',
    `CntrCd`     VARCHAR(2) COLLATE utf8mb4_uca1400_nopad_ai_ci                              NULL COMMENT 'Код страны',
    `RegN`       VARCHAR(9) COLLATE utf8mb4_uca1400_nopad_ai_ci                              NULL COMMENT 'Регистрационный порядковый номер',
    `Ind`        VARCHAR(6) COLLATE utf8mb4_uca1400_nopad_ai_ci                              NULL COMMENT 'Индекс',
    `Rgn`        VARCHAR(2) COLLATE utf8mb4_uca1400_nopad_ai_ci DEFAULT '00'                 NOT NULL COMMENT 'Код территории',
    `Tnp`        VARCHAR(5) COLLATE utf8mb4_uca1400_nopad_ai_ci                              NULL COMMENT 'Тип населённого пункта',
    `Nnp`        VARCHAR(25) COLLATE utf8mb4_uca1400_nopad_ai_ci                             NULL COMMENT 'Наименование населённого пункта',
    `Adr`        VARCHAR(160) COLLATE utf8mb4_uca1400_nopad_ai_ci                            NULL COMMENT 'Адрес',
    `DATE_CH`    DATE                                                                        NULL COMMENT 'Дата вступления в силу',
    `NAMEN`      VARCHAR(30) COLLATE utf8mb4_uca1400_nopad_ai_ci                             NULL COMMENT 'Наименование участника расчетов для поиска в ЭБД',
    `NAMEMAXB`   VARCHAR(140) COLLATE utf8mb4_uca1400_nopad_ai_ci                            NULL COMMENT 'Фирменное (полное официальное) наименование кредитной организации (KEYBASEB.DBF)',
    `SWIFT_NAME` VARCHAR(90) COLLATE utf8mb4_uca1400_nopad_ai_ci                             NULL COMMENT 'Имя банка в системе SWIFT из старого DBF (NAME_SRUS)',
    `OLD_NEWNUM` INT(9) UNSIGNED ZEROFILL                                                    NULL COMMENT 'У некоторых старых записей БИК неуникален, что противоречит более новым схемам. Для сохранения информации, вводится эта колонка.',
    `VKEY`       VARCHAR(8) COLLATE utf8mb4_uca1400_nopad_as_ci                              NULL COMMENT 'Уникальный внутренний код',
    `VKEYDEL`    VARCHAR(8) COLLATE utf8mb4_uca1400_nopad_as_ci                              NULL COMMENT 'Уникальный внутренний код преемника',
    `BVKEY`      VARCHAR(8) COLLATE utf8mb4_uca1400_nopad_as_ci                              NULL COMMENT 'Внутренний код участника расчётов по ЭБД «Книги ГРКО» (KEYBASEB.DBF)',
    `FVKEY`      VARCHAR(8) COLLATE utf8mb4_uca1400_nopad_as_ci                              NULL COMMENT 'Внутренний код участника расчётов по ЭБД «Книги ГРКО» (KEYBASEF.dbf)',
    `AT1`        VARCHAR(7) COLLATE utf8mb4_uca1400_nopad_ai_ci                              NULL COMMENT 'Абонентский телеграф 1',
    `AT2`        VARCHAR(7) COLLATE utf8mb4_uca1400_nopad_ai_ci                              NULL COMMENT 'Абонентский телеграф 2',
    `CKS`        VARCHAR(6) COLLATE utf8mb4_uca1400_nopad_ai_ci                              NULL COMMENT 'Номер установки центра коммутации сообщений',
    `TELEF`      VARCHAR(25) COLLATE utf8mb4_uca1400_nopad_ai_ci                             NULL COMMENT 'Телефон',
    `SROK`       VARCHAR(2) COLLATE utf8mb4_uca1400_nopad_ai_ci                              NULL COMMENT 'Срок прохождения документов',
    `NEWKS`      VARCHAR(9) COLLATE utf8mb4_uca1400_nopad_ai_ci                              NULL COMMENT 'Корреспондентский счет (субсчет), действовавший до перехода на новый План счетов бухгалтерского учета',
    `OKPO`       VARCHAR(8) COLLATE utf8mb4_uca1400_nopad_ai_ci                              NULL COMMENT 'Код ОКПО',
    `PERMFO`     VARCHAR(6) COLLATE utf8mb4_uca1400_nopad_ai_ci                              NULL COMMENT 'Номер МФО',
    `RKC`        INT(9) UNSIGNED ZEROFILL                                                    NULL COMMENT 'БИК РКЦ (ГРКЦ)',
    `R_CLOSE`    VARCHAR(2) COLLATE utf8mb4_uca1400_nopad_ai_ci                              NULL COMMENT 'Код причины закрытия номера счета',
    `PRIM1`      VARCHAR(30) COLLATE utf8mb4_uca1400_nopad_ai_ci                             NULL COMMENT 'Основание для ограничения участия в расчётах или исключения из состава участников расчётов (PRIM.dbf)',
    `PRIM2`      VARCHAR(34) COLLATE utf8mb4_uca1400_nopad_ai_ci                             NULL COMMENT 'Реквизиты ликвидационной комиссии (PRIM.dbf)',
    `PRIM3`      VARCHAR(30) COLLATE utf8mb4_uca1400_nopad_ai_ci                             NULL COMMENT 'Основание для аннулировании в «Книге ГРКО» записи о регистрации кредитной организации (филиала) (PRIM.dbf)',
    CONSTRAINT `bic__pzn` FOREIGN KEY (`PtType`) REFERENCES `bic__pzn` (`PtType`),
    CONSTRAINT `bic__rclose` FOREIGN KEY (`R_CLOSE`) REFERENCES `bic__rclose` (`R_CLOSE`),
    CONSTRAINT `bic__rgn` FOREIGN KEY (`Rgn`) REFERENCES `bic__reg` (`RGN`),
    CONSTRAINT `bic__service` FOREIGN KEY (`Srvcs`) REFERENCES `bic__srvcs` (`Srvcs`)
) COMMENT 'Список банков (BNKSEEK + BNKDEL)' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE FULLTEXT INDEX `Adr` ON `bic__list` (`Adr`);

CREATE INDEX `DateOut` ON `bic__list` (`DateOut` DESC);

CREATE FULLTEXT INDEX `NameP` ON `bic__list` (`NameP`);

CREATE INDEX `OLD_NEWNUM` ON `bic__list` (`OLD_NEWNUM`);

CREATE INDEX `REGN` ON `bic__list` (`RegN`);

CREATE INDEX `RKC` ON `bic__list` (`RKC`);

CREATE INDEX `Updated` ON `bic__list` (`Updated` DESC);

CREATE INDEX `VKEY` ON `bic__list` (`VKEY`);

CREATE INDEX `VKEYDEL` ON `bic__list` (`VKEYDEL`);
