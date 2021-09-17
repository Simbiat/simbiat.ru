<?php
declare(strict_types=1);
namespace Simbiat\usercontrol;

use Simbiat\Database\Controller;

class Signinup
{
    #Attach common settings
    use Common;

    public function __construct()
    {
        #Cache DB controller, if not done already
        if (self::$dbController === NULL) {
            self::$dbController = new Controller;
        }
    }

    #Routing function for signing in or registering or reminding
    public function signinup(): bool
    {
        if (!empty($_POST['signinup'])) {
            #Processing is based on type, so if it's empty - something is wrong
            if (empty($_POST['signinup']['type'])) {
                return false;
            }
            switch ($_POST['signinup']['type']) {
                #Login
                case 'member':

                    break;
                #New user
                case 'newuser':

                    break;
                #Reminder
                case 'forget':

                    break;
                default:
                    return false;
            }
            return true;
        } else {
            return false;
        }
    }

    #Function to register user
    public function register(): bool
    {
        return true;
    }
}
