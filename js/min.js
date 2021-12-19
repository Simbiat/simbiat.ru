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

/*exported getMeta, timer, openDetails, colorValue, colorValueOnEvent, toggleSidebar, toggleNav, tooltipInit,
updateHistory*/

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

//Get and show color in attribute. For some reason, CSS's attr(value) does not show the value, if I do not do this
function colorValue(target) {
    target.setAttribute('value', target.value);
}
function colorValueOnEvent(event) {
    colorValue(event.target);
}

//Toggle sidebar for small screens
function toggleSidebar(event) {
    event.preventDefault();
    const sidebar = document.getElementById('sidebar');
    if (sidebar.classList.contains('shown')) {
        sidebar.classList.remove('shown');
    } else {
        sidebar.classList.add('shown');
    }
}
//Toggle navigation for small screens
function toggleNav(event) {
    event.preventDefault();
    const sidebar = document.getElementById('navigation');
    if (sidebar.classList.contains('shown')) {
        sidebar.classList.remove('shown');
    } else {
        sidebar.classList.add('shown');
    }
}

//Update document title and push to history. Required, since browsers mostly ignore title argument in pushState
function updateHistory(newUrl, title)
{
    document.title = title;
    window.history.pushState(title, title, newUrl);
}
/*globals addSnackbar, getMeta*/
/*exported ajax*/

async function ajax(url, request = null, type ='json', method = 'GET', timeout = 60000)
{
    let result;
    let controller = new AbortController();
    setTimeout(() => controller.abort(), timeout);
    //Wrapping in try to allow timeout
    try {
        let response = await fetch(url, {
            method: method,
            mode: 'same-origin',
            //Cache is allowed, but essentially, only if stale. While this may put some extra stress on server, for API it's better this way
            cache: 'no-cache',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRFToken': getMeta('X-CSRFToken'),
            },
            //Do not follow redirects. If redirected - something is wrong on API level
            redirect: 'error',
            referrer: window.location.href,
            referrerPolicy: 'same-origin',
            //integrity: '', useful if we know expected hash of the response
            keepalive: false,
            signal: controller.signal,
            body: method === 'POST' ? JSON.stringify(request) : null,
        });
        if (!response.ok) {
            addSnackbar('Request to "'+url+'" returned code '+response.status, 'failure', 10000);
            return false;
        } else {
            if (type === 'json') {
                result = await response.json();
            } else if (type === 'blob') {
                result = await response.blob();
            } else if (type === 'array') {
                result = await response.arrayBuffer();
            } else if (type === 'form') {
                result = await response.formData();
            } else {
                result = await response.text();
            }
        }
        return result;
    } catch(err) {
        if (err.name === 'AbortError') {
            addSnackbar('Request to "'+url+'" timed out after '+timeout+' milliseconds', 'failure', 10000);
            return false;
        } else {
            addSnackbar('Request to "'+url+'" failed on fetch operation', 'failure', 10000);
            throw err;
        }
    }
}
/*exported ariaInit*/

//Accessibility related functions

//General initialization
function ariaInit(item)
{
    item.addEventListener('focus', ariaNationOnEvent);
    item.addEventListener('change', ariaNationOnEvent);
    item.addEventListener('input', ariaNationOnEvent);
    //Force update the values right now
    ariaNation(item);
}

//Adding some aria attributes to input elements.
function ariaNation(inputElement)
{
    //Adjust aria-invalid based on whether input is valid or not
    inputElement.setAttribute('aria-invalid', !inputElement.validity.valid);
    //Add aria-required with value based on whether "required" attribute is present
    if (inputElement.hasAttribute('type') === true && ['text', 'search', 'url', 'tel', 'email', 'password', 'date', 'month', 'week', 'time', 'datetime-local', 'number', 'checkbox', 'radio', 'file',].includes(inputElement.getAttribute('type'))) {
        if (inputElement.required === true) {
            inputElement.setAttribute('aria-required', true);
        } else {
            inputElement.setAttribute('aria-required', false);
        }
    }
    //Add checkbox role
    if (inputElement.hasAttribute('type') === true && inputElement.getAttribute('type') === 'checkbox') {
        inputElement.setAttribute('role', 'checkbox');
        //Add aria-checked value based on whether checkbox is checked
        inputElement.setAttribute('aria-checked', inputElement.checked);
        //Handle indeterminate state of checkboxes
        if (inputElement.indeterminate === true) {
            inputElement.setAttribute('aria-checked', 'mixed');
        }
    }
}

