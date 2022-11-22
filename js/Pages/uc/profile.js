export class EditProfile {
    usernameForm = null;
    usernameSubmit = null;
    usernameField = null;
    profileForm = null;
    profileSubmit = null;
    profileFormData = '';
    timeOut = null;
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
    profile(auto = false) {
        let formData = new FormData(this.profileForm);
        let button = this.profileForm.querySelector('#details_submit');
        buttonToggle(button);
        ajax(location.protocol + '//' + location.host + '/api/uc/profile/', formData, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                this.profileFormData = JSON.stringify([...formData.entries()]);
                this.profileOnChange();
                new Snackbar('Profile updated', 'success');
                if (auto) {
                    let autoTime = document.getElementById('lastAutoSave');
                    autoTime.classList.remove('hidden');
                    let timeTag = autoTime.querySelector('time');
                    let time = new Date();
                    timeTag.setAttribute('datetime', time.toISOString());
                    timeTag.innerHTML = time.toLocaleTimeString();
                }
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(button);
        });
    }
    profileOnChange() {
        if (this.timeOut) {
            clearTimeout(this.timeOut);
        }
        let formData = new FormData(this.profileForm);
        this.profileSubmit.disabled = this.profileFormData === JSON.stringify([...formData.entries()]);
        if (!this.profileSubmit.disabled) {
            this.timeOut = setTimeout(() => { this.profile(true); }, 10000);
        }
    }
    usernameOnChange() {
        this.usernameSubmit.disabled = this.usernameField.getAttribute('data-original') === this.usernameField.value;
    }
    username() {
        let formData = new FormData(this.usernameForm);
        let button = this.usernameForm.querySelector('#username_submit');
        buttonToggle(button);
        ajax(location.protocol + '//' + location.host + '/api/uc/username/', formData, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                this.usernameField.setAttribute('data-original', this.usernameField.value);
                this.usernameOnChange();
                new Snackbar('Username changed', 'success');
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(button);
        });
    }
}
//# sourceMappingURL=profile.js.map