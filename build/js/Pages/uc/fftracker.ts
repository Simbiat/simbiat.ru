export class EditFFLinks
{
    private readonly form: HTMLFormElement | null = null;
    private readonly button: HTMLInputElement | null = null;

    public constructor()
    {
        this.form = document.querySelector('#ff_link_user');
        if (this.form) {
            submitIntercept(this.form, this.link.bind(this));
            this.button = this.form.querySelector('#ff_link_submit');
        }
    }
    
    private link(): void
    {
        if (this.form && this.button) {
            //Get form data
            const formData = new FormData(this.form);
            buttonToggle(this.button);
            void ajax(`${location.protocol}//${location.host}/api/uc/fflink`, formData, 'json', 'POST', ajaxTimeout, true).then((response) => {
                const data = response as ajaxJSONResponse;
                if (data.data === true) {
                    addSnackbar('Character linked successfully. Reloading page...', 'success');
                    pageRefresh();
                } else {
                    addSnackbar(data.reason, 'failure', snackbarFailLife);
                }
                if (this.button) {
                    buttonToggle(this.button);
                }
            });
        }
    }
}
