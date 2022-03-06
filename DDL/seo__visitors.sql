create table seo__visitors
(
    ip     varchar(45)                                 not null comment 'IP of unique visitor',
    os     varchar(100)    default ''                  not null comment 'OS version used by visitor',
    client varchar(100)    default ''                  not null comment 'Client version used by visitor',
    first  timestamp       default current_timestamp() not null comment 'Time of first visit',
    last   timestamp       default current_timestamp() not null on update current_timestamp() comment 'Time of last visit',
    views  bigint unsigned default 1                   not null comment 'Number of viewed pages',
    primary key (ip, os, client)
)
    comment 'Views statistics per user';

create index client
    on seo__visitors (client);

create index first
    on seo__visitors (first);

create index os
    on seo__visitors (os);

