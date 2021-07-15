/*exported getMeta, timer, openDetails*/

//Get meta content
function getMeta(metaName) {
    const metas = document.getElementsByTagName('meta');
    for (let i = 0; i < metas.length; i++) {
        if (metas[i].getAttribute('name') === metaName) {
            return metas[i].getAttribute('content');
        }
    }
    return null;
}

//Timer to show remaining or elapsed time
function timer(target, increase = true) {
    setInterval(function() {
        if (parseInt(target.innerHTML) > 0) {
            if (increase === true) {
                target.innerHTML = parseInt(target.innerHTML) + 1;
            } else {
                target.innerHTML = parseInt(target.innerHTML) - 1;
            }
        }
    }, 1000);
}

//Close details tags that are not the target
function openDetails(event) {
    //Get all details tags
    const details = document.querySelectorAll('details');
    //Iterate
    details.forEach((detail) => {
        //If it's not target and does not have "persistent" class - close it
        if (detail !== event.target && detail.classList.contains('persistent') === false) {
            detail.removeAttribute('open');
        }
    });
}
