create table seo__pageviews
(
    page    varchar(256)                                not null comment 'Page URL',
    referer varchar(256)    default ''                  not null comment 'Value of Referer HTTP header',
    ip      varchar(45)     default ''                  not null comment 'IP of unique visitor',
    os      varchar(100)    default ''                  not null comment 'OS version used by visitor',
    client  varchar(100)    default ''                  not null comment 'Client version used by visitor',
    first   timestamp       default current_timestamp() not null comment 'Time of first visit',
    last    timestamp       default current_timestamp() not null on update current_timestamp() comment 'Time of last visit',
    views   bigint unsigned default 1                   not null comment 'Number of views',
    primary key (page, referer, ip, os, client)
)
    comment 'Views statistics per page';

create index client
    on seo__pageviews (client);

create index first
    on seo__pageviews (first);

create index ip
    on seo__pageviews (ip);

create index os
    on seo__pageviews (os);

create index referer
    on seo__pageviews (referer);

