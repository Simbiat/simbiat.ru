export class EditFFLinks
{
    private readonly form: HTMLFormElement | null = null;

    constructor()
    {
        this.form = document.getElementById('ff_link_user') as HTMLFormElement;
        if (this.form) {
            submitIntercept(this.form, this.link.bind(this));
        }
    }

    public link(): void
    {
        //Get form data
        let formData = new FormData(this.form as HTMLFormElement);
        let spinner = document.getElementById('ff_link_spinner') as HTMLImageElement;
        spinner.classList.remove('hidden');
        ajax(location.protocol+'//'+location.host+'/api/uc/fflink/', formData, 'json', 'POST', 60000, true).then(data => {
            if (data.data === true) {
                new Snackbar('Character linked successfully. Reloading page...', 'success');
                location.reload();
            } else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            spinner.classList.add('hidden');
        });
    }
}
