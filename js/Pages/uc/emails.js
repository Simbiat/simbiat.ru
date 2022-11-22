export class Emails {
    addMailForm = null;
    constructor() {
        this.addMailForm = document.getElementById('addMailForm');
        if (this.addMailForm) {
            submitIntercept(this.addMailForm, this.add.bind(this));
            document.querySelectorAll('.mail_activation').forEach(item => {
                item.addEventListener('click', (event) => {
                    this.activate(event.target);
                });
            });
            document.querySelectorAll('[id^=subscription_checkbox_]').forEach(item => {
                item.addEventListener('click', (event) => {
                    this.subscribe(event);
                });
            });
            document.querySelectorAll('.mail_deletion').forEach(item => {
                item.addEventListener('click', (event) => {
                    this.delete(event.target);
                });
            });
        }
    }
    add() {
        let formData = new FormData(this.addMailForm);
        if (!formData.get('email')) {
            new Snackbar('Please, enter a valid email address', 'failure');
            return false;
        }
        let email = String(formData.get('email'));
        let button = this.addMailForm.querySelector('#addMail_submit');
        buttonToggle(button);
        ajax(location.protocol + '//' + location.host + '/api/uc/emails/add/', formData, 'json', 'POST', 60000, true).then(data => {
            if (data.data === true) {
                let row = document.getElementById('emailsList').insertRow();
                row.classList.add('middle');
                let cell = row.insertCell();
                cell.innerHTML = email;
                cell = row.insertCell();
                cell.innerHTML = '<input type="button" value="Confirm" class="mail_activation" data-email="' + email + '" aria-invalid="false" placeholder="Confirm"><img class="hidden spinner inline" src="/img/spinner.svg" alt="Activating ' + email + '..." data-tooltip="Activating ' + email + '...">';
                cell = row.insertCell();
                cell.innerHTML = 'Confirm address to change setting';
                cell.classList.add('warning');
                cell = row.insertCell();
                cell.innerHTML = '<td><input class="mail_deletion" data-email="' + email + '" type="image" src="/img/close.svg" alt="Delete ' + email + '" aria-invalid="false" placeholder="image" data-tooltip="Delete ' + email + '" tabindex="0"><img class="hidden spinner inline" src="/img/spinner.svg" alt="Removing ' + email + '..." data-tooltip="Removing ' + email + '...">';
                let input = cell.querySelector('input');
                new Input().init(input);
                row.querySelectorAll('.mail_activation').forEach(item => {
                    item.addEventListener('click', (event) => {
                        this.activate(event.target);
                    });
                });
                row.querySelectorAll('[id^=subscription_checkbox_]').forEach(item => {
                    item.addEventListener('click', (event) => {
                        this.subscribe(event);
                    });
                });
                row.querySelectorAll('.mail_deletion').forEach(item => {
                    item.addEventListener('click', (event) => {
                        this.delete(event.target);
                    });
                });
                this.blockDelete();
                this.addMailForm.reset();
                new Snackbar(email + ' added', 'success');
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(button);
        });
    }
    delete(button) {
        let formData = new FormData();
        let email = button.getAttribute('data-email') ?? '';
        formData.set('email', email);
        buttonToggle(button);
        ajax(location.protocol + '//' + location.host + '/api/uc/emails/delete/', formData, 'json', 'DELETE', 60000, true).then(data => {
            if (data.data === true) {
                deleteRow(button);
                this.blockDelete();
                new Snackbar(email + ' removed', 'success');
            }
            else {
                buttonToggle(button);
                new Snackbar(data.reason, 'failure', 10000);
            }
        });
    }
    blockDelete() {
        let confirmedMail = document.getElementsByClassName('mail_confirmed').length;
        document.querySelectorAll('.mail_deletion').forEach(item => {
            if (item.parentElement.parentElement.getElementsByClassName('mail_confirmed').length > 0) {
                item.disabled = confirmedMail < 2;
            }
            else {
                item.disabled = false;
                if (item.getAttribute('data-tooltip') && item.getAttribute('data-tooltip') === 'Can\'t delete') {
                    let email = item.parentElement.parentElement.querySelector('td').innerHTML;
                    item.setAttribute('data-tooltip', 'Delete ' + email);
                    let spinner = item.parentElement.querySelector('.spinner');
                    spinner.setAttribute('data-tooltip', 'Removing ' + email + '...');
                    spinner.setAttribute('alt', 'Removing ' + email + '...');
                }
            }
        });
    }
    subscribe(event) {
        event.preventDefault();
        event.stopPropagation();
        let checkbox = event.target;
        let verb;
        if (checkbox.checked) {
            verb = 'subscribe';
        }
        else {
            verb = 'unsubscribe';
        }
        let label = checkbox.parentElement.querySelector('label');
        buttonToggle(checkbox);
        let email = checkbox.getAttribute('data-email') ?? '';
        let formData = new FormData();
        formData.set('verb', verb);
        formData.set('email', email);
        ajax(location.protocol + '//' + location.host + '/api/uc/emails/' + verb + '/', formData, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                if (checkbox.checked) {
                    checkbox.checked = false;
                    label.innerText = 'Subscribe';
                    new Snackbar(email + ' unsubscribed', 'success');
                }
                else {
                    checkbox.checked = true;
                    label.innerText = 'Unsubscribe';
                    new Snackbar(email + ' subscribed', 'success');
                }
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(checkbox);
        });
    }
    activate(button) {
        let email = button.getAttribute('data-email') ?? '';
        let formData = new FormData();
        formData.set('verb', 'activate');
        formData.set('email', email);
        buttonToggle(button);
        ajax(location.protocol + '//' + location.host + '/api/uc/emails/activate/', formData, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                new Snackbar('Activation email sent to ' + email, 'success');
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(button);
        });
    }
}
//# sourceMappingURL=emails.js.map