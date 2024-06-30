<?php
declare(strict_types=1);
namespace Simbiat\Website\Talks\Pages\Edit;

#This class is more for the sake of segregation of section edit mode, which allows higher-level overview of section's contents
class Section extends \Simbiat\Website\Talks\Pages\Section
{
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $requiredPermission = ['editSections', 'addSections', 'removeSections'];
    #Flag to indicate editor mode
    protected bool $editMode = true;
}
