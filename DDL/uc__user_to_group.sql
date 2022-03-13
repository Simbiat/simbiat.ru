create table uc__user_to_group
(
    userid  int unsigned           not null comment 'User ID',
    groupid int unsigned default 2 not null comment 'Group ID',
    primary key (userid, groupid),
    constraint group_to_user
        foreign key (userid) references uc__users (userid)
            on update cascade on delete cascade,
    constraint groupid
        foreign key (groupid) references uc__groups (groupid)
            on update cascade on delete cascade
);

