export class Emails
{
    private readonly addMailForm: HTMLFormElement | null = null;

    constructor()
    {
        this.addMailForm = document.getElementById('addMailForm') as HTMLFormElement;
        if (this.addMailForm) {
            submitIntercept(this.addMailForm, this.add.bind(this));
            //Listener for mail activation buttons
            document.querySelectorAll('.mail_activation').forEach(item => {
                item.addEventListener('click', (event: Event) => {
                    this.activate(event.target as HTMLInputElement);
                });
            });
            //Listener for mail subscription checkbox
            document.querySelectorAll('[id^=subscription_checkbox_]').forEach(item => {
                item.addEventListener('click', (event: Event) => {
                    this.subscribe(event);
                });
            });
            //Listener for mail deletion buttons
            document.querySelectorAll('.mail_deletion').forEach(item => {
                item.addEventListener('click', (event: Event) => {
                    this.delete(event.target as HTMLInputElement);
                });
            });
        }
    }

    public add(): boolean | void
    {
        //Get form data
        let formData = new FormData(this.addMailForm as HTMLFormElement);
        if (!formData.get('email')) {
            new Snackbar('Please, enter a valid email address', 'failure');
            return false;
        }
        let email = String(formData.get('email'));
        let button = (this.addMailForm as HTMLFormElement).querySelector('#addMail_submit');
        buttonToggle(button as HTMLInputElement);
        ajax(location.protocol+'//'+location.host+'/api/uc/emails/add/', formData, 'json', 'POST', 60000, true).then(data => {
            if (data.data === true) {
                //Add row to table
                let template = (document.querySelector('#email_row') as HTMLTemplateElement).content.cloneNode(true) as DocumentFragment;
                let cells = template.querySelectorAll('td');
                //Set email as value of the first cell
                (cells[0] as HTMLTableCellElement).innerHTML = email;
                //Update attributes of the second cell's input
                let inputElement = (cells[1] as HTMLTableCellElement).querySelector('input') as HTMLInputElement;
                new Input().init(inputElement);
                inputElement.setAttribute('data-email', email);
                //Attach listener
                inputElement.addEventListener('click', (event: Event) => {
                    this.activate(event.target as HTMLInputElement);
                });
                //Update attributes of the second cell's spinner
                let spinner = (cells[1] as HTMLTableCellElement).querySelector('img') as HTMLImageElement;
                spinner.setAttribute('data-tooltip', String(spinner.getAttribute('data-tooltip')).replace('email', email));
                spinner.setAttribute('alt', String(spinner.getAttribute('alt')).replace('email', email));
                //Update attributes of the 4th cell's input
                inputElement = (cells[3] as HTMLTableCellElement).querySelector('input') as HTMLInputElement;
                new Input().init(inputElement);
                inputElement.setAttribute('data-email', email);
                inputElement.setAttribute('data-tooltip', String(inputElement.getAttribute('data-tooltip')).replace('email', email));
                inputElement.setAttribute('alt', String(inputElement.getAttribute('alt')).replace('email', email));
                //Attach listener
                inputElement.addEventListener('click', (event: Event) => {
                    this.delete(event.target as HTMLInputElement);
                });
                //Update attributes of the 4th cell's spinner
                spinner = (cells[3] as HTMLTableCellElement).querySelector('img') as HTMLImageElement;
                spinner.setAttribute('data-tooltip', String(spinner.getAttribute('data-tooltip')).replace('email', email));
                spinner.setAttribute('alt', String(spinner.getAttribute('alt')).replace('email', email));
                //Attach the row to table body
                (document.querySelector('#emailsList tbody') as HTMLTableElement).appendChild(template);
                //Refresh delete buttons' status
                this.blockDelete();
                (this.addMailForm as HTMLFormElement).reset();
                new Snackbar(email+' added', 'success');
            } else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(button as HTMLInputElement);
        });
    }

    public delete(button: HTMLInputElement): void
    {
        //Generate form data
        let formData = new FormData();
        let email = button.getAttribute('data-email') ?? '';
        formData.set('email', email);
        buttonToggle(button as HTMLInputElement);
        ajax(location.protocol+'//'+location.host+'/api/uc/emails/delete/', formData, 'json', 'DELETE', 60000, true).then(data => {
            if (data.data === true) {
                deleteRow(button);
                this.blockDelete();
                new Snackbar(email+' removed', 'success');
            } else {
                buttonToggle(button as HTMLInputElement);
                new Snackbar(data.reason, 'failure', 10000);
            }
        });
    }

    //Function to block button for mail removal if we have less than 2 confirmed mails
    public blockDelete(): void
    {
        let confirmedMail = document.getElementsByClassName('mail_confirmed').length;
        document.querySelectorAll('.mail_deletion').forEach(item => {
            //Check if row is for confirmed mail
            if ((((item as HTMLInputElement).parentElement as HTMLTableCellElement).parentElement as HTMLTableRowElement).getElementsByClassName('mail_confirmed').length > 0) {
                (item as HTMLInputElement).disabled = confirmedMail < 2;
            } else {
                (item as HTMLInputElement).disabled = false;
                //Update tooltips
                if (item.getAttribute('data-tooltip') && item.getAttribute('data-tooltip') === 'Can\'t delete') {
                    let email = (((item.parentElement as HTMLTableCellElement).parentElement as HTMLTableRowElement).querySelector('td') as HTMLTableCellElement).innerHTML;
                    item.setAttribute('data-tooltip', 'Delete '+email);
                    let spinner = (item.parentElement as HTMLTableCellElement).querySelector('.spinner') as HTMLImageElement;
                    spinner.setAttribute('data-tooltip', 'Removing '+email+'...');
                    spinner.setAttribute('alt', 'Removing '+email+'...');
                }
            }
        });
    }

    public subscribe(event: Event): void
    {
        event.preventDefault();
        event.stopPropagation();
        let checkbox = event.target as HTMLInputElement;
        //Get verb
        let verb;
        if (checkbox.checked) {
            verb = 'subscribe';
        } else {
            verb = 'unsubscribe';
        }
        let label = (checkbox.parentElement as HTMLDivElement).querySelector('label') as HTMLLabelElement;
        buttonToggle(checkbox as HTMLInputElement);
        //Generate form data
        let email = checkbox.getAttribute('data-email') ?? '';
        let formData = new FormData();
        formData.set('verb', verb);
        formData.set('email', email);
        ajax(location.protocol+'//'+location.host+'/api/uc/emails/'+verb+'/', formData, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                if (checkbox.checked) {
                    checkbox.checked = false;
                    label.innerText = 'Subscribe';
                    new Snackbar(email+' unsubscribed', 'success');
                } else {
                    checkbox.checked = true;
                    label.innerText = 'Unsubscribe';
                    new Snackbar(email+' subscribed', 'success');
                }
            } else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(checkbox as HTMLInputElement);
        });
    }

    public activate(button: HTMLInputElement): void
    {
        //Generate form data
        let email = button.getAttribute('data-email') ?? '';
        let formData = new FormData();
        formData.set('verb', 'activate');
        formData.set('email', email);
        buttonToggle(button as HTMLInputElement);
        ajax(location.protocol+'//'+location.host+'/api/uc/emails/activate/', formData, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                new Snackbar('Activation email sent to '+email, 'success');
            } else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(button as HTMLInputElement);
        });
    }
}