//This should be attached to all input tags to "change" and "input" events. Preferably to "focus" as well.
function ariaNationOnEvent(event)
{
    ariaNation(event.target);
}
/*exported backToTop*/

function backToTop(event) {
    //Check position of the scroll
    if (event.target.scrollTop === 0) {
        //Hide buttons
        document.querySelectorAll('.back-to-top').forEach(item => {
            item.classList.add('hidden');
            item.removeEventListener('click', scrollToTop);
        });
    } else {
        //Show buttons
        document.querySelectorAll('.back-to-top').forEach(item => {
            item.classList.remove('hidden');
            item.addEventListener('click', scrollToTop);
        });
    }
}
function scrollToTop() {
    document.getElementById('content').scrollTop = 0;
}
/*globals ajax, pageTitle, updateHistory, addSnackbar*/
/*exported bicInit*/

function bicInit()
{
    let bicKey = document.getElementById('bic_key');
    let accKey = document.getElementById('account_key');
    if (bicKey && accKey) {
        bicKey.addEventListener('input', bicCalc);
        accKey.addEventListener('input', bicCalc);
    }
    let refresh = document.getElementById('bicRefresh');
    if (refresh) {
        refresh.addEventListener('click', bicRefresh);
    }
}

function bicCalc()
{
    let result = document.getElementById('accCheckResult');
    let bicKey = document.getElementById('bic_key');
    let accKey = document.getElementById('account_key');
    let bicKeySample = document.getElementById('bic_key_sample');
    let accKeySample = document.getElementById('account_key_sample');
    result.classList.remove(...result.classList);
    if (/^[0-9]{9}$/u.exec(bicKey.value) === null) {
        result.classList.add('failure');
        result.innerHTML = 'Неверный формат БИКа';
        bicStyle(bicKeySample, 'warning', 'БИК');
        return;
    } else {
        bicStyle(bicKeySample, 'success', bicKey.value);
    }
    if (/^[0-9]{5}[0-9АВСЕНКМРТХавсенкмртх][0-9]{14}$/u.exec(accKey.value) === null) {
        result.classList.add('failure');
        result.innerHTML = 'Неверный формат счёта';
        bicStyle(accKeySample, 'warning', 'СЧЁТ');
        return;
    } else {
        bicStyle(accKeySample, 'success', accKey.value);
    }
    //Change address
    updateHistory(location.protocol+'//'+location.host+'/bictracker/keying/'+bicKey.value+'/'+accKey.value+'/', 'Ключевание счёта '+accKey.value+pageTitle);
    //Initiate request
    result.classList.add('warning');
    result.innerHTML = 'Проверяем...';
    //Get spinner
    let spinner = document.getElementById('bic_spinner');
    spinner.classList.remove('hidden');
    ajax(location.protocol+'//'+location.host+'/api/bictracker/keying/'+bicKey.value+'/'+accKey.value+'/').then(data => {
        result.classList.remove(...result.classList);
        if (data === true) {
            result.classList.add('success');
            result.innerHTML = 'Правильное ключевание';
        } else {
            result.classList.add('failure');
            if (data === false) {
                result.innerHTML = 'Непредвиденная ошибка';
            } else {
                result.innerHTML = 'Неверное ключевание. Ожидаемый ключ: ' + data + ' (' + accKey.value.replace(/(^[0-9]{5}[0-9АВСЕНКМРТХавсенкмртх][0-9]{2})([0-9])([0-9]{11})$/u, '$1<span class="success">' + data + '</span>$3') + ')';
            }
        }
        spinner.classList.add('hidden');
    });
}

//Helper function for styling
function bicStyle(element, newClass, text = '')
{
    element.classList.remove(...element.classList);
    element.classList.add(newClass);
    element.innerHTML = text;
}

