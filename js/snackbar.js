/*exported addSnackbar*/
let notificationIndex = 0;

//Add snackbar notification
function addSnackbar(text, color = 'text', milliseconds = 3000) {
    //Get container
    const snacks = document.getElementById('snacksContainer');
    //Generate element
    let snack = document.createElement('dialog');
    //Set ID for notification
    let id = notificationIndex++;
    snack.setAttribute('id', 'snackbar' + id);
    //Add snackbar class
    snack.classList.add('snackbar');
    //Add text
    snack.innerHTML = '<span class="snack_text">' + text + '</span><input id="closeSnack' + id + '" class="navIcon snack_close" alt="Close notification" type="image" src="/img/close.svg" aria-invalid="false" placeholder="image">';
    //Add class for color
    snack.classList.add(color);
    //Add element to parent
    snacks.appendChild(snack);
    //Add animation class
    snack.classList.add('fadeIn');
    //Add event listener to close button
    document.getElementById('closeSnack' + id).addEventListener('click', function() {removeSnack(snack);});
    //Set time to remove the child
    if (milliseconds > 0) {
        setTimeout(function() {
            removeSnack(snack);
        }, milliseconds);
    }
}

function removeSnack(snack) {
    //Get container
    const snacks = document.getElementById('snacksContainer');
    //Animate removal
    snack.classList.remove('fadeIn');
    snack.classList.add('fadeOut');
    //Actual removal
    snack.addEventListener('animationend', function() {snacks.removeChild(snack);});
}
