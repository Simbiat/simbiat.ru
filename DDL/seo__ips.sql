create table seo__ips
(
    ip      varchar(45)  not null comment 'IP address'
        primary key,
    country varchar(60)  not null comment 'Country name',
    city    varchar(200) not null comment 'City name'
)
    comment 'IP to country and city based on ipinfo.io';

