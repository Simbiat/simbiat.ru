export class EditSessions {
    constructor() {
        document.querySelectorAll('.cookie_deletion, .session_deletion').forEach(item => {
            item.addEventListener('click', (event) => {
                this.delete(event.target);
            });
        });
        document.querySelectorAll('#delete_cookies, #delete_sessions').forEach(item => {
            item.addEventListener('click', (event) => {
                this.deleteAll(event.target);
            });
        });
    }
    deleteAll(button) {
        let buttons;
        let type;
        if (button.id === 'delete_cookies') {
            type = 'cookies';
            buttons = document.querySelectorAll('.cookie_deletion:not([disabled])');
        }
        else if (button.id === 'delete_sessions') {
            type = 'sessions';
            buttons = document.querySelectorAll('.session_deletion:not([disabled])');
        }
        else {
            new Snackbar('Unknown button type', 'failure', 10000);
            return;
        }
        Array.prototype.reverse.call(buttons).forEach(item => {
            this.delete(item, false);
        });
        new Snackbar('All ' + type + ' except current were removed', 'success');
    }
    delete(button, singular = true) {
        let spinner = button.parentElement.getElementsByClassName('spinner')[0];
        let formData = new FormData();
        let type, typeSingular;
        if (button.classList.contains('cookie_deletion')) {
            type = 'cookies';
            typeSingular = 'Cookie';
            formData.set('cookie', button.getAttribute('data-cookie'));
        }
        else if (button.classList.contains('session_deletion')) {
            type = 'sessions';
            typeSingular = 'Session';
            formData.set('session', button.getAttribute('data-session'));
        }
        else {
            new Snackbar('Unknown button type', 'failure', 10000);
            return;
        }
        spinner.classList.remove('hidden');
        ajax(location.protocol + '//' + location.host + '/api/uc/' + type + '/delete/', formData, 'json', 'DELETE', 60000, true).then(data => {
            if (data.data === true) {
                deleteRow(button);
                if (singular) {
                    new Snackbar(typeSingular + ' removed', 'success');
                }
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            spinner.classList.add('hidden');
        });
    }
}
//# sourceMappingURL=sessions.js.map