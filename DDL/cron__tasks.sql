create table cron__tasks
(
    task           varchar(100)              not null comment 'Function''s internal ID'
        primary key,
    function       varchar(255)              not null comment 'Actual function reference, that will be called by Cron processor',
    object         varchar(255)              null comment 'Optional object',
    parameters     varchar(5000)             null comment 'Optional parameters used on initial object creation in JSON string',
    allowedreturns varchar(5000)             null comment 'Optional allowed return values to be treated as ''true'' by Cron processor in JSON string',
    maxTime        int unsigned default 3600 not null comment 'Maximum time allowed for the task to run. If exceeded, it will be terminated by PHP.',
    description    varchar(1000)             null comment 'Description of the task'
);

