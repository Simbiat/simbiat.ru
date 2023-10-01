CREATE TABLE `uc__users`
(
    `userid`     INT UNSIGNED AUTO_INCREMENT COMMENT 'User ID' PRIMARY KEY,
    `username`   VARCHAR(64) COLLATE utf8mb4_uca1400_nopad_as_ci                              NOT NULL COMMENT 'User''s username/login',
    `phone`      BIGINT(15) UNSIGNED                                                          NULL COMMENT 'User''s phone number in international format',
    `password`   TEXT                                                                         NOT NULL COMMENT 'Hashed password',
    `strikes`    TINYINT(2) UNSIGNED                             DEFAULT 0                    NOT NULL COMMENT 'Number of unsuccessful logins',
    `pw_reset`   TEXT                                                                         NULL COMMENT 'Password reset code',
    `api_key`    TEXT                                                                         NULL COMMENT 'API key',
    `ff_token`   VARCHAR(64)                                                                  NOT NULL COMMENT 'Token for linking FFXIV characters',
    `registered` DATETIME(6)                                     DEFAULT CURRENT_TIMESTAMP(6) NOT NULL COMMENT 'When user was registered',
    `updated`    DATETIME(6)                                     DEFAULT CURRENT_TIMESTAMP(6) NOT NULL ON UPDATE CURRENT_TIMESTAMP(6) COMMENT 'When user was updated',
    `parentid`   INT UNSIGNED                                                                 NULL COMMENT 'User ID, that added this one (if added manually)',
    `birthday`   DATE                                                                         NULL COMMENT 'User''s date of birth',
    `firstname`  VARCHAR(100) COLLATE utf8mb4_uca1400_nopad_ai_ci                             NULL COMMENT 'User''s first name',
    `lastname`   VARCHAR(100) COLLATE utf8mb4_uca1400_nopad_ai_ci                             NULL COMMENT 'User''s last/family name (also known as surname)',
    `middlename` VARCHAR(100) COLLATE utf8mb4_uca1400_nopad_ai_ci                             NULL COMMENT 'User''s middle name(s)',
    `fathername` VARCHAR(100) COLLATE utf8mb4_uca1400_nopad_ai_ci                             NULL COMMENT 'User''s patronymic or matronymic name (also known as father''s name or mother''s name)',
    `prefix`     VARCHAR(25) COLLATE utf8mb4_uca1400_nopad_ai_ci                              NULL COMMENT 'The prefix or title, such as "Mrs.", "Mr.", "Miss", "Ms.", "Dr.", or "Mlle."',
    `suffix`     VARCHAR(25) COLLATE utf8mb4_uca1400_nopad_ai_ci                              NULL COMMENT 'The suffix, such as "Jr.", "B.Sc.", "PhD.", "MBASW", or "IV"',
    `sex`        TINYINT(1) UNSIGNED                                                          NULL COMMENT 'User''s sex',
    `about`      VARCHAR(250) COLLATE utf8mb4_uca1400_nopad_ai_ci                             NULL COMMENT 'Introductory words from the user',
    `timezone`   VARCHAR(30) COLLATE utf8mb4_uca1400_nopad_ai_ci DEFAULT 'UTC'                NOT NULL COMMENT 'User''s timezone',
    `country`    VARCHAR(60) COLLATE utf8mb4_uca1400_nopad_ai_ci                              NULL COMMENT 'User''s country',
    `city`       VARCHAR(200) COLLATE utf8mb4_uca1400_nopad_ai_ci                             NULL COMMENT 'User''s city',
    `website`    VARCHAR(255) COLLATE utf8mb4_uca1400_nopad_as_ci                             NULL COMMENT 'User''s personal website',
    CONSTRAINT `api_key` UNIQUE (`api_key`) USING HASH,
    CONSTRAINT `ff_token` UNIQUE (`ff_token`),
    CONSTRAINT `username_unique` UNIQUE (`username`),
    CONSTRAINT `parent_to_user` FOREIGN KEY (`parentid`) REFERENCES `uc__users` (`userid`) ON UPDATE SET NULL ON DELETE SET NULL
) `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `birthday` ON `uc__users` (`birthday`);

CREATE INDEX `parentid` ON `uc__users` (`parentid`);

CREATE INDEX `registered` ON `uc__users` (`registered`);

CREATE INDEX `usergender` ON `uc__users` (`sex`);

CREATE FULLTEXT INDEX `username` ON `uc__users` (`username`);
