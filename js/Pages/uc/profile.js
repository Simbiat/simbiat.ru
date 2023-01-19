export class EditProfile {
    usernameForm = null;
    usernameSubmit = null;
    usernameField = null;
    profileForm = null;
    profileSubmit = null;
    aboutValue = null;
    autoTime = null;
    timeTag = null;
    profileFormData = '';
    timeOut = null;
    constructor() {
        this.aboutValue = document.querySelector('#about_value');
        this.usernameForm = document.querySelector('#profile_username');
        this.autoTime = document.querySelector('#lastAutoSave');
        if (this.autoTime) {
            this.timeTag = this.autoTime.querySelector('time');
        }
        if (this.usernameForm) {
            this.usernameField = document.querySelector('#username_value');
            this.usernameSubmit = document.querySelector('#username_submit');
            ['focus', 'change', 'input',].forEach((eventType) => {
                if (this.usernameField) {
                    this.usernameField.addEventListener(eventType, this.usernameOnChange.bind(this));
                }
            });
            this.usernameOnChange();
            submitIntercept(this.usernameForm, this.username.bind(this));
        }
        this.profileForm = document.querySelector('#profile_details');
        if (this.profileForm) {
            this.profileSubmit = document.querySelector('#details_submit');
            this.profileFormData = JSON.stringify([...new FormData(this.profileForm).entries()]);
            this.profileOnChange();
            ['select', 'textarea', 'input',].forEach((elementType) => {
                if (this.profileForm) {
                    Array.from(this.profileForm.querySelectorAll(elementType)).forEach((element) => {
                        ['focus', 'change', 'input',].forEach((eventType) => {
                            element.addEventListener(eventType, this.profileOnChange.bind(this));
                        });
                    });
                }
            });
            submitIntercept(this.profileForm, this.profile.bind(this));
        }
    }
    profile(auto = false) {
        if (this.profileForm) {
            const formData = new FormData(this.profileForm);
            void ajax(`${location.protocol}//${location.host}/api/uc/profile/`, formData, 'json', 'PATCH', 60000, true).then((response) => {
                const data = response;
                if (data.data === true) {
                    this.profileFormData = JSON.stringify([...formData.entries()]);
                    this.profileOnChange();
                    addSnackbar('Profile updated', 'success');
                    if (auto) {
                        this.autoTime?.classList.remove('hidden');
                        if (this.timeTag) {
                            const time = new Date();
                            this.timeTag.setAttribute('datetime', time.toISOString());
                            this.timeTag.innerHTML = time.toLocaleTimeString();
                        }
                    }
                    if (this.aboutValue && !empty(this.aboutValue.id)) {
                        saveTinyMCE(this.aboutValue.id);
                    }
                }
                else {
                    addSnackbar(data.reason, 'failure', 10000);
                }
            });
        }
    }
    profileOnChange() {
        if (this.profileForm && this.profileSubmit) {
            if (this.timeOut !== null) {
                window.clearTimeout(this.timeOut);
            }
            const formData = new FormData(this.profileForm);
            this.profileSubmit.disabled = this.profileFormData === JSON.stringify([...formData.entries()]);
            if (!this.profileSubmit.disabled) {
                this.timeOut = window.setTimeout(() => {
                    this.profile(true);
                }, 10000);
            }
        }
    }
    usernameOnChange() {
        if (this.usernameField && this.usernameSubmit) {
            this.usernameSubmit.disabled = this.usernameField.getAttribute('data-original') === this.usernameField.value;
        }
    }
    username() {
        if (this.usernameForm && this.usernameSubmit) {
            const formData = new FormData(this.usernameForm);
            buttonToggle(this.usernameSubmit);
            void ajax(`${location.protocol}//${location.host}/api/uc/username/`, formData, 'json', 'PATCH', 60000, true).then((response) => {
                const data = response;
                if (data.data === true) {
                    this.usernameField.setAttribute('data-original', this.usernameField.value);
                    this.usernameOnChange();
                    addSnackbar('Username changed', 'success');
                }
                else {
                    addSnackbar(data.reason, 'failure', 10000);
                }
                if (this.usernameSubmit) {
                    buttonToggle(this.usernameSubmit);
                }
            });
        }
    }
}
//# sourceMappingURL=profile.js.map