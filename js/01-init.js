// JSHint in PHPStorm does not look for functions in all files, thus silencing errors for appropriate functions (done in all files)
/*globals showPassToggle, ariaNationOnEvent, ariaNation, loginRadioCheck, webShare, backToTop, timer, colorValue,
colorValueOnEvent, toggleSidebar, toggleNav*/
'use strict';

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
    //Show password functionality
    document.querySelectorAll('.showpassword').forEach(item => {
        item.addEventListener('click', showPassToggle);
    });
    //Register automated aria-invalid attribute adding
    Array.from(document.getElementsByTagName('input')).forEach(item => {
        item.addEventListener('focus', ariaNationOnEvent);
        item.addEventListener('change', ariaNationOnEvent);
        item.addEventListener('input', ariaNationOnEvent);
        //Force update the values right now
        ariaNation(item);
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
    //Enforce placeholder for textarea similar to text inputs
    Array.from(document.getElementsByTagName('textarea')).forEach(item => {
        if (item.hasAttribute('placeholder') === false) {
            item.setAttribute('placeholder', item.value || item.type || 'placeholder');
        }
    });
    //Register function for radio buttons toggling on login form
    document.querySelectorAll('#radio_signinup input[type=radio]').forEach(item => {
        item.addEventListener('change', loginRadioCheck);
    });
    //Force loginRadioCheck for consistency
    loginRadioCheck();
    //Register WebShare if supported
    if (navigator.share) {
        document.getElementById('shareButton').classList.remove('hidden');
        document.getElementById('shareButton').addEventListener('click', webShare);
    } else {
        document.getElementById('shareButton').classList.add('hidden');
    }
    //Close all details except currently selected one
    document.querySelectorAll('details').forEach((details,_,list)=>{
        details.ontoggle =_=> { // jshint ignore:line
            if(details.open && details.classList.contains('persistent') === false) {
                list.forEach(tag =>{
                    if(tag !== details && tag.classList.contains('persistent') === false) {
                        tag.open=false;
                    }
                });
            }
        };
    });
    window.addEventListener('click', function(event){
        document.querySelectorAll('details').forEach((details)=>{
            if(details.classList.contains('popup') === true && details.contains(event.target) === false) {
                details.open=false;
            }
        });
    });
    //Click handling for toggling sidebar
    document.querySelectorAll('#showSidebar, #hideSidebar').forEach(item => {
        item.addEventListener('click', toggleSidebar);
    });
    document.querySelectorAll('#showNav, #hideNav').forEach(item => {
        item.addEventListener('click', toggleNav);
    });
    //Counter for refresh timer
    let refreshTimer = document.getElementById('refresh_timer');
    if (refreshTimer) {
        timer(refreshTimer, false);
    }
}

