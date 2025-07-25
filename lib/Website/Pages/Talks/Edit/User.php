<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\Talks\Edit;

class User extends \Simbiat\Website\Pages\Talks\User
{
    #Sub service name
    protected string $subservice_name = 'user';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'User profile';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'User profile';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'User profile';
    #Flag to indicate editor mode
    protected bool $edit_mode = true;
}
