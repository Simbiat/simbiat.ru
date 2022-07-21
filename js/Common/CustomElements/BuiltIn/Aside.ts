class Aside
{
    private static _instance: null | Aside = null;
    private sidebarDiv: HTMLElement | null = null;
    private readonly loginForm: HTMLFormElement | null = null;

    constructor()
    {
        if (Aside._instance) {
            return Aside._instance;
        }
        this.sidebarDiv = (document.getElementById('sidebar') as HTMLElement);
        //Listeners for click event to show or hide sidebar
        (document.getElementById('showSidebar') as HTMLDivElement).addEventListener('click', () => {(this.sidebarDiv as HTMLElement).classList.add('shown')});
        (document.getElementById('hideSidebar') as HTMLDivElement).addEventListener('click', () => {(this.sidebarDiv as HTMLElement).classList.remove('shown')});
        //Login form
        this.loginForm = (document.getElementById('signinup') as HTMLFormElement);
        if (this.loginForm) {
            //Register function for radio buttons toggling on login form
            this.loginForm.querySelectorAll('#radio_signinup input[type=radio]').forEach(item => {
                item.addEventListener('change', this.loginRadioCheck);
            });
            //Force loginRadioCheck for consistency
            this.loginRadioCheck();
            submitIntercept(this.loginForm, this.singInUpSubmit.bind(this));
        }
        Aside._instance = this;
    }

    public singInUpSubmit(): void
    {
        if (this.loginForm) {
            //Get form data
            let formData = new FormData(this.loginForm);
            if (!formData.get('signinup[type]')) {
                formData.set('signinup[type]', 'logout');
            }
            let spinner = document.getElementById('signinup_spinner') as HTMLImageElement;
            spinner.classList.remove('hidden');
            ajax(location.protocol + '//' + location.host + '/api/uc/signinup/' + formData.get('signinup[type]') + '/', formData, 'json', 'POST', 60000, true).then(data => {
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
    }

    //Handle some adjustments when using radio-button switch
    public loginRadioCheck(): void
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
            //Show or hide password field
            (password.parentElement as HTMLDivElement).classList.remove('hidden');
            //Show or hide remember me checkbox
            (rememberme.parentElement as HTMLDivElement).classList.remove('hidden');
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
            (password.parentElement as HTMLDivElement).classList.remove('hidden');
            (rememberme.parentElement as HTMLDivElement).classList.remove('hidden');
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
            (password.parentElement as HTMLDivElement).classList.add('hidden');
            (rememberme.parentElement as HTMLDivElement).classList.add('hidden');
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
            new Input().ariaNation(password);
        }
    }
}
