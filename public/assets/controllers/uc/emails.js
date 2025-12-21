export class Emails {
    add_mail_form = null;
    submit = null;
    template = null;
    tbody = null;
    constructor() {
        this.add_mail_form = document.querySelector('#add_mail_form');
        this.template = document.querySelector('#email_row');
        this.tbody = document.querySelector('#emails_list tbody');
        if (this.add_mail_form) {
            this.submit = this.add_mail_form.querySelector('#add_mail_submit');
            submitIntercept(this.add_mail_form, this.add.bind(this));
            document.querySelectorAll('.mail_activation').forEach((item) => {
                item.addEventListener('click', (event) => {
                    Emails.activate(event.target);
                });
            });
            document.querySelectorAll('[id^=subscription_checkbox_]').forEach((item) => {
                item.addEventListener('click', (event) => {
                    Emails.subscribe(event);
                });
            });
            document.querySelectorAll('.mail_deletion').forEach((item) => {
                item.addEventListener('click', (event) => {
                    Emails.delete(event.target);
                });
            });
        }
    }
    add() {
        if (this.add_mail_form && this.submit) {
            const formData = new FormData(this.add_mail_form);
            if (empty(formData.get('email'))) {
                addSnackbar('Please, enter a valid email address', 'failure');
                return;
            }
            const email = String(formData.get('email'));
            buttonToggle(this.submit);
            void ajax(`${location.protocol}//${location.host}/api/uc/emails/add`, formData, 'json', 'POST', AJAX_TIMEOUT, true).then((response) => {
                const data = response;
                if (data.data === true) {
                    this.addRow(email);
                    Emails.blockDelete();
                    if (this.add_mail_form) {
                        this.add_mail_form.reset();
                    }
                    addSnackbar(`${email} added`, 'success');
                }
                else {
                    addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                }
                if (this.submit) {
                    buttonToggle(this.submit);
                }
            });
        }
    }
    addRow(email) {
        if (this.tbody && this.template) {
            const clone = this.template.content.cloneNode(true);
            const cells = clone.querySelectorAll('td');
            if (cells[0]) {
                cells[0].innerHTML = email;
            }
            if (cells[1]) {
                const inputElement1 = cells[1].querySelector('input');
                if (inputElement1) {
                    inputElement1.setAttribute('data-email', email);
                    inputElement1.addEventListener('click', (event) => {
                        Emails.activate(event.target);
                    });
                }
                const spinner1 = cells[1].querySelector('img');
                if (spinner1) {
                    spinner1.setAttribute('data-tooltip', String(spinner1.getAttribute('data-tooltip')).replace('email', email));
                    spinner1.setAttribute('alt', String(spinner1.getAttribute('alt')).replace('email', email));
                }
            }
            if (cells[3]) {
                const inputElement3 = cells[3].querySelector('input');
                if (inputElement3) {
                    inputElement3.setAttribute('data-email', email);
                    inputElement3.setAttribute('data-tooltip', String(inputElement3.getAttribute('data-tooltip')).replace('email', email));
                    inputElement3.setAttribute('alt', String(inputElement3.getAttribute('alt')).replace('email', email));
                    inputElement3.addEventListener('click', (event) => {
                        Emails.delete(event.target);
                    });
                }
                const spinner3 = cells[3].querySelector('img');
                if (spinner3) {
                    spinner3.setAttribute('data-tooltip', String(spinner3.getAttribute('data-tooltip')).replace('email', email));
                    spinner3.setAttribute('alt', String(spinner3.getAttribute('alt')).replace('email', email));
                }
            }
            this.tbody.appendChild(clone);
        }
    }
    static delete(button) {
        const formData = new FormData();
        const email = button.getAttribute('data-email') ?? '';
        formData.set('email', email);
        buttonToggle(button);
        void ajax(`${location.protocol}//${location.host}/api/uc/emails/delete`, formData, 'json', 'DELETE', AJAX_TIMEOUT, true).then((response) => {
            const data = response;
            if (data.data === true) {
                deleteRow(button);
                Emails.blockDelete();
                addSnackbar(`${email} removed`, 'success');
            }
            else {
                buttonToggle(button);
                addSnackbar(data.reason ?? 'Failed to delete email', 'failure', SNACKBAR_FAIL_LIFE);
            }
        });
    }
    static blockDelete() {
        const confirmedMail = document.querySelectorAll('.mail_confirmed').length;
        document.querySelectorAll('.mail_deletion').forEach((item) => {
            const input = item;
            const cell = input.parentElement;
            if (cell) {
                const row = cell.parentElement;
                if (row) {
                    if (row.querySelectorAll('.mail_confirmed').length > 0) {
                        input.disabled = confirmedMail < 2;
                    }
                    else {
                        input.disabled = false;
                        if (input.hasAttribute('data-tooltip') && input.getAttribute('data-tooltip') === 'Can\'t delete') {
                            const email = String(row.querySelector('td')?.innerHTML);
                            input.setAttribute('data-tooltip', `Delete ${email}`);
                            const spinner = cell.querySelector('.spinner');
                            spinner?.setAttribute('data-tooltip', `Removing ${email}...`);
                            spinner?.setAttribute('alt', `Removing ${email}...`);
                        }
                    }
                }
            }
        });
    }
    static subscribe(event) {
        event.preventDefault();
        event.stopPropagation();
        const checkbox = event.target;
        if (!checkbox) {
            return;
        }
        if (!checkbox.hasAttribute('data-email')) {
            return;
        }
        buttonToggle(checkbox);
        const email = String(checkbox.getAttribute('data-email'));
        const form_data = new FormData();
        form_data.set('email', email);
        form_data.set('verb', 'subscribe');
        void ajax(`${location.protocol}//${location.host}/api/uc/emails/subscribe`, form_data, 'json', 'PATCH', AJAX_TIMEOUT, true).then((response) => {
            const data = response;
            if (data.data === true) {
                checkbox.checked = true;
                addSnackbar(`${email} subscribed`, 'success');
            }
            else {
                addSnackbar(data.reason ?? 'Failed to subscribe', 'failure', SNACKBAR_FAIL_LIFE);
            }
            buttonToggle(checkbox);
        });
    }
    static activate(button) {
        const email = button.getAttribute('data-email') ?? '';
        const formData = new FormData();
        formData.set('verb', 'activate');
        formData.set('email', email);
        buttonToggle(button);
        void ajax(`${location.protocol}//${location.host}/api/uc/emails/activate`, formData, 'json', 'PATCH', AJAX_TIMEOUT, true).then((response) => {
            const data = response;
            if (data.data === true) {
                addSnackbar(`Activation email sent to ${email}`, 'success');
            }
            else {
                addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
            }
            buttonToggle(button);
        });
    }
}
//# sourceMappingURL=emails.js.map