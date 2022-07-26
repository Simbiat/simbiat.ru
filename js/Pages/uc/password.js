export class PasswordChange {
    form = null;
    constructor() {
        this.form = document.getElementById('password_change');
        if (this.form) {
            submitIntercept(this.form, this.change.bind(this));
        }
    }
    change() {
        let formData = new FormData(this.form);
        let spinner = document.getElementById('pw_change_spinner');
        spinner.classList.remove('hidden');
        ajax(location.protocol + '//' + location.host + '/api/uc/password/', formData, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                new Snackbar('Password changed', 'success');
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            spinner.classList.add('hidden');
        });
    }
}
//# sourceMappingURL=password.js.map