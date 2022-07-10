'use strict';
const pageTitle = ' on Simbiat Software';

//Stuff to do on load

document.addEventListener('DOMContentLoaded', init);
window.addEventListener('hashchange', function() {hashCheck();});

//Runs initialization routines
function init()
{
    //Back-to-top buttons
    customElements.define('back-to-top', BackToTop);
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
    bicInit();
    detailsInit();
    copyQuoteInit();
    formInit();
    fftrackerInit();
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
    //Web-share button
    customElements.define('web-share', WebShare);
    //Floating tooltip
    customElements.define('tool-tip', Tooltip);
    //Snackbar close button
    customElements.define('snack-close', SnackbarClose);
    //Gallery overlay
    customElements.define('gallery-overlay', Gallery);
    //Define image carousels
    customElements.define('image-carousel', CarouselList);
    cleanGET();
    hashCheck();
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
function hashCheck()
{
    let url = new URL(document.location.href);
    let hash = url.hash;
    let Gallery = document.getElementsByTagName('gallery-overlay')[0] as Gallery;
    const galleryLink = new RegExp('#gallery=\\d+', 'ui');
    if (galleryLink.test(hash)) {
        let imageID = Number(hash.replace(/(#gallery=)(\d+)/ui, '$2'));
        if (imageID) {
            if (Gallery.images[imageID - 1]) {
                Gallery.current = imageID - 1;
            } else {
                new Snackbar('Image number '+imageID+' not found on page', 'failure');
                window.history.replaceState(null, document.title, document.location.href.replace(hash, ''));
            }
        }
    } else {
        Gallery.close();
    }
}
