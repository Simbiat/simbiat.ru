export class PasswordChange
{
    private readonly form: HTMLFormElement | null = null;

    constructor()
    {
        this.form = document.getElementById('password_change') as HTMLFormElement;
        if (this.form) {
            submitIntercept(this.form, this.change.bind(this));
        }
    }

    public change(): void
    {
        //Get form data
        let formData = new FormData(this.form as HTMLFormElement);
        let spinner = document.getElementById('pw_change_spinner') as HTMLImageElement;
        spinner.classList.remove('hidden');
        ajax(location.protocol+'//'+location.host+'/api/uc/password/', formData, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                new Snackbar('Password changed', 'success');
            } else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            spinner.classList.add('hidden');
        });
    }
}
