// JSHint in PHPStorm does not look for functions in all files, thus silencing errors for appropriate functions (done in all files)
/*globals signInUpInit, ariaInit, webShareInit, backToTop, timer, colorValue, bicInit, detailsInit,
colorValueOnEvent, toggleSidebar, toggleNav, idToHeader, anchorFromHeader, tooltipInit, copyQuoteInit,
placeholders, formInit, galleryInit, fftrackerInit*/
/*exported pageTitle*/
'use strict';

const pageTitle = ' on Simbiat Software';

//Stuff to do on load

// Avoid `console` errors in browsers that lack a console.
(function() {
    let method;
    const noop = function () {};
    const methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeline', 'timelineEnd', 'timeStamp', 'trace', 'warn',
    ];
    let length = methods.length;
    const console = window.console = window.console || {};

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

document.addEventListener('DOMContentLoaded', attachListeners);

//Attaches event listeners
function attachListeners()
{
    //Back-to-top buttons
    document.getElementById('content').addEventListener('scroll', backToTop);
    //Register automated aria-invalid attribute adding
    Array.from(document.getElementsByTagName('input')).forEach(item => {
        ariaInit(item);
        //Add placeholder, if not present. Required more as a precaution for text-like inputs with no placeholder
        if (item.hasAttribute('placeholder') === false) {
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
    signInUpInit();
    webShareInit();
    bicInit();
    detailsInit();
    copyQuoteInit();
    formInit();
    galleryInit();
    fftrackerInit();
    //Click handling for toggling sidebar
    document.querySelectorAll('#showSidebar, #hideSidebar').forEach(item => {
        item.addEventListener('click', toggleSidebar);
    });
    document.querySelectorAll('#showNav, #hideNav').forEach(item => {
        item.addEventListener('click', toggleNav);
    });
    //Add IDs to H1-H6 tags and handle onclick events to copy anchor links
    document.querySelectorAll('h1, h2, h3, h4, h5, h6').forEach(item => {
        idToHeader(item);
        item.addEventListener('click', anchorFromHeader);
    });
    //Counter for refresh timer
    let refreshTimer = document.getElementById('refresh_timer');
    if (refreshTimer) {
        timer(refreshTimer, false);
    }
    //Floating tooltip
    tooltipInit();
}

