/*globals getMeta*/
/*exported webShare*/

//WebShare API call
function webShare() {
    navigator.share({
        title: document.title,
        text: getMeta('og:description') ?? getMeta('description'),
        url: document.location,
    });
}
