function ucInit(): void
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
    //Intercept forms submit
    submitIntercept('signinup');
    submitIntercept('addMailForm');
    submitIntercept('password_change');
    //Listener for mail activation buttons
    document.querySelectorAll('.mail_activation').forEach(item => {
        item.addEventListener('click', activationMail);
    });
    //Listener for mail subscription checkbox
    document.querySelectorAll('[id^=subscription_checkbox_]').forEach(item => {
        item.addEventListener('click', subscribeMail);
    });
    //Listener for mail activation buttons
    document.querySelectorAll('.mail_deletion').forEach(item => {
        item.addEventListener('click', deleteMail);
    });
    let new_password = document.getElementById('new_password') as HTMLElement;
    if (new_password) {
        ['focus', 'change', 'input',].forEach(function (e) {
            new_password.addEventListener(e, passwordStrengthOnEvent);
        });
    }
}

function addMail(): boolean | void
{
    let form = document.getElementById('addMailForm') as HTMLFormElement;
    //Get form data
    let formData = new FormData(form);
    if (!formData.get('email')) {
        new Snackbar('Please, enter a valid email address', 'failure');
        return false;
    }
    let email = String(formData.get('email'));
    let spinner = document.getElementById('addMail_spinner') as HTMLImageElement;
    spinner.classList.remove('hidden');
    ajax(location.protocol+'//'+location.host+'/api/uc/emails/add/', formData, 'json', 'POST', 60000, true).then(data => {
        if (data.data === true) {
            //Add row to table
            let row = (document.getElementById('emailsList') as HTMLTableElement).insertRow();
            row.classList.add('middle');
            let cell = row.insertCell();
            cell.innerHTML = email;
            cell = row.insertCell();
            cell.innerHTML ='<input type="button" value="Confirm" class="mail_activation" data-email="'+email+'" aria-invalid="false" placeholder="Confirm"><img class="hidden spinner inline" src="/img/spinner.svg" alt="Activating '+email+'..." data-tooltip="Activating '+email+'...">';
            cell = row.insertCell();
            cell.innerHTML ='Confirm address to change setting';
            cell.classList.add('warning');
            cell = row.insertCell();
            cell.innerHTML ='<td><input class="mail_deletion" data-email="'+email+'" type="image" src="/img/close.svg" alt="Delete '+email+'" aria-invalid="false" placeholder="image" data-tooltip="Delete '+email+'" tabindex="0"><img class="hidden spinner inline" src="/img/spinner.svg" alt="Removing '+email+'..." data-tooltip="Removing '+email+'...">';
            blockDeleteMail();
            form.reset();
            new Snackbar('Mail added', 'success');
        } else {
            new Snackbar(data.reason, 'failure', 10000);
        }
        spinner.classList.add('hidden');
    });
}

function deleteMail(event: Event): void
{
    let button = event.target as HTMLInputElement;
    let table = ((button.parentElement as HTMLTableCellElement).parentElement as HTMLTableRowElement).parentElement as HTMLTableElement;
    //Get row number
    let tr = ((button.parentElement as HTMLTableCellElement).parentElement as HTMLTableRowElement).rowIndex - 1;
    let spinner = (button.parentElement as HTMLTableCellElement).getElementsByClassName('spinner')[0] as HTMLImageElement;
    //Generate form data
    let formData = new FormData();
    formData.set('email', button.getAttribute('data-email') ?? '');
    spinner.classList.remove('hidden');
    ajax(location.protocol+'//'+location.host+'/api/uc/emails/delete/', formData, 'json', 'DELETE', 60000, true).then(data => {
        if (data.data === true) {
            table.deleteRow(tr);
            blockDeleteMail();
            new Snackbar('Mail removed', 'success');
        } else {
            new Snackbar(data.reason, 'failure', 10000);
        }
        spinner.classList.add('hidden');
    });
}

//Function to block button for mail removal if we have less than 2 confirmed mails
function blockDeleteMail(): void
{
    let confirmedMail = document.getElementsByClassName('mail_confirmed').length;
    document.querySelectorAll('.mail_deletion').forEach(item => {
        //Check if row is for confirmed mail
        if ((((item as HTMLInputElement).parentElement as HTMLTableCellElement).parentElement as HTMLTableRowElement).getElementsByClassName('mail_confirmed').length > 0) {
            (item as HTMLInputElement).disabled = confirmedMail < 2;
        } else {
            (item as HTMLInputElement).disabled = false;
        }
    });
}

