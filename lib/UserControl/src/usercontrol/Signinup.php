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

    #Function to generate registration/sign_in form.
    public function form(): string
    {
        #Open form
        $form = '<form role="form" id="signinup" name="signinup" autocomplete="on">';
        #Toggle for user login/registration/password reset
        $form .= '<div id="radio_signinup">
            <span>I am</span>
            <span class="radio_and_label">
                <input type="radio" id="radio_existuser" name="signinup[type]" value="member" checked>
                <label for="radio_existuser">member</label>
            </span>
            <span class="radio_and_label">
                <input type="radio" id="radio_newuser" name="signinup[type]" value="newuser">
                <label for="radio_newuser">new</label>
            </span>
            <span class="radio_and_label">
                <input type="radio" id="radio_forget" name="signinup[type]" value="forget">
                <label for="radio_forget">forgetful</label>
            </span>
        </div>';
        #Email
        $form .= '<div class="float_label_div">
            <input form="signinup" type="email" required aria-required="true" name="signinup[email]" id="signinup_email" placeholder="Email or name" autocomplete="username" inputmode="email" minlength="1" maxlength="320" pattern="^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$">
            <label for="signinup_email">Email or name</label>
        </div>';
        #Password
        $form .= '<div class="float_label_div">
            <input form="signinup" type="password" required aria-required="true" name="signinup[password]" id="signinup_password" placeholder="Password" autocomplete="current-password" inputmode="text" minlength="8" pattern=".{8,}">
            <label for="signinup_password">Password</label>
            <div class="showpassword" title="Show password"></div>
            <div id="password_req">Only password requirement: at least 8 symbols</div>
            <div class="pass_str_div" title="Strength of the password. Strong passwords are advisable.">Password strength:
                <span class="password_strength">weak</span>
            </div>
        </div>';
        #RememberMe checkbox
        $form .= '<div class="rememberme_div">
            <input role="checkbox" aria-checked="false" form="signinup" type="checkbox" name="signinup[rememberme]" id="rememberme">
            <label for="rememberme">Remember me</label>
        </div>';
        #Submit button
        $form .= '<input role="button" form="signinup" type="submit" name="signinup[submit]" id="signinup_submit" formaction="/'.$_SERVER['REQUEST_URI'].'" formmethod="post" formtarget="_self" value="Sign in/Join">';
        #Close form
        $form .= '</form>';
        return $form;
    }
}
