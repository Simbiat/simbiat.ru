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
        let spinner = document.getElementById('ff_link_spinner');
        spinner.classList.remove('hidden');
        ajax(location.protocol + '//' + location.host + '/api/uc/fflink/', formData, 'json', 'POST', 60000, true).then(data => {
            if (data.data === true) {
                location.reload();
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            spinner.classList.add('hidden');
        });
    }
}
//# sourceMappingURL=fftracker.js.map