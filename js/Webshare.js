/*globals getMeta*/

//WebShare API call
function webShare() {
    'use strict';
    navigator.share({
        title: document.title,
        text: getMeta('og:description') ?? getMeta('description'),
        url: document.location,
    });
}
