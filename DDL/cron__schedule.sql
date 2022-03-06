create table cron__schedule
(
    task        varchar(100)                                    not null comment 'Task ID',
    arguments   varchar(255)        default ''                  not null comment 'Optional arguments in JSON string',
    frequency   int unsigned        default 0                   not null comment 'Frequency to run a task in seconds',
    dayofmonth  varchar(255)                                    null comment 'Optional limit to run only on specific days of the month. Expects array of integers in JSON string.',
    dayofweek   varchar(60)                                     null comment 'Optional limit to run only on specific days of the week. Expects array of integers in JSON string.',
    priority    tinyint unsigned    default 0                   not null comment 'Priority of the task',
    message     varchar(100)                                    null comment 'Optional message, that will be shown if launched outside of CLI',
    status      tinyint(1) unsigned default 0                   not null comment 'Flag showing whether the job is running or not',
    runby       varchar(30)                                     null comment 'If not NULL, indicates, that a job is queued for a run by a process.',
    sse         tinyint(1) unsigned default 0                   not null comment 'Flag to indicate whether job is being ran by SSE call.',
    registered  timestamp           default current_timestamp() not null comment 'When the job was initially registered',
    updated     timestamp           default current_timestamp() not null comment 'When the job schedule was updated',
    nextrun     timestamp           default current_timestamp() not null comment 'Next expected time for the job to be run',
    lastrun     timestamp                                       null comment 'Time of the last run attempt',
    lastsuccess timestamp                                       null comment 'Time of the last successful run',
    lasterror   timestamp                                       null comment 'Time of the last error',
    primary key (task, arguments),
    constraint schedule_to_task
        foreign key (task) references cron__tasks (task)
            on update cascade on delete cascade
);

create index arguments
    on cron__schedule (arguments);

create index lastrun
    on cron__schedule (lastrun);

create index nextrun
    on cron__schedule (nextrun);

create index priority
    on cron__schedule (priority);

create index runby
    on cron__schedule (runby);

create index status
    on cron__schedule (status);

