export class EditFFLinks {
    form = null;
    button = null;
    constructor() {
        this.form = document.querySelector('#ff_link_user');
        if (this.form) {
            submitIntercept(this.form, this.link.bind(this));
            this.button = this.form.querySelector('#ff_link_submit');
        }
    }
    link() {
        if (this.form && this.button) {
            const formData = new FormData(this.form);
            buttonToggle(this.button);
            void ajax(`${location.protocol}//${location.host}/api/uc/fflink/`, formData, 'json', 'POST', 60000, true).then((response) => {
                const data = response;
                if (data.data === true) {
                    addSnackbar('Character linked successfully. Reloading page...', 'success');
                    pageRefresh();
                }
                else {
                    addSnackbar(data.reason, 'failure', 10000);
                }
                if (this.button) {
                    buttonToggle(this.button);
                }
            });
        }
    }
}
//# sourceMappingURL=fftracker.js.map