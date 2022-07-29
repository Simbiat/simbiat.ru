export class EditProfile {
    usernameForm = null;
    usernameSubmit = null;
    usernameField = null;
    profileForm = null;
    profileSubmit = null;
    profileFormData = '';
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
        this.profileForm = document.getElementById('profile_details');
        if (this.profileForm) {
            this.profileSubmit = document.getElementById('details_submit');
            this.profileFormData = JSON.stringify([...new FormData(this.profileForm).entries()]);
            this.profileOnChange();
            ['select', 'textarea', 'input',].forEach((elementType) => {
                Array.from(this.profileForm.getElementsByTagName(elementType)).forEach((element) => {
                    ['focus', 'change', 'input',].forEach((eventType) => {
                        element.addEventListener(eventType, this.profileOnChange.bind(this));
                    });
                });
            });
            submitIntercept(this.profileForm, this.profile.bind(this));
        }
    }
    profile() {
        let formData = new FormData(this.profileForm);
        let spinner = document.getElementById('details_spinner');
        spinner.classList.remove('hidden');
        ajax(location.protocol + '//' + location.host + '/api/uc/profile/', formData, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                this.profileFormData = JSON.stringify([...formData.entries()]);
                this.profileOnChange();
                new Snackbar('Profile updated', 'success');
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            spinner.classList.add('hidden');
        });
    }
    profileOnChange() {
        let formData = new FormData(this.profileForm);
        this.profileSubmit.disabled = this.profileFormData === JSON.stringify([...formData.entries()]);
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