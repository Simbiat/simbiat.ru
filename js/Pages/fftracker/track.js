export class ffTrack {
    select;
    idInput;
    constructor() {
        this.idInput = document.getElementById('ff_track_id');
        this.select = document.getElementById('ff_track_type');
        this.select.addEventListener('change', () => {
            this.typeChange();
        });
        submitIntercept(document.getElementById('ff_track_register'), this.add.bind(this));
    }
    add() {
        let selectedOption = this.select.selectedOptions[0];
        let selectText;
        if (selectedOption) {
            selectText = selectedOption.text;
        }
        else {
            selectText = 'Character';
        }
        if (this.idInput && this.select) {
            let spinner = document.getElementById('ff_track_spinner');
            spinner.classList.remove('hidden');
            ajax(location.protocol + '//' + location.host + '/api/fftracker/' + this.select.value + '/' + this.idInput.value + '/', null, 'json', 'POST', 60000, true).then(data => {
                if (data.data === true) {
                    new Snackbar(selectText + ' with ID ' + this.idInput.value + ' was registered. Check <a href="' + data.location + '" target="_blank">here</a>.', 'success', 0);
                }
                else if (data === '404') {
                    new Snackbar(selectText + ' with ID ' + this.idInput.value + ' was not found on Lodestone.', 'failure', 10000);
                }
                else {
                    if (data.reason.match(/^ID `.*` is already registered$/ui)) {
                        new Snackbar(data.reason + '. Check <a href="' + data.location + '" target="_blank">here</a>.', 'warning', 0);
                    }
                    else {
                        new Snackbar(data.reason, 'failure', 10000);
                    }
                }
                spinner.classList.add('hidden');
            });
        }
    }
    typeChange() {
        let pattern = '^\\d{1,20}$';
        if (this.select.value === 'pvpteam' || this.select.value === 'crossworld_linkshell') {
            pattern = '^[\\da-z]{40}$';
        }
        this.idInput.setAttribute('pattern', pattern);
    }
}
//# sourceMappingURL=track.js.map