//Refresh BIC library through API
function bicRefresh(event)
{
    let refresh = event.target;
    if (refresh.classList.contains('spin')) {
        //It already has been clicked, cancel event
        event.stopPropagation();
        event.preventDefault();
    } else {
        refresh.classList.add('spin');
        setTimeout(async function() {
            await ajax(location.protocol + '//' + location.host + '/api/bictracker/dbupdate/', null, 'json', 'GET', 300000).then(data => {
                if (data === true) {
                    addSnackbar('Библиотека БИК обновлена', 'success');
                } else {
                    addSnackbar('Не удалось обновить библиотеку БИК', 'failure', 10000);
                }
            });
            refresh.classList.remove('spin');
        }, 500);
    }
}
/*globals addSnackbar*/
/*exported copyQuoteInit, placeholders, detailsInit*/
/*These are functions, that are used to somehow style or standardise different elements*/

function copyQuoteInit()
{
    document.querySelectorAll('samp, code, blockquote').forEach(item => {
        //Modifying innerHTML instead of insertBefore, since block may not have any actual children in the first place, and as per https://developer.mozilla.org/en-US/docs/Web/API/Node/insertBefore
        //"When the element does not have a first child, then firstChild is null. The element is still appended to the parent, after the last child."
        //This results in same effect as with appendChild, that the image is inserted at the end, which is not what we want
        item.innerHTML = '<img loading="lazy" decoding="async"  src="/img/copy.svg" alt="Copy block" class="copyQuote">' + item.innerHTML;
    });
    document.querySelectorAll('.copyQuote, q').forEach(item => {
        item.addEventListener('click', copyQuote);
    });
}
function copyQuote(event)
{
    let node;
    if (event.target.tagName.toLowerCase() === 'q') {
        node = event.target;
    } else {
        node = event.target.parentElement;
    }
    let tag;
    switch (node.tagName.toLowerCase()) {
        case 'samp':
            tag = 'sample';
            break;
        case 'code':
            tag = 'code';
            break;
        case 'blockquote':
        case 'q':
            tag = 'quote';
            break;
    }
    navigator.clipboard.writeText(node.textContent).then(function() {
        addSnackbar(tag.charAt(0).toUpperCase() + tag.slice(1) + ' copied to clipboard', 'success');
    }, function() {
        addSnackbar('Failed to copy '+tag,'failure');
    });
}

function placeholders()
{
    //Enforce placeholder for textarea similar to text inputs
    Array.from(document.getElementsByTagName('textarea')).forEach(item => {
        if (item.hasAttribute('placeholder') === false) {
            item.setAttribute('placeholder', item.value || item.type || 'placeholder');
        }
    });
}

//Function to handle details elements
function detailsInit()
{
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
}
/*exported fftrackerInit*/
/*globals ajax, addSnackbar*/

function fftrackerInit()
{
    //Listen to changes on Select form
    let select = document.getElementById('ff_track_type');
    if (select) {
        select.addEventListener('change', function (event) {
            ffTrackTypeChange(event.target);
        });

    }
    //Intercept form submit
    let form = document.getElementById('ff_track_register');
    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            event.stopPropagation();
            ffTrackAdd();
            return false;
        });
        form.onkeydown = function(event){
            if(event.code === 'Enter'){
                event.preventDefault();
                event.stopPropagation();
                ffTrackAdd();
                return false;
            }
        };
    }
}

//Track the entity
function ffTrackAdd()
{
    //Get the ID input
    let idInput = document.getElementById('ff_track_id');
    //Get select
    let select = document.getElementById('ff_track_type');
    if (idInput && select) {
        //Get spinner
        let spinner = document.getElementById('ff_track_spinner');
        spinner.classList.remove('hidden');
        ajax(location.protocol+'//'+location.host+'/api/fftracker/'+select.value+'/'+idInput.value+'/register/').then(data => {
            if (data === true) {
                addSnackbar(select.options[select.selectedIndex].text + ' with ID ' + idInput.value + ' was registered. Check <a href="' + location.protocol + '//' + location.host + '/fftracker/' + select.value + '/' + idInput.value + '/' + '" target="_blank">here</a>.', 'success', 0);
            } else if (data === '404') {
                addSnackbar(select.options[select.selectedIndex].text + ' with ID ' + idInput.value + ' was not found on Lodestone.', 'failure', 10000);
            } else {
                addSnackbar(select.options[select.selectedIndex].text + ' with ID ' + idInput.value + ' failed to be registered. Please, try again later.', 'failure', 10000);
            }
            spinner.classList.add('hidden');
        });
    }
}

