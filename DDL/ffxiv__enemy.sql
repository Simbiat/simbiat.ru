create table ffxiv__enemy
(
    enemyid int unsigned auto_increment comment 'Internal ID of the enemy'
        primary key,
    name    varchar(50) not null comment 'Name of the enemy',
    constraint FFXIVEnemyName
        unique (name)
)
    comment 'List of some monsters, that are used for character ''deaths'', when they are marked as deleted';

