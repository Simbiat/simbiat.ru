<?php
declare(strict_types=1);
namespace Simbiat\Talks\Pages\Edit;

class User extends \Simbiat\Talks\Pages\User
{
    #Sub service name
    protected string $subServiceName = 'user';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'User profile';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'User profile';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'User profile';
    #Flag to indicate editor mode
    protected bool $editMode = true;
}