//Updates pattern for input field
function ffTrackTypeChange(target)
{
    //Get the ID input
    let idInput = document.getElementById('ff_track_id');
    //Set default value for pattern
    let pattern = '^\\d+$';
    //Update pattern value
    switch (target.value) {
        case 'character':
        case 'freecompany':
        case 'linkshell':
            pattern = '^\\d{1,20}$';
            break;
        case 'pvpteam':
        case 'crossworld_linkshell':
            pattern = '^[0-9a-z]{40}$';
            break;
    }
    //Set pattern for the element
    idInput.setAttribute('pattern', pattern);
}
/*globals addSnackbar*/
/*exported formInit*/
//List of input types, that are "textual" by default, thus can be tracked through keypress and paste events. In essence,
// these are types, that support maxlength attribute
const textInputTypes = ['email', 'password', 'search', 'tel', 'text', 'url', ];

//List of other input types, that do not make much sense to be tracked through keypress or paste events
//Including date/time types, even though some of them may fallback to textual fields. Doing this, since you can't predict this by checking browser version.
//Not including hidden (since it's hidden), image (since its purpose is unclear by default),
//range (unclear how to track to actually determine, that user stopped interaction),
//reset and submit (due to their purpose)
const nonTextInputTypes = ['checkbox', 'color', 'date', 'datetime-local', 'file', 'month', 'number', 'radio', 'time', 'week',];

//Handle dynamic action attribute for search inputs
function formInit()
{
    document.querySelectorAll('form').forEach((item)=>{
        item.addEventListener('keypress', formEnter);
    });
    //Forms with dynamic actions (expected to be search forms only at the time of writing)
    document.querySelectorAll('form[data-baseURL] input[type=search]').forEach((item)=>{
        item.addEventListener('input', searchAction);
        item.addEventListener('change', searchAction);
        item.addEventListener('focus', searchAction);
    });
    document.querySelectorAll('form input').forEach((item)=>{
        if (textInputTypes.includes(item.type)) {
            //Somehow backspace can be tracked only on keydown, not keypress
            item.addEventListener('keydown', inputBackSpace);
            if (item.getAttribute('maxlength')) {
                item.addEventListener('input', autoNext);
                item.addEventListener('change', autoNext);
                item.addEventListener('paste', pasteSplit);
            }
        }
        if (nonTextInputTypes.includes(item.type)) {

        }
    });
}

function searchAction(event)
{
    let search = event.target;
    let form = search.form;
    if (search.value === '') {
        form.action = form.getAttribute('data-baseURL');
    } else {
        form.action = form.getAttribute('data-baseURL') + search.value;
    }
    //Ensure that form will use GET method. This adds unnecessary question mark to the end of the URL, but it's better than form resubmit prompt
    form.method = 'get';
}


//Prevent form submit on Enter, if action is empty (otherwise this causes page reload with additional question mark in address
function formEnter(event)
{
    let form = event.target.form;
    if ((event.keyCode || event.charCode || 0) === 13 && (!form.action || !(form.getAttribute('data-baseURL') && location.protocol + '//' + location.host+form.getAttribute('data-baseURL') !== form.action))) {
        event.stopPropagation();
        event.preventDefault();
        return false;
    }
}

//Track backspace and focus previous input field, if input is empty, when it's pressed
function inputBackSpace(event)
{
    let current = event.target;
    if ((event.keyCode || event.charCode || 0) === 8 && !current.value) {
        let moveTo = nextInput(current, true);
        if (moveTo) {
            moveTo.focus();
            //Ensure, that cursor ends up at the end of the previous field
            moveTo.selectionStart = moveTo.selectionEnd = moveTo.value.length;
        }
    }
}

