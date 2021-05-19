//Stuff to do on load
document.addEventListener('DOMContentLoaded', attachListeners);

//Attaches event listeners
function attachListeners()
{
    //Show password functionality
    document.querySelectorAll('.showpassword').forEach(item => {
        item.addEventListener('mousedown', showPassToggle);
    })
    //Register automated aria-invalid attribute adding
    Array.from(document.getElementsByTagName('input')).forEach(item => {
        item.addEventListener('focus', ariaNationOnEvent);
        item.addEventListener('change', ariaNationOnEvent);
        item.addEventListener('input', ariaNationOnEvent);
        //Force update the values right now
        ariaNation(item);
    })
    //Register function for radio buttons toggling on login form
    document.querySelectorAll('#radio_signinup input[name=signinuptype]').forEach(item => {
        item.addEventListener('change', loginRadioCheck);
    })
    //Force loginRadioCheck for consistency
    loginRadioCheck();
    //Register WebShare if supported
    if (navigator.share) {
        document.getElementById('sharebutton').classList.remove('hideme');
        document.getElementById('sharebutton').addEventListener('click', webShare);
    } else {
        document.getElementById('sharebutton').classList.add('hideme');
    }
}