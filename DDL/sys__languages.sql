create table sys__languages
(
    tag  varchar(35)  not null comment 'Language tag as per RFC 5646',
    name varchar(100) not null comment 'Human-readable name',
    constraint name
        unique (name),
    constraint tag
        unique (tag)
)
    comment 'List of language tags';