//Focus next field, if current is filled to the brim and valid
function autoNext(event)
{
    let current = event.target;
    //Get length attribute
    let maxLength = parseInt(current.getAttribute('maxlength'));
    //Check it against value length
    if (maxLength && current.value.length === maxLength && current.validity.valid) {
        let moveTo = nextInput(current, false);
        if (moveTo) {
            moveTo.focus();
        }
    }
}

async function pasteSplit(event)
{
    let permission = await navigator.permissions.query({ name: 'clipboard-read',});
    //Check permission is granted or not
    if (permission.state === 'denied') {
        //It's explicitly denied, thus cancelling script
        return false;
    }
    //Get buffer
    navigator.clipboard.readText().then(result => {
        let buffer = result.toString();
        //Get initial element
        let current = event.target;
        //Get initial length attribute
        let maxLength = parseInt(current.getAttribute('maxlength'));
        //Loop while the buffer is too large
        while (current && maxLength && buffer.length > maxLength) {
            //Ensure input value is updated
            current.value = buffer.substring(0, maxLength);
            //Trigger input event to bubble any bound events
            current.dispatchEvent(new Event('input', {
                bubbles: true,
                cancelable: true,
            }));
            //Do not spill over if a field is invalid
            if (!current.validity.valid) {
                return false;
            }
            //Update buffer value (not the buffer itself)
            buffer = buffer.substring(maxLength);
            //Get next node
            current = nextInput(current);
            if (current) {
                //Focus to provide visual identification of a switch
                current.focus();
                //Update maxLength
                maxLength = parseInt(current.getAttribute('maxlength'));
            }
        }
        //Check if we still have a valid node
        if (current) {
            //Dump everything we can from leftovers
            current.value = buffer;
            //Trigger input event to bubble any bound events
            current.dispatchEvent(new Event('input', {
                bubbles: true,
                cancelable: true,
            }));
        }
    }).catch(err => {
        //Most likely user denied request. Check status
        navigator.permissions.query({ name: 'clipboard-read',}).then(newPerm => {
            if (newPerm.state === 'granted') {
                console.error('Failed to read clipboard', err);
            } else {
                addSnackbar('Failed to read clipboard', 'warning');
            }
        }).catch(errPerm => {
            console.error('Failed to check clipboard permission', errPerm);
        });
    });
}

//Find next/previous input
function nextInput(initial, reverse = false)
{
    //Get form
    let form = initial.form;
    //Iterate inputs inside the form. Not using previousElementSibling, because next/previous input may not be a sibling on the same level
    if (form) {
        let previous;
        for (let moveTo of form.querySelectorAll('input')) {
            if (reverse) {
                //Check if current element in loop is the initial one, meaning
                if (moveTo === initial) {
                    //If previous is not empty - share it. Otherwise - false, since initial input is first in the form
                    if (previous) {
                        return previous;
                    } else {
                        return false;
                    }
                }
            } else {
                //If we are moving forward and initial node is the previous one
                if (previous === initial) {
                    return moveTo;
                }
            }
            //Update previous input
            previous = moveTo;
        }
    }
    return false;
}
/*exported galleryInit*/
let galleryCurrent = 1;
let galleryList = [];

function galleryInit()
{
    //Attach trigger for opening overlay
    Array.from(document.getElementsByClassName('galleryZoom')).forEach(item => {
        item.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            galleryOpen(event.target);
            return false;
        });
    });
    //Attach trigger for closing the overlay
    document.getElementById('galleryClose').addEventListener('click', galleryClose);
    //Attach triggers for navigation
    document.getElementById('galleryPrevious').addEventListener('click', galleryPrevious);
    document.getElementById('galleryNext').addEventListener('click', galleryNext);
}

function galleryOpen(image)
{
    //Get list of images
    galleryCount();
    //Get current image
    let link;
    if (image.tagName.toLowerCase() === 'a') {
        link = image;
    } else {
        link = image.closest('a');
    }
    //Get current index
    galleryCurrent = galleryGetIndex(link);
    //Load image
    galleryLoadImage();
    //Show overlay
    document.getElementById('galleryOverlay').classList.remove('hidden');
}

