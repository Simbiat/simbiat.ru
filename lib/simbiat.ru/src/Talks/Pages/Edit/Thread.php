<?php
declare(strict_types=1);
namespace Simbiat\Talks\Pages\Edit;

class Thread extends \Simbiat\Talks\Pages\Thread
{
    #Sub service name
    protected string $subServiceName = 'thread';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Talks';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Talks';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Talks';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $requiredPermission = ['viewPosts'];
    #Flag to indicate editor mode
    protected bool $editMode = true;
}
