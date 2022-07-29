export class EditProfile {
    usernameForm = null;
    usernameSubmit = null;
    usernameField = null;
    constructor() {
        this.usernameForm = document.getElementById('profile_username');
        if (this.usernameForm) {
            this.usernameField = document.getElementById('username_value');
            this.usernameSubmit = document.getElementById('username_submit');
            ['focus', 'change', 'input',].forEach((eventType) => {
                this.usernameField.addEventListener(eventType, this.usernameOnChange.bind(this));
            });
            this.usernameOnChange();
            submitIntercept(this.usernameForm, this.username.bind(this));
        }
    }
    usernameOnChange() {
        this.usernameSubmit.disabled = this.usernameField.getAttribute('data-original') === this.usernameField.value;
    }
    username() {
        let formData = new FormData(this.usernameForm);
        let spinner = document.getElementById('username_spinner');
        spinner.classList.remove('hidden');
        ajax(location.protocol + '//' + location.host + '/api/uc/username/', formData, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                this.usernameField.setAttribute('data-original', this.usernameField.value);
                this.usernameOnChange();
                new Snackbar('Username changed', 'success');
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            spinner.classList.add('hidden');
        });
    }
}
//# sourceMappingURL=profile.js.map