class LoginForm extends HTMLElement
{
    //Regex for username. This is NOT JS Regex, thus it has doubled slashes.
    private readonly userRegex = '^[\\p{L}\\d.!#$%&\'*+\\\\/=?^_`{|}~\\- ]{1,64}$';
    //Regex for proper email. This is NOT JS Regex, thus it has doubled slashes.
    private readonly emailRegex = '[\\p{L}\\d.!#$%&\'*+\\/=?^_`{|}~-]+@[a-zA-Z\\d](?:[a-zA-Z\\d\\-]{0,61}[a-zA-Z\\d])?(?:\\.[a-zA-Z\\d](?:[a-zA-Z\\d\\-]{0,61}[a-zA-Z\\d])?)*';
    private readonly loginForm: HTMLFormElement | null = null;
    //Sub-elements
    private readonly existUser: HTMLInputElement | null = null;
    private readonly newUser: HTMLInputElement | null = null;
    private readonly forget: HTMLInputElement | null = null;
    private readonly login: HTMLInputElement | null = null;
    private readonly password: HTMLInputElement | null = null;
    private readonly button: HTMLInputElement | null = null;
    private readonly rememberme: HTMLInputElement | null = null;
    private readonly username: HTMLInputElement | null = null;
    
    public constructor() {
        super();
        //Login form
        this.loginForm = document.querySelector('#signinup');
        if (this.loginForm) {
            //Assign actual elements to variables
            this.existUser = document.querySelector('#radio_existuser');
            this.newUser = document.querySelector('#radio_newuser');
            this.forget = document.querySelector('#radio_forget');
            this.login = document.querySelector('#signinup_email');
            this.password = document.querySelector('#signinup_password');
            this.button = document.querySelector('#signinup_submit');
            this.rememberme = document.querySelector('#rememberme');
            this.username = document.querySelector('#signinup_username');
            //Register function for radio buttons toggling on login form
            this.loginForm.querySelectorAll('#radio_signinup input[type=radio]').forEach((item) => {
                item.addEventListener('change', this.loginRadioCheck.bind(this));
            });
            //Force loginRadioCheck for consistency
            this.loginRadioCheck();
            submitIntercept(this.loginForm, this.singInUpSubmit.bind(this));
        }
    }
    
    private singInUpSubmit(): void
    {
        if (this.loginForm) {
            //Get form data
            const formData = new FormData(this.loginForm);
            if (empty(formData.get('signinup[type]'))) {
                formData.set('signinup[type]', 'logout');
            }
            formData.set('signinup[timezone]', Intl.DateTimeFormat().resolvedOptions().timeZone);
            const button = this.loginForm.querySelector('#signinup_submit');
            buttonToggle(button as HTMLInputElement);
            void ajax(`${location.protocol}//${location.host}/api/uc/${String(formData.get('signinup[type]'))}/`, formData, 'json', 'POST', 60000, true).then((response) => {
                const data = response as ajaxJSONResponse;
                if (data.data === true) {
                    if (formData.get('signinup[type]') === 'remind') {
                        addSnackbar('If respective account is registered an email has been sent with password reset link.', 'success');
                    } else {
                        pageRefresh();
                    }
                } else {
                    addSnackbar(data.reason, 'failure', 10000);
                }
                buttonToggle(button as HTMLInputElement);
            });
        }
    }
    
    //Handle some adjustments when using radio-button switch
    private loginRadioCheck(): void
    {
        if (this.login && this.password && this.button && this.rememberme && this.username) {
            let loginLabel;
            if (this.login.labels) {
                loginLabel = this.login.labels[0];
            }
            //Adjust elements based on the toggle
            if ((this.existUser?.checked) === true) {
                //Whether password field is required
                this.password.required = true;
                //Autocomplete suggestion for password
                this.password.setAttribute('autocomplete', 'current-password');
                //Autocomplete suggestion for login
                this.login.setAttribute('type', 'email');
                this.login.setAttribute('autocomplete', 'email');
                //Set pattern for login
                this.login.setAttribute('pattern', `^${this.emailRegex}$`);
                //Enforce minimum length for password
                this.password.setAttribute('minlength', '8');
                //Adjust name of the button
                this.button.value = 'Sign in';
                //Show or hide password field
                (this.password.parentElement as HTMLDivElement).classList.remove('hidden');
                //Show or hide remember me checkbox
                (this.rememberme.parentElement as HTMLDivElement).classList.remove('hidden');
                //Hide username field
                (this.username.parentElement as HTMLDivElement).classList.add('hidden');
                this.username.required = false;
            }
            if ((this.newUser?.checked) === true) {
                this.password.required = true;
                this.password.setAttribute('autocomplete', 'new-password');
                this.login.setAttribute('type', 'email');
                this.login.setAttribute('autocomplete', 'email');
                this.login.setAttribute('pattern', `^${this.emailRegex}$`);
                this.password.setAttribute('minlength', '8');
                this.button.value = 'Join';
                (this.password.parentElement as HTMLDivElement).classList.remove('hidden');
                (this.rememberme.parentElement as HTMLDivElement).classList.remove('hidden');
                this.login.placeholder = 'Email';
                if (loginLabel) {
                    loginLabel.innerHTML = 'Email';
                }
                //Show username field
                (this.username.parentElement as HTMLDivElement).classList.remove('hidden');
                this.username.required = true;
            }
            if ((this.forget?.checked) === true) {
                this.password.required = false;
                this.password.removeAttribute('autocomplete');
                this.login.setAttribute('type', 'text');
                this.login.setAttribute('autocomplete', 'username');
                this.login.setAttribute('pattern', `^(${this.userRegex})|(${this.emailRegex})$`);
                this.password.removeAttribute('minlength');
                this.button.value = 'Remind';
                (this.password.parentElement as HTMLDivElement).classList.add('hidden');
                (this.rememberme.parentElement as HTMLDivElement).classList.add('hidden');
                //Additionally uncheck rememberme as precaution
                this.rememberme.checked = false;
                this.login.placeholder = 'Email or name';
                if (loginLabel) {
                    loginLabel.innerHTML = 'Email or name';
                }
                //Hide username field
                (this.username.parentElement as HTMLDivElement).classList.add('hidden');
                this.username.required = false;
            }
            //Adjust Aria values
            ariaNation(this.password);
        }
    }
}