function galleryLoadImage()
{
    //Get element from array
    let image = galleryList[galleryCurrent - 1];
    //Get name
    let name = image.getAttribute('data-tooltip') ?? image.getAttribute('title') ?? image.href.replace(/^.*[\\\/]/u, '');// jshint ignore:line
    //Update elements
    document.getElementById('galleryName').innerText = name;
    document.getElementById('galleryNameLink').innerHTML = '<a href="'+image.href+'" target="_blank"><img loading="lazy" decoding="async" class="linkIcon" alt="Open in new tab" src="/img/newtab.svg"></a>';
    document.getElementById('galleryTotal').innerText = galleryList.length.toString();
    document.getElementById('galleryCurrent').innerText = galleryCurrent.toString();
    document.getElementById('galleryImage').innerHTML = '<img id="galleryLoadedImage" loading="lazy" decoding="async" alt="'+name+'" src="'+image.href+'">';
    document.getElementById('galleryLoadedImage').addEventListener('click', galleryZoom);
}

function galleryClose()
{
    document.getElementById('galleryOverlay').classList.add('hidden');
}

function galleryCount()
{
    //Reset array
    galleryList = [];
    //Populate array
    galleryList = document.querySelectorAll('.galleryZoom');
}

function galleryGetIndex(link)
{
    return Array.from(galleryList).indexOf(link) + 1;
}

function galleryPrevious()
{
    galleryCurrent = galleryCurrent - 1;
    //Scroll over
    if (galleryCurrent < 1) {
        galleryCurrent = galleryList.length;
    }
    //Load image
    galleryLoadImage(galleryCurrent);
}

function galleryNext()
{
    galleryCurrent = galleryCurrent + 1;
    //Scroll over
    if (galleryCurrent > galleryList.length) {
        galleryCurrent = 1;
    }
    //Load image
    galleryLoadImage(galleryCurrent);
}

function galleryZoom()
{
    let image = document.getElementById('galleryLoadedImage');
    if (image.classList.contains('zoomedIn')) {
        image.classList.remove('zoomedIn');
    } else {
        image.classList.add('zoomedIn');
    }
}
/*exported idToHeader, anchorFromHeader*/
/*globals addSnackbar*/

//Add ID attribute to header tags, if it's missing
function idToHeader(hTag) {
    if (hTag.hasAttribute('id') === false) {
        //Get initial ID
        let id = hTag.textContent.replaceAll(/\s/gmu, `_`).replaceAll(/[^\p{L}\p{N}_\-]/gmu, ``).replaceAll(/(^.{1,64})(.*$)/gmu, `$1`);
        //Get ID index, in case it's already used
        let index = 1;
        let altId = id;
        //Check if altID exists
        while (document.getElementById(altId)) {
            //Increase index
            index++;
            altId = id + '_' + index;
        }
        hTag.setAttribute('id', altId);
    }
}

