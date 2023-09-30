CREATE TABLE `seo__visitors`
(
    `ip`     VARCHAR(45) COLLATE utf8mb4_uca1400_nopad_as_ci  NOT NULL COMMENT 'IP of unique visitor',
    `os`     VARCHAR(100) COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'OS version used by visitor',
    `client` VARCHAR(100) COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Client version used by visitor',
    `first`  DATETIME(6)     DEFAULT CURRENT_TIMESTAMP(6)     NOT NULL COMMENT 'Time of first visit',
    `last`   DATETIME(6)     DEFAULT CURRENT_TIMESTAMP(6)     NOT NULL ON UPDATE CURRENT_TIMESTAMP(6) COMMENT 'Time of last visit',
    `views`  BIGINT UNSIGNED DEFAULT 1                        NOT NULL COMMENT 'Number of viewed pages',
    PRIMARY KEY (`ip`, `os`, `client`)
) COMMENT 'Views statistics per user' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `client` ON `seo__visitors` (`client`);

CREATE INDEX `first` ON `seo__visitors` (`first`);

CREATE INDEX `os` ON `seo__visitors` (`os`);
