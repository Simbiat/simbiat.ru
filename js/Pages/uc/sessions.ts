export class EditSessions
{
    constructor()
    {
        //Listener for deletion buttons
        document.querySelectorAll('.cookie_deletion, .session_deletion').forEach(item => {
            item.addEventListener('click', (event: Event) => {
                this.delete(event.target as HTMLInputElement);
            });
        });
        //Listener for "Delete all" buttons
        document.querySelectorAll('#delete_cookies, #delete_sessions').forEach(item => {
            item.addEventListener('click', (event: Event) => {
                this.deleteAll(event.target as HTMLInputElement);
            });
        });
    }
    
    public deleteAll(button: HTMLInputElement)
    {
        let buttons: NodeListOf<HTMLInputElement>;
        let type: string;
        if (button.id === 'delete_cookies') {
            type = 'cookies';
            buttons = document.querySelectorAll('.cookie_deletion:not([disabled])');
        } else if (button.id === 'delete_sessions') {
            type = 'sessions';
            buttons = document.querySelectorAll('.session_deletion:not([disabled])');
        } else {
            new Snackbar('Unknown button type', 'failure', 10000);
            return;
        }
        //Traverse in reverse, because of numeric row IDs used for rows removal
        Array.prototype.reverse.call(buttons).forEach(item => {
            this.delete(item, false);
        });
        new Snackbar('All '+type+' except current were removed', 'success');
    }
    
    public delete(button: HTMLInputElement, singular: boolean = true)
    {
        let spinner = (button.parentElement as HTMLTableCellElement).getElementsByClassName('spinner')[0] as HTMLImageElement;
        //Generate form data
        let formData = new FormData();
        let type: string, typeSingular: string;
        if (button.classList.contains('cookie_deletion')) {
            type = 'cookies';
            typeSingular = 'Cookie';
            formData.set('cookie', (button.getAttribute('data-cookie') as string));
        } else if (button.classList.contains('session_deletion')) {
            type = 'sessions';
            typeSingular = 'Session';
            formData.set('session', (button.getAttribute('data-session') as string));
        } else {
            new Snackbar('Unknown button type', 'failure', 10000);
            return;
        }
        spinner.classList.remove('hidden');
        ajax(location.protocol+'//'+location.host+'/api/uc/'+type+'/delete/', formData, 'json', 'DELETE', 60000, true).then(data => {
            if (data.data === true) {
                deleteRow(button);
                if (singular) {
                    new Snackbar(typeSingular + ' removed', 'success');
                }
            } else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            spinner.classList.add('hidden');
        });
    }
}
