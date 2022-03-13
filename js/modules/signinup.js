/*globals ariaNation, submitIntercept, ajax, addSnackbar*/
/*exported signInUpInit, singInUpSubmit*/

function signInUpInit()
{
    //Show password functionality
    document.querySelectorAll('.showpassword').forEach(item => {
        item.addEventListener('click', showPassToggle);
    });
    //Register function for radio buttons toggling on login form
    document.querySelectorAll('#radio_signinup input[type=radio]').forEach(item => {
        item.addEventListener('change', loginRadioCheck);
    });
    //Force loginRadioCheck for consistency
    loginRadioCheck();
    //Intercept form submit
    submitIntercept('signinup');
}

function singInUpSubmit()
{
    //Get form data
    let formData = new FormData(document.getElementById('signinup'));
    let spinner = document.getElementById('singinup_spinner');
    spinner.classList.remove('hidden');
    ajax(location.protocol+'//'+location.host+'/api/uc/'+formData.get('signinup[type]')+'/', formData, 'json', 'POST', 60000, true).then(data => {
        if (data.data === true) {
            console.log('registered');
        } else {
            addSnackbar(data.reason, 'failure', 10000);
        }
        spinner.classList.add('hidden');
    });
}

//Regex for proper email. This is NOT JS Regex, thus it has doubled slashes.
const emailRegex = '[a-zA-Z0-9.!#$%&\'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*';
//Regex for username. This is NOT JS Regex, thus it has doubled slashes.
const userRegex = '[^\\/\\\\\\[\\]:;|=$%#@&\\(\\)\\{\\}!,+*?<>\\0\\t\\r\\n\\x00-\\x1F\\x7F\\x0b\\f\\x85\\v\\cY\\b]{1,64}';

//Show or hide password. Should be attached to .showpassword class to "mousedown" event
function showPassToggle(event)
{
    //Prevent focus stealing
    event.preventDefault();
    let eyeIcon = event.target;
    let passField = eyeIcon.parentElement.getElementsByTagName('input').item(0);
    if (passField.type === 'password') {
        passField.type = 'text';
        eyeIcon.title = 'Hide password';
    } else {
        passField.type = 'password';
        eyeIcon.title = 'Show password';
    }
}

//Password strength check. Purely as advise, nothing more.
function passwordStrengthOnEvent(event)
{
    //Attempt to get extra values to check against

    //Get element where we will be showing strength
    let strengthField = event.target.parentElement.querySelectorAll('.password_strength').item(0);
    //Get strength
    let strength = passwordStrength(event.target.value);
    //Set text
    strengthField.innerHTML = strength;
    //Remove classes
    strengthField.classList.remove('password_weak', 'password_medium', 'password_strong', 'password_very_strong');
    //Add class
    if (strength === 'very strong') {
        strengthField.classList.add('password_very_strong');
    } else {
        strengthField.classList.add('password_'+strength);
    }
}

//Actual check
function passwordStrength(password, extras = [])
{
    //Assigning points for the password
    let points = 0;
    //Check that it's long enough
    if (/.{8,}/u.test(password) === true) {
        points++;
    }
    //Add one more point, if it's twice as long as minimum requirement
    if (/.{16,}/u.test(password) === true) {
        points++;
    }
    //Add one more point, if it's 3 times as long as minimum requirement
    if (/.{32,}/u.test(password) === true) {
        points++;
    }
    //Add one more point, if it's 64 characters or more
    if (/.{64,}/u.test(password) === true) {
        points++;
    }
    //Check for lower case letters
    if (/\p{Ll}/u.test(password) === true) {// jshint ignore:line
        points++;
    }
    //Check for upper case letters
    if (/\p{Lu}/u.test(password) === true) {// jshint ignore:line
        points++;
    }
    //Check for letters without case (glyphs)
    if (/\p{Lo}/u.test(password) === true) {// jshint ignore:line
        points++;
    }
    //Check for numbers
    if (/\p{N}/u.test(password) === true) {// jshint ignore:line
        points++;
    }
    //Check for punctuation
    if (/[\p{P}\p{S}]/u.test(password) === true) {// jshint ignore:line
        points++;
    }
    //Reduce point for repeating characters
    if (/(.)\1{2,}/u.test(password) === true) {
        points--;
    }
    //Check against extra values. If password contains any of them - reduce points
    if (extras !== []) {

    }
    //Return value based on points. Note, that order is important.
    if (points <= 2) {
        return 'weak';
    } else if (2 < points && points < 5) {
        return 'medium';
    } else if (5 < points && points < 9) {
        return 'very strong';
    } else if (points === 5) {
        return 'strong';
    }
}

