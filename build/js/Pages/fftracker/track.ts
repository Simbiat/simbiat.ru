export class ffTrack
{
    private readonly form: HTMLFormElement | null = null;
    private readonly select: HTMLSelectElement | null = null;
    private readonly idInput: HTMLInputElement | null = null;

    public constructor()
    {
        //Get form
        this.form = document.querySelector('#ff_track_register');
        //Get the ID input
        this.idInput = document.querySelector('#ff_track_id');
        //Get select
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

    //Track the entity
    private add(): void
    {
        if (this.select) {
            const selectedOption = this.select.selectedOptions[0];
            let selectText: string;
            if (selectedOption) {
                selectText = selectedOption.text;
            } else {
                selectText = 'Character';
            }
            if (this.idInput) {
                const button = document.querySelector('#ff_track_submit');
                buttonToggle(button as HTMLInputElement);
                void ajax(`${location.protocol}//${location.host}/api/fftracker/${this.select.value}/${this.idInput.value}`, null, 'json', 'POST', AJAX_TIMEOUT, true).
                    then((response) => {
                        const data = response as ajaxJSONResponse;
                        if (data.data === true) {
                            addSnackbar(`${selectText} with ID ${this.idInput?.value ?? ''} was registered. Check <a href="${data.location}" target="_blank" rel="noopener noreferrer">here</a>.`, 'success', 0);
                        } else if (data.status === 404) {
                            addSnackbar(`${selectText} with ID ${this.idInput?.value ?? ''} was not found on Lodestone.`, 'failure', SNACKBAR_FAIL_LIFE);
                        } else if ((/^ID `.*` is already registered$/ui).exec(data.reason)) {
                            addSnackbar(`${data.reason}. Check <a href="${data.location}" target="_blank" rel="noopener noreferrer">here</a>.`, 'warning', 0);
                        } else {
                            addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                        }
                        buttonToggle(button as HTMLInputElement);
                    });
            }
        }
    }

    //Updates pattern for input field
    private typeChange(): void
    {
        if (this.select && this.idInput) {
            //Set default value for pattern
            let pattern = '^\\d{1,20}$';
            //Update pattern value
            if (this.select.value === 'pvpteams' || this.select.value === 'crossworld_linkshells') {
                pattern = '^[\\da-z]{40}$';
            }
            //Set pattern for the element
            this.idInput.setAttribute('pattern', pattern);
        }
    }
}