//Copy anchor to the header tag on click
function anchorFromHeader(event) {
    //Generate and copy anchor link to clipboard
    navigator.clipboard.writeText(window.location.href.replaceAll(/(^[^#]*)(#.*)?$/gmu, `$1`) + '#' + event.target.getAttribute('id')).then(function() {
        addSnackbar('Anchor link for "' + event.target.textContent + '" copied to clipboard', 'success');
    }, function() {
        addSnackbar('Failed to copy anchor link for "' + event.target.textContent + '"','failure');
    });
}
/*globals ariaNation*/
/*exported signInUpInit*/

function signInUpInit()
{
    //Show password functionality
    document.querySelectorAll('.showpassword').forEach(item => {
        item.addEventListener('click', showPassToggle);
    });
    //Register function for radio buttons toggling on login form
    document.querySelectorAll('#radio_signinup input[type=radio]').forEach(item => {
        item.addEventListener('change', loginRadioCheck);
    });
    //Force loginRadioCheck for consistency
    loginRadioCheck();
}

//Regex for proper email. This is NOT JS Regex, thus it has doubled slashes.
const emailRegex = '[a-zA-Z0-9.!#$%&\'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*';
//Regex for username. This is NOT JS Regex, thus it has doubled slashes.
const userRegex = '[^\\/\\\\\\[\\]:;|=$%#@&\\(\\)\\{\\}!,+*?<>\\0\\t\\r\\n\\x00-\\x1F\\x7F\\x0b\\f\\x85\\v\\cY\\b]{1,64}';

//Show or hide password. Should be attached to .showpassword class to "mousedown" event
function showPassToggle(event)
{
    //Prevent focus stealing
    event.preventDefault();
    let eyeIcon = event.target;
    let passField = eyeIcon.parentElement.getElementsByTagName('input').item(0);
    if (passField.type === 'password') {
        passField.type = 'text';
        eyeIcon.title = 'Hide password';
    } else {
        passField.type = 'password';
        eyeIcon.title = 'Show password';
    }
}

//Password strength check. Purely as advise, nothing more.
function passwordStrengthOnEvent(event)
{
    //Attempt to get extra values to check against

    //Get element where we will be showing strength
    let strengthField = event.target.parentElement.querySelectorAll('.password_strength').item(0);
    //Get strength
    let strength = passwordStrength(event.target.value);
    //Set text
    strengthField.innerHTML = strength;
    //Remove classes
    strengthField.classList.remove('password_weak', 'password_medium', 'password_strong', 'password_very_strong');
    //Add class
    if (strength === 'very strong') {
        strengthField.classList.add('password_very_strong');
    } else {
        strengthField.classList.add('password_'+strength);
    }
}

//Actual check
function passwordStrength(password, extras = [])
{
    //Assigning points for the password
    let points = 0;
    //Check that it's long enough
    if (/.{8,}/u.test(password) === true) {
        points++;
    }
    //Add one more point, if it's twice as long as minimum requirement
    if (/.{16,}/u.test(password) === true) {
        points++;
    }
    //Add one more point, if it's 3 times as long as minimum requirement
    if (/.{32,}/u.test(password) === true) {
        points++;
    }
    //Add one more point, if it's 64 characters or more
    if (/.{64,}/u.test(password) === true) {
        points++;
    }
    //Check for lower case letters
    if (/\p{Ll}/u.test(password) === true) {
        points++;
    }
    //Check for upper case letters
    if (/\p{Lu}/u.test(password) === true) {
        points++;
    }
    //Check for letters without case (glyphs)
    if (/\p{Lo}/u.test(password) === true) {
        points++;
    }
    //Check for numbers
    if (/\p{N}/u.test(password) === true) {
        points++;
    }
    //Check for punctuation
    if (/[\p{P}\p{S}]/u.test(password) === true) {
        points++;
    }
    //Reduce point for repeating characters
    if (/(.)\1{2,}/u.test(password) === true) {
        points--;
    }
    //Check against extra values. If password contains any of them - reduce points
    if (extras !== []) {

    }
    //Return value based on points. Note, that order is important.
    if (points <= 2) {
        return 'weak';
    } else if (2 < points && points < 5) {
        return 'medium';
    } else if (5 < points && points < 9) {
        return 'very strong';
    } else if (points === 5) {
        return 'strong';
    }
}

//Handle some adjustments when using radio-button switch
function loginRadioCheck()
{
    //Assign actual elements to variables
    let existUser = document.getElementById('radio_existuser');
    let newUser = document.getElementById('radio_newuser');
    let forget = document.getElementById('radio_forget');
    let login = document.getElementById('signinup_email');
    let password = document.getElementById('signinup_password');
    let button = document.getElementById('signinup_submit');
    let rememberme = document.getElementById('rememberme');
    //Adjust elements based on the toggle
    if (existUser && existUser.checked === true) {
        //Whether password field is required
        password.required = true;
        //Autocomplete suggestion for password
        password.setAttribute('autocomplete', 'current-password');
        //Autocomplete suggestion for login
        login.setAttribute('type', 'text');
        login.setAttribute('autocomplete', 'username');
        //Set pattern for login
        login.setAttribute('pattern', '^('+userRegex+')|('+emailRegex+')$');
        //Enforce minimum length for password
        password.setAttribute('minlength', '8');
        //Adjust name of the button
        button.value = 'Sign in';
        //Add or remove listeners for password strength
        ['focus', 'change', 'input',].forEach(function(e) {
            password.removeEventListener(e, passwordStrengthOnEvent);
        });
        //Show or hide password field
        password.parentElement.classList.remove('hidden');
        //Show or hide remember me checkbox
        rememberme.parentElement.classList.remove('hidden');
        //Show or hide password requirements
        document.getElementById('password_req').classList.add('hidden');
        document.querySelectorAll('.pass_str_div').item(0).classList.add('hidden');
    }
    if (newUser && newUser.checked === true) {
        password.required = true;
        password.setAttribute('autocomplete', 'new-password');
        login.setAttribute('autocomplete', 'email');
        login.setAttribute('pattern', '^'+emailRegex+'$');
        password.setAttribute('minlength', '8');
        button.value = 'Join';
        ['focus', 'change', 'input',].forEach(function(e) {
            password.addEventListener(e, passwordStrengthOnEvent);
        });
        password.parentElement.classList.remove('hidden');
        rememberme.parentElement.classList.remove('hidden');
        document.getElementById('password_req').classList.remove('hidden');
        document.querySelectorAll('.pass_str_div').item(0).classList.remove('hidden');
    }
    if (forget && forget.checked === true) {
        password.required = false;
        password.removeAttribute('autocomplete');
        login.setAttribute('type', 'text');
        login.setAttribute('autocomplete', 'username');
        login.setAttribute('pattern', '^('+userRegex+')|('+emailRegex+')$');
        password.removeAttribute('minlength');
        button.value = 'Remind';
        ['focus', 'change', 'input',].forEach(function(e) {
            password.removeEventListener(e, passwordStrengthOnEvent);
        });
        password.parentElement.classList.add('hidden');
        rememberme.parentElement.classList.add('hidden');
        document.getElementById('password_req').classList.add('hidden');
        document.querySelectorAll('.pass_str_div').item(0).classList.add('hidden');
        //Additionally uncheck rememberme as precaution
        rememberme.checked = false;
    }
    //Adjust Aria values
    if (password) {
        ariaNation(password);
    }
}
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
    snack.setAttribute('role', 'alert');
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
/*exported tooltipInit*/

function tooltipInit()
{
    tabForTips();
    //Handle tooltip positioning for mouse hover
    document.onmousemove = function (e) {
        tooltip(e.target);
        let x = e.clientX,
            y = e.clientY;
        document.documentElement.style.setProperty('--cursorX', x + 'px');
        document.documentElement.style.setProperty('--cursorY', y + 'px');
    };
    //Handle tooltip positioning for focus
    document.querySelectorAll('[data-tooltip]').forEach(item => {
        item.addEventListener('focus', function(e) {
            tooltip(e.target);
            let coordinates = e.target.getBoundingClientRect();
            let block = document.getElementById('tooltip');
            let x = coordinates.x,
                y = coordinates.y - block.offsetHeight * 1.5;
            document.documentElement.style.setProperty('--cursorX', x + 'px');
            document.documentElement.style.setProperty('--cursorY', y + 'px');
        });
    });
    //Remove tooltip if an element without data-tooltip is selected. Needed to prevent focused tooltips from persisting
    document.querySelectorAll(':not([data-tooltip])').forEach(item => {
        item.addEventListener('focus', function() {
            let block = document.getElementById('tooltip');
            block.removeAttribute('data-tooltip');
        });
    });
}

//Add tabindex to elements with data-tooltip attribute, if missing
function tabForTips()
{
    document.querySelectorAll('[data-tooltip]:not([tabindex])').forEach(item => {
        item.setAttribute('tabindex', '0');
    });
}

function tooltip(element)
{
    let parent = element.parentElement;
    let block = document.getElementById('tooltip');
    if (element.hasAttribute('data-tooltip') || parent.hasAttribute('data-tooltip')) {
        block.setAttribute('data-tooltip', element.getAttribute('data-tooltip') ?? parent.getAttribute('data-tooltip'));// jshint ignore:line
    } else {
        block.removeAttribute('data-tooltip');
    }
}
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
