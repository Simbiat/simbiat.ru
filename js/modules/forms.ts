//Switch to determine which function to call on submit
const submitFunctions: { [key: string]: string } = {
    'signinup': 'singInUpSubmit',
    'addMailForm': 'addMail',
    'ff_track_register': 'ffTrackAdd',
    'password_change': 'passwordChange',
};

//Function to intercept form submission
function submitIntercept(formId: string): void
{
    let form = document.getElementById(formId);
    if (form && submitFunctions[formId]) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            event.stopPropagation();
            //Have no idea what it does not like
            // @ts-ignore
            window[submitFunctions[formId]]();
            return false;
        });
        form.onkeydown = function(event){
            if(event.code === 'Enter'){
                event.preventDefault();
                event.stopPropagation();
                //Have no idea what it does not like
                // @ts-ignore
                window[submitFunctions[formId]]();
                return false;
            }
            return true;
        };
    }
}
