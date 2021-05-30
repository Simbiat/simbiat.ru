//Stuff to do on load

// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeline', 'timelineEnd', 'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

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
    'use strict';
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
    });
    //Register function for radio buttons toggling on login form
    document.querySelectorAll('#radio_signinup input[name=signinuptype]').forEach(item => {
        item.addEventListener('change', loginRadioCheck);
    });
    //Force loginRadioCheck for consistency
    loginRadioCheck();
    //Register WebShare if supported
    if (navigator.share) {
        document.getElementById('sharebutton').classList.remove('hidden');
        document.getElementById('sharebutton').addEventListener('click', webShare);
    } else {
        document.getElementById('sharebutton').classList.add('hidden');
    }
}
