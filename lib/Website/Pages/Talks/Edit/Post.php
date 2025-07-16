<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\Talks\Edit;

class Post extends \Simbiat\Website\Pages\Talks\Post
{
    #Sub service name
    protected string $subservice_name = 'post';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Talks';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Talks';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'Talks';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $required_permission = ['view_posts'];
    #Flag to indicate editor mode
    protected bool $edit_mode = true;
}
