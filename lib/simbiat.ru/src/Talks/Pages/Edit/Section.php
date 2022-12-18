<?php
declare(strict_types=1);
namespace Simbiat\Talks\Pages\Edit;

class Section extends \Simbiat\Talks\Pages\Section
{
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $requiredPermission = ['editSections', 'addSections', 'removeSections'];
    #Flag to indicate editor mode
    protected bool $editMode = true;
    #Link to JS module for preload
    protected string $jsModule = 'talks/edit/sections';
}
