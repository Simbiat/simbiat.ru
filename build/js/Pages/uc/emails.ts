export class Emails
{
    private readonly addMailForm: HTMLFormElement | null = null;
    private readonly submit: HTMLInputElement | null = null;
    private readonly template: HTMLTemplateElement | null = null;
    private readonly tbody: HTMLTableElement | null = null;

    public constructor()
    {
        this.addMailForm = document.querySelector('#addMailForm');
        this.template = document.querySelector('#email_row');
        this.tbody = document.querySelector('#emailsList tbody');
        if (this.addMailForm) {
            this.submit = this.addMailForm.querySelector('#addMail_submit');
            submitIntercept(this.addMailForm, this.add.bind(this));
            //Listener for mail activation buttons
            document.querySelectorAll('.mail_activation').forEach((item) => {
                item.addEventListener('click', (event: MouseEvent) => {
                    Emails.activate(event.target as HTMLInputElement);
                });
            });
            //Listener for mail subscription checkbox
            document.querySelectorAll('[id^=subscription_checkbox_]').forEach((item) => {
                //Tracking click to be able to roll back change easily
                item.addEventListener('click', (event: MouseEvent) => {
                    Emails.subscribe(event);
                });
            });
            //Listener for mail deletion buttons
            document.querySelectorAll('.mail_deletion').forEach((item) => {
                item.addEventListener('click', (event: MouseEvent) => {
                    Emails.delete(event.target as HTMLInputElement);
                });
            });
        }
    }
    
    private add(): void
    {
        if (this.addMailForm && this.submit) {
            //Get form data
            const formData = new FormData(this.addMailForm);
            if (empty(formData.get('email'))) {
                addSnackbar('Please, enter a valid email address', 'failure');
                return;
            }
            const email = String(formData.get('email'));
            buttonToggle(this.submit);
            void ajax(`${location.protocol}//${location.host}/api/uc/emails/add`, formData, 'json', 'POST', AJAX_TIMEOUT, true).
                then((response) => {
                    const data = response as ajaxJSONResponse;
                    if (data.data === true) {
                        //Add row to table
                        this.addRow(email);
                        //Refresh delete buttons' status
                        Emails.blockDelete();
                        if (this.addMailForm) {
                            this.addMailForm.reset();
                        }
                        addSnackbar(`${email} added`, 'success');
                    } else {
                        addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                    }
                    if (this.submit) {
                        buttonToggle(this.submit);
                    }
                });
        }
    }
    
    private addRow(email: string): void
    {
        if (this.tbody && this.template) {
            const clone = this.template.content.cloneNode(true) as HTMLElement;
            const cells = clone.querySelectorAll('td');
            //Set email as value of the first cell
            if (cells[0]) {
                cells[0].innerHTML = email;
            }
            if (cells[1]) {
                //Update attributes of the second cell's input
                const inputElement1: HTMLInputElement | null = cells[1].querySelector('input');
                if (inputElement1) {
                    inputElement1.setAttribute('data-email', email);
                    //Attach listener
                    inputElement1.addEventListener('click', (event: MouseEvent) => {
                        Emails.activate(event.target as HTMLInputElement);
                    });
                }
                //Update attributes of the second cell's spinner
                const spinner1: HTMLImageElement | null = cells[1].querySelector('img');
                if (spinner1) {
                    spinner1.setAttribute('data-tooltip', String(spinner1.getAttribute('data-tooltip')).
                        replace('email', email));
                    spinner1.setAttribute('alt', String(spinner1.getAttribute('alt')).
                        replace('email', email));
                }
            }
            if (cells[3]) {
                //Update attributes of the 4th cell's input
                const inputElement3: HTMLInputElement | null = cells[3].querySelector('input');
                if (inputElement3) {
                    inputElement3.setAttribute('data-email', email);
                    inputElement3.setAttribute('data-tooltip', String(inputElement3.getAttribute('data-tooltip')).
                        replace('email', email));
                    inputElement3.setAttribute('alt', String(inputElement3.getAttribute('alt')).
                        replace('email', email));
                    //Attach listener
                    inputElement3.addEventListener('click', (event: MouseEvent) => {
                        Emails.delete(event.target as HTMLInputElement);
                    });
                }
                //Update attributes of the 4th cell's spinner
                const spinner3: HTMLImageElement | null = cells[3].querySelector('img');
                if (spinner3) {
                    spinner3.setAttribute('data-tooltip', String(spinner3.getAttribute('data-tooltip')).
                        replace('email', email));
                    spinner3.setAttribute('alt', String(spinner3.getAttribute('alt')).
                        replace('email', email));
                }
            }
            //Attach the row to table body
            this.tbody.appendChild(clone);
        }
    }
    
    private static delete(button: HTMLInputElement): void
    {
        //Generate form data
        const formData = new FormData();
        const email = button.getAttribute('data-email') ?? '';
        formData.set('email', email);
        buttonToggle(button);
        void ajax(`${location.protocol}//${location.host}/api/uc/emails/delete`, formData, 'json', 'DELETE', AJAX_TIMEOUT, true).then((response) => {
                const data = response as ajaxJSONResponse;
                if (data.data === true) {
                    deleteRow(button);
                    Emails.blockDelete();
                    addSnackbar(`${email} removed`, 'success');
                } else {
                    buttonToggle(button);
                    addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                }
            });
    }
    
    //Function to block button for mail removal if we have less than 2 confirmed mails
    private static blockDelete(): void
    {
        const confirmedMail = document.querySelectorAll('.mail_confirmed').length;
        document.querySelectorAll('.mail_deletion').forEach((item) => {
            const input = item as HTMLInputElement;
            const cell = input.parentElement;
            if (cell) {
                const row = cell.parentElement;
                if (row) {
                    //Check if row is for confirmed mail
                    if (row.querySelectorAll('.mail_confirmed').length > 0) {
                        input.disabled = confirmedMail < 2;
                    } else {
                        input.disabled = false;
                        //Update tooltips
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
    
    private static subscribe(event: Event): void
    {
        event.preventDefault();
        event.stopPropagation();
        const checkbox = event.target;
        if (!checkbox) {
            return;
        }
        if (!(checkbox as HTMLInputElement).hasAttribute('data-email')) {
            return;
        }
        //Get verb
        let verb;
        if ((checkbox as HTMLInputElement).checked) {
            verb = 'subscribe';
        } else {
            verb = 'unsubscribe';
        }
        buttonToggle(checkbox as HTMLInputElement);
        //Generate form data
        const email = String((checkbox as HTMLInputElement).getAttribute('data-email'));
        const formData = new FormData();
        formData.set('verb', verb);
        formData.set('email', email);
        void ajax(`${location.protocol}//${location.host}/api/uc/emails/${verb}`, formData, 'json', 'PATCH', AJAX_TIMEOUT, true).then((response) => {
                const data = response as ajaxJSONResponse;
                if (data.data === true) {
                    if ((checkbox as HTMLInputElement).checked) {
                        (checkbox as HTMLInputElement).checked = false;
                        addSnackbar(`${email} unsubscribed`, 'success');
                    } else {
                        (checkbox as HTMLInputElement).checked = true;
                        addSnackbar(`${email} subscribed`, 'success');
                    }
                } else {
                    addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                }
                buttonToggle(checkbox as HTMLInputElement);
            });
    }
    
    private static activate(button: HTMLInputElement): void
    {
        //Generate form data
        const email = button.getAttribute('data-email') ?? '';
        const formData = new FormData();
        formData.set('verb', 'activate');
        formData.set('email', email);
        buttonToggle(button);
        void ajax(`${location.protocol}//${location.host}/api/uc/emails/activate`, formData, 'json', 'PATCH', AJAX_TIMEOUT, true).then((response) => {
                const data = response as ajaxJSONResponse;
                if (data.data === true) {
                    addSnackbar(`Activation email sent to ${email}`, 'success');
                } else {
                    addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                }
                buttonToggle(button);
            });
    }
}
