export class PasswordChange {
    form = null;
    button = null;
    constructor() {
        this.form = document.querySelector('#password_change');
        if (this.form) {
            submitIntercept(this.form, this.change.bind(this));
            this.button = this.form.querySelector('#password_submit');
        }
    }
    change() {
        if (this.form && this.button) {
            const formData = new FormData(this.form);
            buttonToggle(this.button);
            void ajax(`${location.protocol}//${location.host}/api/uc/password`, formData, 'json', 'PATCH', AJAX_TIMEOUT, true).then((response) => {
                const data = response;
                if (data.data === true) {
                    addSnackbar('Password changed', 'success');
                }
                else {
                    addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                }
                if (this.button) {
                    buttonToggle(this.button);
                }
            });
        }
    }
}
//# sourceMappingURL=password.js.map