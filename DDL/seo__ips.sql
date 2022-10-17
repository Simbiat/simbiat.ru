CREATE TABLE `seo__ips`
(
    `ip`      VARCHAR(45)  NOT NULL COMMENT 'IP address' PRIMARY KEY,
    `country` VARCHAR(60)  NOT NULL COMMENT 'Country name',
    `city`    VARCHAR(200) NOT NULL COMMENT 'City name'
) COMMENT 'IP to country and city based on ipinfo.io';
