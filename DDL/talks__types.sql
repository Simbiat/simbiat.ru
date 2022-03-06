create table talks__types
(
    typeid      tinyint unsigned auto_increment comment 'Unique ID',
    type        varchar(25)  not null comment 'Type name',
    description varchar(100) null comment 'Description of the type',
    icon        varchar(50)  null comment 'Name of the default icon file',
    constraint typeid
        unique (typeid)
)
    comment 'Types of forums';

