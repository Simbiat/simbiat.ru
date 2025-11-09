export class ffTrack {
    form = null;
    select = null;
    idInput = null;
    constructor() {
        this.form = document.querySelector('#ff_track_register');
        this.idInput = document.querySelector('#ff_track_id');
        this.select = document.querySelector('#ff_track_type');
        if (this.select) {
            this.select.addEventListener('change', () => {
                this.typeChange();
            });
        }
        if (this.form) {
            submitIntercept(this.form, this.add.bind(this));
        }
    }
    add() {
        if (this.select) {
            const selectedOption = this.select.selectedOptions[0];
            let selectText;
            if (selectedOption) {
                selectText = selectedOption.text;
            }
            else {
                selectText = 'Character';
            }
            if (this.idInput) {
                const button = document.querySelector('#ff_track_submit');
                buttonToggle(button);
                void ajax(`${location.protocol}//${location.host}/api/fftracker/${this.select.value}/${this.idInput.value}`, null, 'json', 'POST', AJAX_TIMEOUT, true).
                    then((response) => {
                    const data = response;
                    if (data.data === true) {
                        addSnackbar(`${selectText} with ID ${this.idInput?.value ?? ''} was registered. Check <a href="${data.location}" target="_blank" rel="noopener noreferrer">here</a>.`, 'success', 0);
                    }
                    else if (data.status === 404) {
                        addSnackbar(`${selectText} with ID ${this.idInput?.value ?? ''} was not found on Lodestone.`, 'failure', SNACKBAR_FAIL_LIFE);
                    }
                    else if ((/^ID `.*` is already registered$/ui).exec(data.reason)) {
                        addSnackbar(`${data.reason}. Check <a href="${data.location}" target="_blank" rel="noopener noreferrer">here</a>.`, 'warning', 0);
                    }
                    else {
                        addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                    }
                    buttonToggle(button);
                });
            }
        }
    }
    typeChange() {
        if (this.select && this.idInput) {
            let pattern = '^\\d{1,20}$';
            if (this.select.value === 'pvpteams' || this.select.value === 'crossworld_linkshells') {
                pattern = '^[\\da-z]{40}$';
            }
            this.idInput.setAttribute('pattern', pattern);
        }
    }
}
//# sourceMappingURL=track.js.map