//Handle some adjustments when using radio-button switch
function loginRadioCheck()
{
    //Assign actual elements to variables
    let existUser = document.getElementById('radio_existuser');
    let newUser = document.getElementById('radio_newuser');
    let forget = document.getElementById('radio_forget');
    let login = document.getElementById('signinup_email');
    let loginLabel = login.labels[0];
    let password = document.getElementById('signinup_password');
    let button = document.getElementById('signinup_submit');
    let rememberme = document.getElementById('rememberme');
    let username = document.getElementById('signinup_username');
    //Adjust elements based on the toggle
    if (existUser && existUser.checked === true) {
        //Whether password field is required
        password.required = true;
        //Autocomplete suggestion for password
        password.setAttribute('autocomplete', 'current-password');
        //Autocomplete suggestion for login
        login.setAttribute('type', 'text');
        login.setAttribute('autocomplete', 'username');
        //Set pattern for login
        login.setAttribute('pattern', '^('+userRegex+')|('+emailRegex+')$');
        //Enforce minimum length for password
        password.setAttribute('minlength', '8');
        //Adjust name of the button
        button.value = 'Sign in';
        //Add or remove listeners for password strength
        ['focus', 'change', 'input',].forEach(function(e) {
            password.removeEventListener(e, passwordStrengthOnEvent);
        });
        //Show or hide password field
        password.parentElement.classList.remove('hidden');
        //Show or hide remember me checkbox
        rememberme.parentElement.classList.remove('hidden');
        //Show or hide password requirements
        document.getElementById('password_req').classList.add('hidden');
        document.querySelectorAll('.pass_str_div').item(0).classList.add('hidden');
        //Hide username field
        username.parentElement.classList.add('hidden');
        username.required = false;
    }
    if (newUser && newUser.checked === true) {
        password.required = true;
        password.setAttribute('autocomplete', 'new-password');
        login.setAttribute('autocomplete', 'email');
        login.setAttribute('pattern', '^'+emailRegex+'$');
        password.setAttribute('minlength', '8');
        button.value = 'Join';
        ['focus', 'change', 'input',].forEach(function(e) {
            password.addEventListener(e, passwordStrengthOnEvent);
        });
        password.parentElement.classList.remove('hidden');
        rememberme.parentElement.classList.remove('hidden');
        document.getElementById('password_req').classList.remove('hidden');
        document.querySelectorAll('.pass_str_div').item(0).classList.remove('hidden');
        login.placeholder = 'Email';
        loginLabel.innerHTML = 'Email';
        //Show username field
        username.parentElement.classList.remove('hidden');
        username.required = true;
    }
    if (forget && forget.checked === true) {
        password.required = false;
        password.removeAttribute('autocomplete');
        login.setAttribute('type', 'text');
        login.setAttribute('autocomplete', 'username');
        login.setAttribute('pattern', '^('+userRegex+')|('+emailRegex+')$');
        password.removeAttribute('minlength');
        button.value = 'Remind';
        ['focus', 'change', 'input',].forEach(function(e) {
            password.removeEventListener(e, passwordStrengthOnEvent);
        });
        password.parentElement.classList.add('hidden');
        rememberme.parentElement.classList.add('hidden');
        document.getElementById('password_req').classList.add('hidden');
        document.querySelectorAll('.pass_str_div').item(0).classList.add('hidden');
        //Additionally uncheck rememberme as precaution
        rememberme.checked = false;
        login.placeholder = 'Email or name';
        loginLabel.innerHTML = 'Email or name';
        //Hide username field
        username.parentElement.classList.add('hidden');
        username.required = false;
    }
    //Adjust Aria values
    if (password) {
        ariaNation(password);
    }
}
