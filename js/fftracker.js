/*exported fftrackerInit*/
/*globals ajax, addSnackbar*/

function fftrackerInit()
{
    //Listen to changes on Select form
    let select = document.getElementById('ff_track_type');
    if (select) {
        select.addEventListener('change', function (event) {
            ffTrackTypeChange(event.target);
        });

    }
    //Intercept form submit
    let form = document.getElementById('ff_track_register');
    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            event.stopPropagation();
            ffTrackAdd();
            return false;
        });
        form.onkeydown = function(event){
            if(event.code === 'Enter'){
                event.preventDefault();
                event.stopPropagation();
                ffTrackAdd();
                return false;
            }
        };
    }
}

//Track the entity
function ffTrackAdd()
{
    //Get the ID input
    let idInput = document.getElementById('ff_track_id');
    //Get select
    let select = document.getElementById('ff_track_type');
    if (idInput && select) {
        //Get spinner
        let spinner = document.getElementById('ff_track_spinner');
        spinner.classList.remove('hidden');
        ajax(location.protocol+'//'+location.host+'/api/fftracker/'+select.value+'/'+idInput.value+'/register/').then(data => {
            if (data === true) {
                addSnackbar(select.options[select.selectedIndex].text + ' with ID ' + idInput.value + ' was registered. Check <a href="' + location.protocol + '//' + location.host + '/fftracker/' + select.value + '/' + idInput.value + '/' + '" target="_blank">here</a>.', 'success', 0);
            } else if (data === '404') {
                addSnackbar(select.options[select.selectedIndex].text + ' with ID ' + idInput.value + ' was not found on Lodestone.', 'failure', 10000);
            } else {
                addSnackbar(select.options[select.selectedIndex].text + ' with ID ' + idInput.value + ' failed to be registered. Please, try again later.', 'failure', 10000);
            }
            spinner.classList.add('hidden');
        });
    }
}

//Updates pattern for input field
function ffTrackTypeChange(target)
{
    //Get the ID input
    let idInput = document.getElementById('ff_track_id');
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
