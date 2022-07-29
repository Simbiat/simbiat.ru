export class EditProfile
{
    private readonly usernameForm: HTMLFormElement | null = null;
    private readonly usernameSubmit: HTMLInputElement | null = null;
    private readonly usernameField: HTMLInputElement | null = null;

    constructor()
    {
        this.usernameForm = document.getElementById('profile_username') as HTMLFormElement;
        if (this.usernameForm) {
            this.usernameField = document.getElementById('username_value') as HTMLInputElement;
            this.usernameSubmit = document.getElementById('username_submit') as HTMLInputElement;
            ['focus', 'change', 'input',].forEach((eventType: string) => {
                (this.usernameField as HTMLInputElement).addEventListener(eventType, this.usernameOnChange.bind(this));
            });
            this.usernameOnChange();
            submitIntercept(this.usernameForm, this.username.bind(this));
        }
    }

    public usernameOnChange(): void
    {
        (this.usernameSubmit as HTMLInputElement).disabled = (this.usernameField as HTMLInputElement).getAttribute('data-original') === (this.usernameField as HTMLInputElement).value;
    }

    public username(): void
    {
        //Get form data
        let formData = new FormData(this.usernameForm as HTMLFormElement);
        let spinner = document.getElementById('username_spinner') as HTMLImageElement;
        spinner.classList.remove('hidden');
        ajax(location.protocol+'//'+location.host+'/api/uc/username/', formData, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                (this.usernameField as HTMLInputElement).setAttribute('data-original', (this.usernameField as HTMLInputElement).value);
                this.usernameOnChange();
                new Snackbar('Username changed', 'success');
            } else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            spinner.classList.add('hidden');
        });
    }
}
