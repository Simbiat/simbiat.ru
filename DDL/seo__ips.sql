CREATE TABLE `seo__ips`
(
    `ip`      VARCHAR(45) COLLATE utf8mb4_uca1400_nopad_as_ci  NOT NULL COMMENT 'IP address' PRIMARY KEY,
    `country` VARCHAR(60) COLLATE utf8mb4_uca1400_nopad_ai_ci  NOT NULL COMMENT 'Country name',
    `city`    VARCHAR(200) COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'City name'
) COMMENT 'IP to country and city based on ipinfo.io' `PAGE_COMPRESSED` = 'ON';
