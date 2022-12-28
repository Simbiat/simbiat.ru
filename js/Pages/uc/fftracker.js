export class EditFFLinks {
    form = null;
    constructor() {
        this.form = document.getElementById('ff_link_user');
        if (this.form) {
            submitIntercept(this.form, this.link.bind(this));
        }
    }
    link() {
        let formData = new FormData(this.form);
        let button = this.form.querySelector('#ff_link_submit');
        buttonToggle(button);
        ajax(location.protocol + '//' + location.host + '/api/uc/fflink/', formData, 'json', 'POST', 60000, true).then(data => {
            if (data.data === true) {
                new Snackbar('Character linked successfully. Reloading page...', 'success');
                window.location.href = window.location.href + '?forceReload=true';
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(button);
        });
    }
}
//# sourceMappingURL=fftracker.js.map