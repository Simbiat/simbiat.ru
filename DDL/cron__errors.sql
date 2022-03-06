create table cron__errors
(
    task      varchar(100) default ''''''              not null comment 'Optional task ID',
    arguments varchar(255) default ''''''              not null comment 'Optional task arguments',
    time      timestamp    default current_timestamp() not null on update current_timestamp() comment 'Time the error occured',
    text      text                                     not null comment 'Error for the text',
    primary key (task, arguments),
    constraint errors_to_arguments
        foreign key (arguments) references cron__schedule (arguments)
            on update cascade on delete cascade,
    constraint errors_to_tasks
        foreign key (task) references cron__tasks (task)
            on update cascade on delete cascade
);

create index time
    on cron__errors (time);

