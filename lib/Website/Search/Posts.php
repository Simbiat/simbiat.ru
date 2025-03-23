<?php
declare(strict_types = 1);

namespace Simbiat\Website\Search;
use Simbiat\Website\Abstracts\Search;

class Posts extends Search
{
    #Items to display per page for lists
    public int $listItems = 50;
    #Type of entity to return as static value in results (required for frontend routing)
    protected string $entityType = 'post';
    #Name of the table to search use
    protected string $table = 'talks__posts';
    #List of fields
    protected string $fields = '`talks__posts`.`postid` as `id`, `talks__posts`.`threadid`, `talks__threads`.`name`, `talks__types`.`type` as `detailedType`, `replyto`, `locked`, `talks__posts`.`system`, `talks__posts`.`created`, `talks__posts`.`createdby`, `talks__posts`.`updated`, `talks__posts`.`updatedby`, `text`, `for_creation`.`username` as `createdby_name`, `for_update`.`username` as `updatedby_name`, COALESCE((SELECT CONCAT(\'/assets/images/avatars/\', SUBSTRING(`sys__files`.`fileid`, 1, 2), \'/\', SUBSTRING(`sys__files`.`fileid`, 3, 2), \'/\', SUBSTRING(`sys__files`.`fileid`, 5, 2), \'/\', `sys__files`.`fileid`, \'.\', `sys__files`.`extension`) AS `avatar` FROM `uc__avatars` LEFT JOIN `sys__files` ON `uc__avatars`.`fileid`=`sys__files`.`fileid` WHERE `uc__avatars`.`userid`=`for_creation`.`userid` AND `uc__avatars`.`current`=1), \'/assets/images/avatar.svg\') AS `avatar`, COUNT(CASE WHEN `talks__likes`.`likevalue` < 0 THEN 1 END) as `dislikes`, COUNT(CASE WHEN `talks__likes`.`likevalue` > 0 THEN 1 END) as `likes`, COALESCE(MAX(CASE WHEN `talks__likes`.`userid` = :userid THEN `talks__likes`.`likevalue` END), 0) as `liked`';
    #Optional JOIN string, in case it's needed
    protected string $join = 'LEFT JOIN `uc__users` as `for_creation` ON `talks__posts`.`createdby`=`for_creation`.`userid` LEFT JOIN `uc__users` as `for_update` ON `talks__posts`.`updatedby`=`for_update`.`userid` INNER JOIN `talks__threads` ON `talks__posts`.`threadid`=`talks__threads`.`threadid` INNER JOIN `talks__sections` ON `talks__sections`.`sectionid`=`talks__threads`.`sectionid` INNER JOIN `talks__types` ON `talks__sections`.`type`=`talks__types`.`typeid` LEFT JOIN `talks__likes` ON `talks__posts`.`postid` = `talks__likes`.`postid`';
    #Optional GROUP BY
    protected string $groupBy = '`talks__posts`.`postid`';
    #Default order (for main page, for example)
    protected string $orderDefault = '`talks__posts`.`updated` DESC';
    #Order for list pages
    protected string $orderList = '`talks__posts`.`created` DESC';
    #Next 3 values are lists of columns to use in search. The order is important, since the higher in the list a field is,
    #the more weight/relevancy condition with it will have (if true)
    #List of FULLTEXT columns
    protected array $fulltext = [
        'text',
    ];
    #List of optional columns for direct comparison
    protected array $exact = [
        'text',
    ];
    #List of optional columns for LIKE %% comparison
    protected array $like = [
        'text',
    ];
}
