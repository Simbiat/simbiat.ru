export class RemoveProfile
{
    private readonly button: HTMLInputElement | null = null;
    private readonly checkbox: HTMLInputElement | null = null;
    
    constructor()
    {
        //Check if form exists
        if (document.getElementById('user_removal')) {
            this.checkbox = document.getElementById('hard_removal') as HTMLInputElement;
            this.button = document.getElementById('remove_user') as HTMLInputElement;
            //Add event listeners
            this.checkbox.addEventListener('change', this.style.bind(this));
            this.button.addEventListener('click', this.remove.bind(this));
        }
    }
    
    private remove()
    {
        if (confirm('This is the last chance to back out.\nIf you press \'OK\' your user will be '+(this.checkbox && this.checkbox.checked ? 'permanently deleted' : 'removed')+'.\nPress \'Cancel\' to cancel the action.')) {
            //Get form data
            let formData = new FormData();
            //Append value of the checkbox
            formData.append('hard', (this.checkbox && this.checkbox.checked ? 'true' : 'false'));
            buttonToggle(this.button as HTMLInputElement);
            ajax(location.protocol+'//'+location.host+'/api/uc/remove/', formData, 'json', 'PATCH', 60000, true).then(data => {
                if (data.data === true) {
                    new Snackbar('Sad to see you go 😭', 'success', 10000);
                    location.reload();
                } else {
                    new Snackbar('Gods gave you another chance with this failure. 😇 Time to rethink your decision, maybe? 🤔', 'failure', 10000);
                }
                buttonToggle(this.button as HTMLInputElement);
            });
        } else {
            new Snackbar('Phew... That was a close one. 😅 No need to rush with drastic measures. 😊', 'success');
        }
    }
    
    private style()
    {
        if (this.checkbox) {
            if (this.checkbox.checked) {
                ((this.checkbox.parentNode as HTMLSpanElement).querySelector('label') as HTMLLabelElement).classList.add('failure');
                this.button?.classList.replace('warning', 'failure');
            } else {
                ((this.checkbox.parentNode as HTMLSpanElement).querySelector('label') as HTMLLabelElement).classList.remove('failure');
                this.button?.classList.replace('failure', 'warning');
            }
        }
    }
}
