export class EditSessions {
    cookieButtons;
    sessionButtons;
    constructor() {
        this.cookieButtons = document.querySelectorAll('.cookie_deletion:not([disabled])');
        this.sessionButtons = document.querySelectorAll('.session_deletion:not([disabled])');
        document.querySelectorAll('.cookie_deletion, .session_deletion').forEach((item) => {
            item.addEventListener('click', (event) => {
                EditSessions.delete(event.target);
            });
        });
        document.querySelectorAll('#delete_cookies, #delete_sessions').forEach((item) => {
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
            buttons = this.cookieButtons;
        }
        else if (button.id === 'delete_sessions') {
            type = 'sessions';
            buttons = this.sessionButtons;
        }
        else {
            addSnackbar('Unknown button type', 'failure', 10000);
            return;
        }
        const ArrayOfButtons = Array.from(buttons).reverse();
        ArrayOfButtons.forEach((item) => {
            EditSessions.delete(item, false);
        });
        addSnackbar(`All ${type} except current were removed`, 'success');
    }
    static delete(button, singular = true) {
        const formData = new FormData();
        let type;
        let typeSingular;
        if (button.classList.contains('cookie_deletion')) {
            type = 'cookies';
            typeSingular = 'Cookie';
            formData.set('cookie', String(button.getAttribute('data-cookie')));
        }
        else if (button.classList.contains('session_deletion')) {
            type = 'sessions';
            typeSingular = 'Session';
            formData.set('session', String(button.getAttribute('data-session')));
        }
        else {
            addSnackbar('Unknown button type', 'failure', 10000);
            return;
        }
        buttonToggle(button);
        void ajax(`${location.protocol}//${location.host}/api/uc/${type}/delete/`, formData, 'json', 'DELETE', 60000, true).then((response) => {
            const data = response;
            if (data.data === true) {
                deleteRow(button);
                if (singular) {
                    addSnackbar(`${typeSingular} removed`, 'success');
                }
            }
            else {
                buttonToggle(button);
                addSnackbar(data.reason, 'failure', 10000);
            }
        });
    }
}
//# sourceMappingURL=sessions.js.map