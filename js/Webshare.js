/*globals getMeta*/
/*exported webShareInit*/

function webShareInit()
{
    //Register WebShare if supported
    if (navigator.share) {
        document.getElementById('shareButton').classList.remove('hidden');
        document.getElementById('shareButton').addEventListener('click', webShare);
    } else {
        document.getElementById('shareButton').classList.add('hidden');
    }
}

//WebShare API call
function webShare() {
    navigator.share({
        title: document.title,
        text: getMeta('og:description') ?? getMeta('description'),// jshint ignore:line
        url: document.location,
    });
}
