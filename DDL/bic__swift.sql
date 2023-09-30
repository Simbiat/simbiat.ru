CREATE TABLE `bic__swift`
(
    `BIC`          INT(9) UNSIGNED ZEROFILL                        NOT NULL COMMENT 'БИК код',
    `SWBIC`        VARCHAR(11) COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Банковский идентификационный код, присвоенный SWIFT',
    `DefaultSWBIC` TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Признак использования БИК (СВИФТ), «по умолчанию»',
    `DateIn`       DATE                DEFAULT CURRENT_TIMESTAMP() NULL COMMENT 'Дата добавления кода',
    `DateOut`      DATE                                            NULL COMMENT 'Дата удаления кода',
    PRIMARY KEY (`BIC`, `SWBIC`),
    CONSTRAINT `bic_swift` FOREIGN KEY (`BIC`) REFERENCES `bic__list` (`BIC`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Коды SWIFT' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `DateOut` ON `bic__swift` (`DateOut` DESC);

CREATE INDEX `DefaultSWBIC` ON `bic__swift` (`DefaultSWBIC` DESC);

CREATE INDEX `SWBIC` ON `bic__swift` (`SWBIC`);
