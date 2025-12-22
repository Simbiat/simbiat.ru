export class Contacts {
    add_thread_form = null;
    constructor() {
        this.add_thread_form = document.querySelector('#add_thread_form');
        if (this.add_thread_form) {
            submitIntercept(this.add_thread_form, this.addThread.bind(this));
        }
    }
    addThread() {
        if (this.add_thread_form) {
            const button = this.add_thread_form.querySelector('input[type=submit]');
            const form_data = new FormData(this.add_thread_form);
            form_data.append('new_thread[timezone]', TIMEZONE);
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/contact`, form_data, 'json', 'POST', AJAX_TIMEOUT, true)
                .then((response) => {
                const data = response;
                if (data.data === true) {
                    if (this.add_thread_form) {
                        const textarea = this.add_thread_form.querySelector('textarea');
                        if (textarea && !empty(textarea.id)) {
                            saveTinyMCE(textarea.id);
                        }
                    }
                    addSnackbar('Thread created. Reloading...', 'success');
                    pageRefresh(data.location);
                }
                else {
                    if (data.location) {
                        addSnackbar(data.reason + ` View the thread <a href="${data.location}" target="_blank" rel="noopener noreferrer">here</a>.`, 'failure', 0);
                    }
                    else {
                        addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                    }
                }
                buttonToggle(button);
            });
        }
    }
}
//# sourceMappingURL=contacts.js.map