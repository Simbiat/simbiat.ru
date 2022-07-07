'use strict';
const pageTitle = ' on Simbiat Software';

//Stuff to do on load

document.addEventListener('DOMContentLoaded', init);
window.addEventListener('hashchange', function() {hashCheck(false);});

//Runs initialization routines
function init()
{
    //Back-to-top buttons
    let content = document.getElementById('content') as HTMLDivElement;
    content.addEventListener('scroll', backToTop);
    //Register automated aria-invalid attribute adding
    Array.from(document.getElementsByTagName('input')).forEach(item => {
        ariaInit(item);
        //Add placeholder, if not present. Required more as a precaution for text-like inputs with no placeholder
        if (!item.hasAttribute('placeholder')) {
            item.setAttribute('placeholder', item.value || item.type || 'placeholder');
        }
        //Attach listeners for color picker
        if (item.type === 'color') {
            item.addEventListener('focus', colorValueOnEvent);
            item.addEventListener('change', colorValueOnEvent);
            item.addEventListener('input', colorValueOnEvent);
            colorValue(item);
        }
    });
    placeholders();
    ucInit();
    new WebShare();
    bicInit();
    detailsInit();
    copyQuoteInit();
    formInit();
    fftrackerInit();
    new Gallery();
    //Click handling for toggling sidebar
    document.querySelectorAll('#showSidebar, #hideSidebar').forEach(item => {
        item.addEventListener('click', toggleSidebar);
    });
    document.querySelectorAll('#showNav, #hideNav').forEach(item => {
        item.addEventListener('click', toggleNav);
    });
    //Add IDs to H1-H6 tags and handle onclick events to copy anchor links
    document.querySelectorAll('h1:not(#h1title), h2, h3, h4, h5, h6').forEach(item => {
        idToHeader(item as HTMLHeadingElement);
        item.addEventListener('click', anchorFromHeader);
    });
    //Counter for refresh timer
    let refreshTimer = document.getElementById('refresh_timer');
    if (refreshTimer) {
        timer(refreshTimer, false);
    }
    //Floating tooltip
    new Tooltip();
    cleanGET();
    hashCheck(true);
}

//Remove cacheReset flag
function cleanGET()
{
    let url = new URL(document.location.href);
    let params = new URLSearchParams(url.search);
    params.delete('cacheReset');
    if (params.toString() === '') {
        window.history.replaceState(null, document.title, location.pathname + location.hash);
    } else {
        window.history.replaceState(null, document.title, '?' + params + location.hash);
    }
}

//Special processing for special hash links
function hashCheck(hashUpdate: boolean)
{
    let url = new URL(document.location.href);
    let hash = url.hash;
    const galleryLink = new RegExp('#gallery=\\d+', 'ui');
    if (galleryLink.test(hash)) {
        let imageID = Number(hash.replace(/(#gallery=)(\d+)/ui, '$2'));
        if (imageID) {
            if (Gallery.images[imageID - 1]) {
                new Gallery().open(Gallery.images[imageID - 1] as HTMLElement, hashUpdate);
            } else {
                new Snackbar().add('Image number '+imageID+' not found on page', 'failure');
                window.history.replaceState(null, document.title, document.location.href.replace(hash, ''));
            }
        }
    }
}
