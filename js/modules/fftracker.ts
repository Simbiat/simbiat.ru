/*exported fftrackerInit, ffTrackAdd*/
/*globals ajax, addSnackbar, submitIntercept*/

function fftrackerInit(): void
{
    //Listen to changes on Select form
    let select = document.getElementById('ff_track_type');
    if (select) {
        select.addEventListener('change', function (event: Event) {
            ffTrackTypeChange(event.target as HTMLSelectElement);
        });

    }
    //Intercept form submit
    submitIntercept('ff_track_register');
}

//Track the entity
function ffTrackAdd(): void
{
    //Get the ID input
    let idInput = document.getElementById('ff_track_id') as HTMLInputElement;
    //Get select
    let select = document.getElementById('ff_track_type') as HTMLSelectElement;
    let selectedOption = select.selectedOptions[0];
    let selectText: string;
    if (selectedOption) {
        selectText = selectedOption.text;
    } else {
        selectText = 'Character';
    }
    if (idInput && select) {
        //Get spinner
        let spinner = document.getElementById('ff_track_spinner') as HTMLImageElement;
        spinner.classList.remove('hidden');
        ajax(location.protocol+'//'+location.host+'/api/fftracker/'+select.value+'/'+idInput.value+'/', null, 'json', 'POST', 60000, true).then(data => {
            if (data.data === true) {
                addSnackbar(selectText + ' with ID ' + idInput.value + ' was registered. Check <a href="' + location.protocol + '//' + location.host + '/fftracker/' + select.value + '/' + idInput.value + '/' + '" target="_blank">here</a>.', 'success', 0);
            } else if (data === '404') {
                addSnackbar(selectText + ' with ID ' + idInput.value + ' was not found on Lodestone.', 'failure', 10000);
            } else {
                addSnackbar(data.reason, 'failure', 10000);
            }
            spinner.classList.add('hidden');
        });
    }
}

//Updates pattern for input field
function ffTrackTypeChange(target: HTMLSelectElement): void
{
    //Get the ID input
    let idInput = document.getElementById('ff_track_id') as HTMLInputElement;
    //Set default value for pattern
    let pattern = '^\\d+$';
    //Update pattern value
    switch (target.value) {
        case 'character':
        case 'freecompany':
        case 'linkshell':
            pattern = '^\\d{1,20}$';
            break;
        case 'pvpteam':
        case 'crossworld_linkshell':
            pattern = '^[0-9a-z]{40}$';
            break;
    }
    //Set pattern for the element
    idInput.setAttribute('pattern', pattern);
}