function subscribeMail(event: Event): void
{
    event.preventDefault();
    event.stopPropagation();
    let checkbox = event.target as HTMLInputElement;
    //Get verb
    let verb;
    if (checkbox.checked) {
        verb = 'subscribe';
    } else {
        verb = 'unsubscribe';
    }
    let label = (checkbox.parentElement as HTMLDivElement).getElementsByTagName('label')[0] as HTMLLabelElement;
    let spinner = ((checkbox.parentElement as HTMLDivElement).parentElement as HTMLTableCellElement).getElementsByClassName('spinner')[0] as HTMLImageElement;
    //Generate form data
    let formData = new FormData();
    formData.set('verb', verb);
    formData.set('email', checkbox.getAttribute('data-email') ?? '');
    spinner.classList.remove('hidden');
    ajax(location.protocol+'//'+location.host+'/api/uc/emails/'+verb+'/', formData, 'json', 'PATCH', 60000, true).then(data => {
        if (data.data === true) {
            if (checkbox.checked) {
                checkbox.checked = false;
                label.innerText = 'Subscribe';
                new Snackbar('Email unsubscribed', 'success');
            } else {
                checkbox.checked = true;
                label.innerText = 'Unsubscribe';
                new Snackbar('Email subscribed', 'success');
            }
        } else {
            new Snackbar(data.reason, 'failure', 10000);
        }
        spinner.classList.add('hidden');
    });
}

function activationMail(event: Event): void
{
    let button = event.target as HTMLInputElement;
    let spinner = (button.parentElement as HTMLTableCellElement).getElementsByClassName('spinner')[0] as HTMLImageElement;
    //Generate form data
    let formData = new FormData();
    formData.set('verb', 'activate');
    formData.set('email', button.getAttribute('data-email') ?? '');
    spinner.classList.remove('hidden');
    ajax(location.protocol+'//'+location.host+'/api/uc/emails/activate/', formData, 'json', 'PATCH', 60000, true).then(data => {
        if (data.data === true) {
            new Snackbar('Activation email sent', 'success');
        } else {
            new Snackbar(data.reason, 'failure', 10000);
        }
        spinner.classList.add('hidden');
    });
}

function singInUpSubmit(): void
{
    //Get form data
    let formData = new FormData(document.getElementById('signinup') as HTMLFormElement);
    if (!formData.get('signinup[type]')) {
        formData.set('signinup[type]', 'logout');
    }
    let spinner = document.getElementById('singinup_spinner') as HTMLImageElement;
    spinner.classList.remove('hidden');
    ajax(location.protocol+'//'+location.host+'/api/uc/signinup/'+formData.get('signinup[type]')+'/', formData, 'json', 'POST', 60000, true).then(data => {
        if (data.data === true) {
            if (formData.get('signinup[type]') === 'remind') {
                new Snackbar('If respective account is registered an email has been sent with password reset link.', 'success');
            } else {
                location.reload();
            }
        } else {
            new Snackbar(data.reason, 'failure', 10000);
        }
        spinner.classList.add('hidden');
    });
}

function passwordChange(): void
{
    //Get form data
    let formData = new FormData(document.getElementById('password_change') as HTMLFormElement);
    let spinner = document.getElementById('pw_change_spinner') as HTMLImageElement;
    spinner.classList.remove('hidden');
    ajax(location.protocol+'//'+location.host+'/api/uc/password/', formData, 'json', 'PATCH', 60000, true).then(data => {
        if (data.data === true) {
            new Snackbar('Password changed', 'success');
        } else {
            new Snackbar(data.reason, 'failure', 10000);
        }
        spinner.classList.add('hidden');
    });
}

//Regex for proper email. This is NOT JS Regex, thus it has doubled slashes.
const emailRegex = '[a-zA-Z0-9.!#$%&\'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*';
//Regex for username. This is NOT JS Regex, thus it has doubled slashes.
const userRegex = '[^\\/\\\\\\[\\]:;|=$%#@&\\(\\)\\{\\}!,+*?<>\\0\\t\\r\\n\\x00-\\x1F\\x7F\\x0b\\f\\x85\\v\\cY\\b]{1,64}';

//Show or hide password. Should be attached to .showpassword class to "mousedown" event
function showPassToggle(event: Event): void
{
    //Prevent focus stealing
    event.preventDefault();
    let eyeIcon = event.target as HTMLDivElement;
    let passField = (eyeIcon.parentElement as HTMLDivElement).getElementsByTagName('input').item(0) as HTMLInputElement;
    if (passField.type === 'password') {
        passField.type = 'text';
        eyeIcon.title = 'Hide password';
    } else {
        passField.type = 'password';
        eyeIcon.title = 'Show password';
    }
}

