export class ffTrack
{
    private readonly select: HTMLSelectElement;
    private readonly idInput: HTMLInputElement;

    constructor()
    {
        //Get the ID input
        this.idInput = document.getElementById('ff_track_id') as HTMLInputElement;
        //Get select
        this.select = document.getElementById('ff_track_type') as HTMLSelectElement;
        this.select.addEventListener('change', () => {
            this.typeChange();
        });
        submitIntercept(document.getElementById('ff_track_register') as HTMLFormElement, this.add.bind(this));
    }

    //Track the entity
    public add(): void
    {
        let selectedOption = this.select.selectedOptions[0];
        let selectText: string;
        if (selectedOption) {
            selectText = selectedOption.text;
        } else {
            selectText = 'Character';
        }
        if (this.idInput && this.select) {
            //Get spinner
            let spinner = document.getElementById('ff_track_spinner') as HTMLImageElement;
            spinner.classList.remove('hidden');
            ajax(location.protocol+'//'+location.host+'/api/fftracker/'+this.select.value+'/'+this.idInput.value+'/', null, 'json', 'POST', 60000, true).then(data => {
                if (data.data === true) {
                    new Snackbar(selectText + ' with ID ' + this.idInput.value + ' was registered. Check <a href="' + location.protocol + '//' + location.host + '/fftracker/' + this.select.value + '/' + this.idInput.value + '/' + '" target="_blank">here</a>.', 'success', 0);
                } else if (data === '404') {
                    new Snackbar(selectText + ' with ID ' + this.idInput.value + ' was not found on Lodestone.', 'failure', 10000);
                } else {
                    if (data.reason.match(/^ID `.*` is already registered$/ui)) {
                        new Snackbar(data.reason + '. Check <a href="' + location.protocol + '//' + location.host + '/fftracker/' + this.select.value + '/' + this.idInput.value + '/' + '" target="_blank">here</a>.', 'warning', 0);
                    } else {
                        new Snackbar(data.reason, 'failure', 10000);
                    }
                }
                spinner.classList.add('hidden');
            });
        }
    }

    //Updates pattern for input field
    public typeChange(): void
    {
        //Set default value for pattern
        let pattern = '^\\d{1,20}$';
        //Update pattern value
        if (this.select.value === 'pvpteam' || this.select.value === 'crossworld_linkshell') {
            pattern = '^[\\da-z]{40}$';
        }
        //Set pattern for the element
        this.idInput.setAttribute('pattern', pattern);
    }
}
