export class RemoveProfile {
    button = null;
    checkbox = null;
    constructor() {
        if (document.getElementById('user_removal')) {
            this.checkbox = document.getElementById('hard_removal');
            this.button = document.getElementById('remove_user');
            this.checkbox.addEventListener('change', this.style.bind(this));
            this.button.addEventListener('click', this.remove.bind(this));
        }
    }
    remove() {
        if (confirm('This is the last chance to back out.\nIf you press \'OK\' your user will be ' + (this.checkbox && this.checkbox.checked ? 'permanently deleted' : 'removed') + '.\nPress \'Cancel\' to cancel the action.')) {
            let formData = new FormData();
            formData.append('hard', (this.checkbox && this.checkbox.checked ? 'true' : 'false'));
            buttonToggle(this.button);
            ajax(location.protocol + '//' + location.host + '/api/uc/remove/', formData, 'json', 'PATCH', 60000, true).then(data => {
                if (data.data === true) {
                    new Snackbar('Sad to see you go 😭', 'success', 10000);
                    window.location.href = window.location.href + '?forceReload=true';
                }
                else {
                    new Snackbar('Gods gave you another chance with this failure. 😇 Time to rethink your decision, maybe? 🤔', 'failure', 10000);
                }
                buttonToggle(this.button);
            });
        }
        else {
            new Snackbar('Phew... That was a close one. 😅 No need to rush with drastic measures. 😊', 'success');
        }
    }
    style() {
        if (this.checkbox) {
            if (this.checkbox.checked) {
                this.checkbox.parentNode.querySelector('label').classList.add('failure');
                this.button?.classList.replace('warning', 'failure');
            }
            else {
                this.checkbox.parentNode.querySelector('label').classList.remove('failure');
                this.button?.classList.replace('failure', 'warning');
            }
        }
    }
}
//# sourceMappingURL=removal.js.map