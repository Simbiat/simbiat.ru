<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\Talks\Edit;

#This class is more for the sake of segregation of section edit mode, which allows higher-level overview of section's contents
class Section extends \Simbiat\Website\Pages\Talks\Section
{
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $required_permission = ['edit_sections', 'add_sections', 'remove_sections'];
    #Flag to indicate editor mode
    protected bool $edit_mode = true;
}
