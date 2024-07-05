export class RemoveProfile
{
    private readonly button: HTMLInputElement | null = null;
    private readonly checkbox: HTMLInputElement | null = null;
    
    public constructor()
    {
        //Check if form exists
        if (document.querySelector('#user_removal')) {
            this.checkbox = document.querySelector('#hard_removal');
            this.button = document.querySelector('#remove_user');
            //Add event listeners
            if (this.checkbox) {
                this.checkbox.addEventListener('change', this.style.bind(this));
            }
            if (this.button) {
                this.button.addEventListener('click', this.remove.bind(this));
            }
        }
    }
    
    private remove(): void
    {
        if (this.checkbox && this.button) {
            if (confirm(`This is the last chance to back out.\nIf you press 'OK' your user will be ${this.checkbox.checked ? 'permanently deleted' : 'removed'}.\nPress 'Cancel' to cancel the action.`)) {
                //Get form data
                const formData = new FormData();
                //Append value of the checkbox
                formData.append('hard', (this.checkbox.checked ? 'true' : 'false'));
                buttonToggle(this.button);
                void ajax(`${location.protocol}//${location.host}/api/uc/remove`, formData, 'json', 'PATCH', 60000, true).then((response) => {
                        const data = response as ajaxJSONResponse;
                        if (data.data === true) {
                            addSnackbar('Sad to see you go 😭', 'success', 10000);
                            pageRefresh();
                        } else {
                            addSnackbar('Gods gave you another chance with this failure. 😇 Time to rethink your decision, maybe? 🤔', 'failure', 10000);
                        }
                        if (this.button) {
                            buttonToggle(this.button);
                        }
                    });
            } else {
                addSnackbar('Phew... That was a close one. 😅 No need to rush with drastic measures. 😊', 'success');
            }
        }
    }
    
    private style(): void
    {
        if (this.checkbox?.parentNode) {
            if (this.checkbox.checked) {
                this.checkbox.parentNode.querySelector('label')?.classList.add('failure');
                this.button?.classList.replace('warning', 'failure');
            } else {
                this.checkbox.parentNode.querySelector('label')?.classList.remove('failure');
                this.button?.classList.replace('failure', 'warning');
            }
        }
    }
}
