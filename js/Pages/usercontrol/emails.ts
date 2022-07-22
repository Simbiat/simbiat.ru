class Emails
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
            //Listener for mail activation buttons
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
        let spinner = document.getElementById('addMail_spinner') as HTMLImageElement;
        spinner.classList.remove('hidden');
        ajax(location.protocol+'//'+location.host+'/api/uc/emails/add/', formData, 'json', 'POST', 60000, true).then(data => {
            if (data.data === true) {
                //Add row to table
                let row = (document.getElementById('emailsList') as HTMLTableElement).insertRow();
                row.classList.add('middle');
                let cell = row.insertCell();
                cell.innerHTML = email;
                cell = row.insertCell();
                cell.innerHTML ='<input type="button" value="Confirm" class="mail_activation" data-email="'+email+'" aria-invalid="false" placeholder="Confirm"><img class="hidden spinner inline" src="/img/spinner.svg" alt="Activating '+email+'..." data-tooltip="Activating '+email+'...">';
                cell = row.insertCell();
                cell.innerHTML ='Confirm address to change setting';
                cell.classList.add('warning');
                cell = row.insertCell();
                cell.innerHTML ='<td><input class="mail_deletion" data-email="'+email+'" type="image" src="/img/close.svg" alt="Delete '+email+'" aria-invalid="false" placeholder="image" data-tooltip="Delete '+email+'" tabindex="0"><img class="hidden spinner inline" src="/img/spinner.svg" alt="Removing '+email+'..." data-tooltip="Removing '+email+'...">';
                let input = cell.getElementsByTagName('input')[0] as HTMLInputElement;
                new Input().init(input);
                //Attach listeners
                row.querySelectorAll('.mail_activation').forEach(item => {
                    item.addEventListener('click', (event: Event) => {
                        this.activate(event.target as HTMLInputElement);
                    });
                });
                //Listener for mail subscription checkbox
                row.querySelectorAll('[id^=subscription_checkbox_]').forEach(item => {
                    item.addEventListener('click', (event: Event) => {
                        this.subscribe(event);
                    });
                });
                //Listener for mail activation buttons
                row.querySelectorAll('.mail_deletion').forEach(item => {
                    item.addEventListener('click', (event: Event) => {
                        this.delete(event.target as HTMLInputElement);
                    });
                });
                //Refresh delete buttons' status
                this.blockDelete();
                (this.addMailForm as HTMLFormElement).reset();
                new Snackbar(email+' added', 'success');
            } else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            spinner.classList.add('hidden');
        });
    }

    public delete(button: HTMLInputElement): void
    {
        let table = ((button.parentElement as HTMLTableCellElement).parentElement as HTMLTableRowElement).parentElement as HTMLTableElement;
        //Get row number
        let tr = ((button.parentElement as HTMLTableCellElement).parentElement as HTMLTableRowElement).rowIndex - 1;
        let spinner = (button.parentElement as HTMLTableCellElement).getElementsByClassName('spinner')[0] as HTMLImageElement;
        //Generate form data
        let formData = new FormData();
        let email = button.getAttribute('data-email') ?? '';
        formData.set('email', email);
        spinner.classList.remove('hidden');
        ajax(location.protocol+'//'+location.host+'/api/uc/emails/delete/', formData, 'json', 'DELETE', 60000, true).then(data => {
            if (data.data === true) {
                table.deleteRow(tr);
                this.blockDelete();
                new Snackbar(email+' removed', 'success');
            } else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            spinner.classList.add('hidden');
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
                    let email = (((item.parentElement as HTMLTableCellElement).parentElement as HTMLTableRowElement).getElementsByTagName('td')[0] as HTMLTableCellElement).innerHTML;
                    item.setAttribute('data-tooltip', 'Delete '+email);
                    let spinner = (item.parentElement as HTMLTableCellElement).getElementsByClassName('spinner')[0] as HTMLImageElement;
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
        let label = (checkbox.parentElement as HTMLDivElement).getElementsByTagName('label')[0] as HTMLLabelElement;
        let spinner = ((checkbox.parentElement as HTMLDivElement).parentElement as HTMLTableCellElement).getElementsByClassName('spinner')[0] as HTMLImageElement;
        //Generate form data
        let email = checkbox.getAttribute('data-email') ?? '';
        let formData = new FormData();
        formData.set('verb', verb);
        formData.set('email', email);
        spinner.classList.remove('hidden');
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
            spinner.classList.add('hidden');
        });
    }

    public activate(button: HTMLInputElement): void
    {
        let spinner = (button.parentElement as HTMLTableCellElement).getElementsByClassName('spinner')[0] as HTMLImageElement;
        //Generate form data
        let email = button.getAttribute('data-email') ?? '';
        let formData = new FormData();
        formData.set('verb', 'activate');
        formData.set('email', email);
        spinner.classList.remove('hidden');
        ajax(location.protocol+'//'+location.host+'/api/uc/emails/activate/', formData, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                new Snackbar('Activation email sent to '+email, 'success');
            } else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            spinner.classList.add('hidden');
        });
    }
}