//Password strength check. Purely as advise, nothing more.
function passwordStrengthOnEvent(event: Event): void
{
    //Get element where we will be showing strength
    let strengthField = document.querySelectorAll('.password_strength').item(0) as HTMLSpanElement;
    //Get strength
    let strength = passwordStrength((event.target as HTMLInputElement).value);
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
function passwordStrength(password: string): string
{
    //Assigning points for the password
    let points = 0;
    //Check that it's long enough
    if (/.{8,}/u.test(password)) {
        points++;
    }
    //Add one more point, if it's twice as long as minimum requirement
    if (/.{16,}/u.test(password)) {
        points++;
    }
    //Add one more point, if it's 3 times as long as minimum requirement
    if (/.{32,}/u.test(password)) {
        points++;
    }
    //Add one more point, if it's 64 characters or more
    if (/.{64,}/u.test(password)) {
        points++;
    }
    //Check for lower case letters
    if (/\p{Ll}/u.test(password)) {
        points++;
    }
    //Check for upper case letters
    if (/\p{Lu}/u.test(password)) {
        points++;
    }
    //Check for letters without case (glyphs)
    if (/\p{Lo}/u.test(password)) {
        points++;
    }
    //Check for numbers
    if (/\p{N}/u.test(password)) {
        points++;
    }
    //Check for punctuation
    if (/[\p{P}\p{S}]/u.test(password)) {
        points++;
    }
    //Reduce point for repeating characters
    if (/(.)\1{2,}/u.test(password)) {
        points--;
    }
    //Return value based on points. Note, that order is important.
    if (points <= 2) {
        return 'weak';
    } else if (2 < points && points < 5) {
        return 'medium';
    } else if (points === 5) {
        return 'strong';
    } else {
        return 'very strong';
    }
}

//Handle some adjustments when using radio-button switch
function loginRadioCheck(): void
{
    //Assign actual elements to variables
    let existUser = document.getElementById('radio_existuser') as HTMLInputElement;
    let newUser = document.getElementById('radio_newuser') as HTMLInputElement;
    let forget = document.getElementById('radio_forget') as HTMLInputElement;
    let login = document.getElementById('signinup_email') as HTMLInputElement;
    let loginLabel;
    if (login && login.labels) {
        loginLabel = login.labels[0];
    }
    let password = document.getElementById('signinup_password') as HTMLInputElement;
    let button = document.getElementById('signinup_submit') as HTMLInputElement;
    let rememberme = document.getElementById('rememberme') as HTMLInputElement;
    let username = document.getElementById('signinup_username') as HTMLInputElement;
    //Adjust elements based on the toggle
    if (existUser && existUser.checked) {
        //Whether password field is required
        password.required = true;
        //Autocomplete suggestion for password
        password.setAttribute('autocomplete', 'current-password');
        //Autocomplete suggestion for login
        login.setAttribute('type', 'email');
        login.setAttribute('autocomplete', 'email');
        //Set pattern for login
        login.setAttribute('pattern', '^'+emailRegex+'$');
        //Enforce minimum length for password
        password.setAttribute('minlength', '8');
        //Adjust name of the button
        button.value = 'Sign in';
        //Add or remove listeners for password strength
        ['focus', 'change', 'input',].forEach(function(e) {
            password.removeEventListener(e, passwordStrengthOnEvent);
        });
        //Show or hide password field
        (password.parentElement as HTMLDivElement).classList.remove('hidden');
        //Show or hide remember me checkbox
        (rememberme.parentElement as HTMLDivElement).classList.remove('hidden');
        //Show or hide password requirements
        (document.getElementById('password_req') as HTMLDivElement).classList.add('hidden');
        document.querySelectorAll('.pass_str_div').item(0).classList.add('hidden');
        //Hide username field
        (username.parentElement as HTMLDivElement).classList.add('hidden');
        username.required = false;
    }
    if (newUser && newUser.checked) {
        password.required = true;
        password.setAttribute('autocomplete', 'new-password');
        login.setAttribute('type', 'email');
        login.setAttribute('autocomplete', 'email');
        login.setAttribute('pattern', '^'+emailRegex+'$');
        password.setAttribute('minlength', '8');
        button.value = 'Join';
        ['focus', 'change', 'input',].forEach(function(e) {
            password.addEventListener(e, passwordStrengthOnEvent);
        });
        (password.parentElement as HTMLDivElement).classList.remove('hidden');
        (rememberme.parentElement as HTMLDivElement).classList.remove('hidden');
        (document.getElementById('password_req') as HTMLDivElement).classList.remove('hidden');
        document.querySelectorAll('.pass_str_div').item(0).classList.remove('hidden');
        login.placeholder = 'Email';
        if (loginLabel) {
            loginLabel.innerHTML = 'Email';
        }
        //Show username field
        (username.parentElement as HTMLDivElement).classList.remove('hidden');
        username.required = true;
    }
    if (forget && forget.checked) {
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
        (password.parentElement as HTMLDivElement).classList.add('hidden');
        (rememberme.parentElement as HTMLDivElement).classList.add('hidden');
        (document.getElementById('password_req') as HTMLDivElement).classList.add('hidden');
        document.querySelectorAll('.pass_str_div').item(0).classList.add('hidden');
        //Additionally uncheck rememberme as precaution
        rememberme.checked = false;
        login.placeholder = 'Email or name';
        if (loginLabel) {
            loginLabel.innerHTML = 'Email or name';
        }
        //Hide username field
        (username.parentElement as HTMLDivElement).classList.add('hidden');
        username.required = false;
    }
    //Adjust Aria values
    if (password) {
        ariaNation(password);
    }
}
