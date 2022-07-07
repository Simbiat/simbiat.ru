'use strict';
const pageTitle = ' on Simbiat Software';
document.addEventListener('DOMContentLoaded', init);
window.addEventListener('hashchange', function () { hashCheck(false); });
function init() {
    let content = document.getElementById('content');
    content.addEventListener('scroll', backToTop);
    Array.from(document.getElementsByTagName('input')).forEach(item => {
        ariaInit(item);
        if (!item.hasAttribute('placeholder')) {
            item.setAttribute('placeholder', item.value || item.type || 'placeholder');
        }
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
    document.querySelectorAll('#showSidebar, #hideSidebar').forEach(item => {
        item.addEventListener('click', toggleSidebar);
    });
    document.querySelectorAll('#showNav, #hideNav').forEach(item => {
        item.addEventListener('click', toggleNav);
    });
    document.querySelectorAll('h1:not(#h1title), h2, h3, h4, h5, h6').forEach(item => {
        idToHeader(item);
        item.addEventListener('click', anchorFromHeader);
    });
    let refreshTimer = document.getElementById('refresh_timer');
    if (refreshTimer) {
        timer(refreshTimer, false);
    }
    new Tooltip();
    cleanGET();
    hashCheck(true);
}
function cleanGET() {
    let url = new URL(document.location.href);
    let params = new URLSearchParams(url.search);
    params.delete('cacheReset');
    if (params.toString() === '') {
        window.history.replaceState(null, document.title, location.pathname + location.hash);
    }
    else {
        window.history.replaceState(null, document.title, '?' + params + location.hash);
    }
}
function hashCheck(hashUpdate) {
    let url = new URL(document.location.href);
    let hash = url.hash;
    const galleryLink = new RegExp('#gallery=\\d+', 'ui');
    if (galleryLink.test(hash)) {
        let imageID = Number(hash.replace(/(#gallery=)(\d+)/ui, '$2'));
        if (imageID) {
            if (Gallery.images[imageID - 1]) {
                new Gallery().open(Gallery.images[imageID - 1], hashUpdate);
            }
            else {
                new Snackbar().add('Image number ' + imageID + ' not found on page', 'failure');
                window.history.replaceState(null, document.title, document.location.href.replace(hash, ''));
            }
        }
    }
}
class Gallery {
    current = 1;
    static images = [];
    constructor() {
        document.querySelectorAll('.galleryZoom').forEach(item => {
            item.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                this.open(event.target, true);
                return false;
            });
        });
        document.getElementById('galleryClose').addEventListener('click', this.close.bind(this));
        document.getElementById('galleryPrevious').addEventListener('click', this.previous.bind(this));
        document.getElementById('galleryNext').addEventListener('click', this.next.bind(this));
        document.querySelectorAll('.imageCarouselPrev, .imageCarouselNext').forEach(item => {
            item.addEventListener('click', (event) => { this.scroll(event); });
        });
        this.count();
        document.querySelectorAll('.imageCarousel').forEach(item => {
            this.disable(item);
        });
    }
    scroll(event) {
        let scrollButton = event.target;
        let ul = scrollButton.parentElement.getElementsByTagName('ul')[0];
        let img = ul.getElementsByTagName('img')[0];
        let width = img.width;
        if (scrollButton.classList.contains('imageCarouselPrev')) {
            ul.scrollLeft -= width;
        }
        else {
            ul.scrollLeft += width;
        }
        this.disable(scrollButton.parentElement);
    }
    disable(carousel) {
        let prev = carousel.getElementsByClassName('imageCarouselPrev')[0];
        let next = carousel.getElementsByClassName('imageCarouselNext')[0];
        let ul = carousel.getElementsByTagName('ul')[0];
        let max = ul.scrollWidth - ul.offsetWidth;
        if (ul.scrollLeft === 0) {
            prev.classList.add('disabled');
        }
        else {
            prev.classList.remove('disabled');
        }
        if (ul.scrollLeft >= max) {
            next.classList.add('disabled');
        }
        else {
            next.classList.remove('disabled');
        }
    }
    open(image, hashUpdate) {
        let link;
        if (image.tagName.toLowerCase() === 'a') {
            link = image;
        }
        else {
            link = image.closest('a');
        }
        this.current = this.getIndex(link);
        this.loadImage(hashUpdate);
        document.getElementById('galleryOverlay').classList.remove('hidden');
    }
    loadImage(hashUpdate) {
        let link = Gallery.images[this.current - 1];
        let image = link.getElementsByTagName('img')[0];
        let caption = link.parentElement.getElementsByTagName('figcaption')[0];
        let name = link.getAttribute('data-tooltip') ?? link.getAttribute('title') ?? image.getAttribute('alt') ?? link.href.replace(/^.*[\\\/]/u, '');
        document.getElementById('galleryName').innerHTML = caption ? caption.innerHTML : name;
        document.getElementById('galleryNameLink').innerHTML = '<a href="' + link.href + '" target="_blank"><img loading="lazy" decoding="async" class="linkIcon" alt="Open in new tab" src="/img/newtab.svg"></a>';
        document.getElementById('galleryTotal').innerText = Gallery.images.length.toString();
        document.getElementById('galleryCurrent').innerText = this.current.toString();
        document.getElementById('galleryImage').innerHTML = '<img id="galleryLoadedImage" loading="lazy" decoding="async" alt="' + name + '" src="' + link.href + '">';
        document.getElementById('galleryLoadedImage').addEventListener('load', this.checkZoom.bind(this));
        if (hashUpdate) {
            let url = new URL(document.location.href);
            let hash = url.hash;
            if (hash) {
                window.history.pushState('Image ' + this.current.toString(), document.title, document.location.href.replace(hash, '#gallery=' + this.current.toString()));
            }
            else {
                window.history.pushState('Image ' + this.current.toString(), document.title, document.location.href + '#gallery=' + this.current.toString());
            }
        }
    }
    close() {
        document.getElementById('galleryOverlay').classList.add('hidden');
    }
    count() {
        Gallery.images = [];
        Gallery.images = Array.from(document.querySelectorAll('.galleryZoom'));
    }
    getIndex(link) {
        return Gallery.images.indexOf(link) + 1;
    }
    previous() {
        this.current = this.current - 1;
        if (this.current < 1) {
            this.current = Gallery.images.length;
        }
        this.loadImage(true);
    }
    next() {
        this.current = this.current + 1;
        if (this.current > Gallery.images.length) {
            this.current = 1;
        }
        this.loadImage(true);
    }
    checkZoom() {
        let image = document.getElementById('galleryLoadedImage');
        if (image.naturalHeight <= image.height) {
            image.classList.add('noZoom');
            image.removeEventListener('click', this.zoom.bind(this));
        }
        else {
            image.classList.remove('noZoom');
            image.addEventListener('click', this.zoom.bind(this));
        }
    }
    zoom() {
        let image = document.getElementById('galleryLoadedImage');
        if (image.classList.contains('zoomedIn')) {
            image.classList.remove('zoomedIn');
        }
        else {
            image.classList.add('zoomedIn');
        }
    }
}
class Snackbar {
    snacks;
    notificationIndex = 0;
    constructor() {
        this.snacks = document.getElementById('snacksContainer');
    }
    add(text, color = '', milliseconds = 3000) {
        let snack = document.createElement('dialog');
        let id = this.notificationIndex++;
        snack.setAttribute('id', 'snackbar' + id);
        snack.setAttribute('role', 'alert');
        snack.classList.add('snackbar');
        snack.innerHTML = '<span class="snack_text">' + text + '</span><input id="closeSnack' + id + '" class="navIcon snack_close" alt="Close notification" type="image" src="/img/close.svg" aria-invalid="false" placeholder="image">';
        if (color) {
            snack.classList.add(color);
        }
        this.snacks.appendChild(snack);
        snack.classList.add('fadeIn');
        snack.addEventListener('click', () => { this.delete(snack); });
        if (milliseconds > 0) {
            setTimeout(() => {
                this.delete(snack);
            }, milliseconds);
        }
    }
    delete(snack) {
        snack.classList.remove('fadeIn');
        snack.classList.add('fadeOut');
        snack.addEventListener('animationend', () => { this.snacks.removeChild(snack); });
    }
}
class Tooltip {
    tooltip;
    x = 0;
    y = 0;
    constructor() {
        this.tooltip = document.getElementById('tooltip');
        if (this.tooltip) {
            document.querySelectorAll('[alt]:not([alt=""]):not([data-tooltip]), [title]:not([title=""]):not([data-tooltip])').forEach(item => {
                if (!item.parentElement.hasAttribute('data-tooltip')) {
                    item.setAttribute('data-tooltip', item.getAttribute('alt') ?? item.getAttribute('title') ?? '');
                }
            });
            document.querySelectorAll('[data-tooltip]:not([tabindex])').forEach(item => {
                item.setAttribute('tabindex', '0');
            });
            document.addEventListener('mousemove', this.onMouseMove.bind(this));
            document.querySelectorAll('[data-tooltip]:not([Data-Attribute=""])').forEach(item => {
                item.addEventListener('focus', this.onFocus.bind(this));
            });
            document.querySelectorAll(':not([data-tooltip])').forEach(item => {
                item.addEventListener('focus', this.remove.bind(this));
            });
        }
    }
    onMouseMove(event) {
        this.update(event.target);
        this.x = event.clientX;
        this.y = event.clientY;
        this.tooltipCursor();
    }
    onFocus(event) {
        this.update(event.target);
        let coordinates = event.target.getBoundingClientRect();
        this.x = coordinates.x;
        this.y = coordinates.y - this.tooltip.offsetHeight * 1.5;
        this.tooltipCursor();
    }
    remove() {
        this.tooltip.removeAttribute('data-tooltip');
    }
    tooltipCursor() {
        if (this.y + this.tooltip.offsetHeight > window.innerHeight) {
            this.y = window.innerHeight - this.tooltip.offsetHeight * 2;
        }
        if (this.x + this.tooltip.offsetWidth > window.innerWidth) {
            this.x = window.innerWidth - this.tooltip.offsetWidth * 1.5;
        }
        document.documentElement.style.setProperty('--cursorX', this.x + 'px');
        document.documentElement.style.setProperty('--cursorY', this.y + 'px');
    }
    update(element) {
        let parent = element.parentElement;
        if (element.hasAttribute('data-tooltip') || parent.hasAttribute('data-tooltip')) {
            this.tooltip.setAttribute('data-tooltip', element.getAttribute('data-tooltip') ?? parent.getAttribute('data-tooltip') ?? '');
        }
        else {
            this.tooltip.removeAttribute('data-tooltip');
        }
    }
}
function getMeta(metaName) {
    const metas = Array.from(document.getElementsByTagName('meta'));
    let tag = metas.find(obj => {
        return obj.name === metaName;
    });
    if (tag) {
        return tag.getAttribute('content');
    }
    else {
        return null;
    }
}
function timer(target, increase = true) {
    setInterval(function () {
        if (parseInt(target.innerHTML) > 0) {
            if (increase) {
                target.innerHTML = String(parseInt(target.innerHTML) + 1);
            }
            else {
                target.innerHTML = String(parseInt(target.innerHTML) - 1);
            }
        }
    }, 1000);
}
function colorValue(target) {
    target.setAttribute('value', target.value);
}
function colorValueOnEvent(event) {
    colorValue(event.target);
}
function toggleSidebar(event) {
    event.preventDefault();
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        if (sidebar.classList.contains('shown')) {
            sidebar.classList.remove('shown');
        }
        else {
            sidebar.classList.add('shown');
        }
    }
}
function toggleNav(event) {
    event.preventDefault();
    const sidebar = document.getElementById('navigation');
    if (sidebar) {
        if (sidebar.classList.contains('shown')) {
            sidebar.classList.remove('shown');
        }
        else {
            sidebar.classList.add('shown');
        }
    }
}
function updateHistory(newUrl, title) {
    document.title = title;
    window.history.pushState(title, title, newUrl);
}
async function ajax(url, formData = null, type = 'json', method = 'GET', timeout = 60000, skipError = false) {
    let result;
    let controller = new AbortController();
    setTimeout(() => controller.abort(), timeout);
    try {
        let response = await fetch(url, {
            method: method,
            mode: 'same-origin',
            cache: 'no-cache',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-Token': getMeta('X-CSRF-Token') ?? '',
            },
            redirect: 'error',
            referrer: window.location.href,
            referrerPolicy: 'same-origin',
            keepalive: false,
            signal: controller.signal,
            body: ['POST', 'PUT', 'DELETE', 'PATCH',].includes(method) ? formData : null,
        });
        if (!response.ok && !skipError) {
            new Snackbar().add('Request to "' + url + '" returned code ' + response.status, 'failure', 10000);
            return false;
        }
        else {
            if (type === 'json') {
                result = await response.json();
            }
            else if (type === 'blob') {
                result = await response.blob();
            }
            else if (type === 'array') {
                result = await response.arrayBuffer();
            }
            else if (type === 'form') {
                result = await response.formData();
            }
            else {
                result = await response.text();
            }
        }
        return result;
    }
    catch (err) {
        if (err.name === 'AbortError') {
            new Snackbar().add('Request to "' + url + '" timed out after ' + timeout + ' milliseconds', 'failure', 10000);
        }
        else {
            new Snackbar().add('Request to "' + url + '" failed on fetch operation', 'failure', 10000);
        }
    }
}
function ariaInit(item) {
    item.addEventListener('focus', ariaNationOnEvent);
    item.addEventListener('change', ariaNationOnEvent);
    item.addEventListener('input', ariaNationOnEvent);
    ariaNation(item);
}
function ariaNation(inputElement) {
    inputElement.setAttribute('aria-invalid', String(!inputElement.validity.valid));
    if (inputElement.hasAttribute('type') && ['text', 'search', 'url', 'tel', 'email', 'password', 'date', 'month', 'week', 'time', 'datetime-local', 'number', 'checkbox', 'radio', 'file',].includes(String(inputElement.getAttribute('type')))) {
        if (inputElement.required) {
            inputElement.setAttribute('aria-required', String(true));
        }
        else {
            inputElement.setAttribute('aria-required', String(false));
        }
    }
    if (inputElement.hasAttribute('type') && inputElement.getAttribute('type') === 'checkbox') {
        inputElement.setAttribute('role', 'checkbox');
        inputElement.setAttribute('aria-checked', String(inputElement.checked));
        if (inputElement.indeterminate) {
            inputElement.setAttribute('aria-checked', 'mixed');
        }
    }
}
function ariaNationOnEvent(event) {
    ariaNation(event.target);
}
function backToTop(event) {
    if (event.target.scrollTop === 0) {
        document.querySelectorAll('.back-to-top').forEach(item => {
            item.classList.add('hidden');
            item.removeEventListener('click', scrollToTop);
        });
    }
    else {
        document.querySelectorAll('.back-to-top').forEach(item => {
            item.classList.remove('hidden');
            item.addEventListener('click', scrollToTop);
        });
    }
}
function scrollToTop() {
    document.getElementById('content').scrollTop = 0;
}
function bicInit() {
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
function bicCalc() {
    let form = document.getElementById('bic_keying');
    let formData = new FormData(form);
    let result = document.getElementById('accCheckResult');
    let bicKey = String(formData.get('bic_key'));
    let accKey = String(formData.get('account_key'));
    let bicKeySample = document.getElementById('bic_key_sample');
    let accKeySample = document.getElementById('account_key_sample');
    result.classList.remove(...result.classList);
    if (/^\d{9}$/u.exec(bicKey) === null) {
        result.classList.add('failure');
        result.innerHTML = 'Неверный формат БИКа';
        bicStyle(bicKeySample, 'warning', 'БИК');
        return;
    }
    else {
        bicStyle(bicKeySample, 'success', bicKey);
    }
    if (/^\d{5}[\dАВСЕНКМРТХавсенкмртх]\d{14}$/u.exec(accKey) === null) {
        result.classList.add('failure');
        result.innerHTML = 'Неверный формат счёта';
        bicStyle(accKeySample, 'warning', 'СЧЁТ');
        return;
    }
    else {
        bicStyle(accKeySample, 'success', accKey);
    }
    updateHistory(location.protocol + '//' + location.host + '/bictracker/keying/' + bicKey + '/' + accKey + '/', 'Ключевание счёта ' + accKey + pageTitle);
    result.classList.add('warning');
    result.innerHTML = 'Проверяем...';
    let spinner = document.getElementById('bic_spinner');
    spinner.classList.remove('hidden');
    ajax(location.protocol + '//' + location.host + '/api/bictracker/keying/', formData, 'json', 'POST', 60000, true).then(data => {
        result.classList.remove(...result.classList);
        if (data.data === true) {
            result.classList.add('success');
            result.innerHTML = 'Правильное ключевание';
        }
        else {
            result.classList.add('failure');
            if (data.data === false) {
                result.innerHTML = 'Непредвиденная ошибка';
            }
            else {
                result.innerHTML = 'Неверное ключевание. Ожидаемый ключ: ' + data.data + ' (' + accKey.replace(/(^\d{5}[\dАВСЕНКМРТХавсенкмртх]\d{2})(\d)(\d{11})$/u, '$1<span class="success">' + data.data + '</span>$3') + ')';
            }
        }
        spinner.classList.add('hidden');
    });
    return;
}
function bicStyle(element, newClass, text = '') {
    element.classList.remove(...element.classList);
    element.classList.add(newClass);
    element.innerHTML = text;
}
function bicRefresh(event) {
    let refresh = event.target;
    if (refresh.classList.contains('spin')) {
        event.stopPropagation();
        event.preventDefault();
    }
    else {
        refresh.classList.add('spin');
        setTimeout(async function () {
            await ajax(location.protocol + '//' + location.host + '/api/bictracker/dbupdate/', null, 'json', 'PUT', 300000).then(data => {
                if (data.data === true) {
                    new Snackbar().add('Библиотека БИК обновлена', 'success');
                    refresh.classList.remove('spin');
                }
                else if (typeof data.data === 'number') {
                    let timestamp = new Date(data.data * 1000);
                    let dateTime = document.getElementsByClassName('bic_date')[0];
                    dateTime.setAttribute('datetime', timestamp.toISOString());
                    dateTime.innerHTML = ('0' + String(timestamp.getUTCDate())).slice(-2) + '.' + ('0' + String(timestamp.getMonth() + 1)).slice(-2) + '.' + String(timestamp.getUTCFullYear());
                    new Snackbar().add('Применено обновление за ' + dateTime.innerHTML, 'success');
                    refresh.classList.remove('spin');
                    bicRefresh(event);
                }
                else {
                    new Snackbar().add('Не удалось обновить библиотеку БИК', 'failure', 10000);
                    refresh.classList.remove('spin');
                }
            });
        }, 500);
    }
}
function copyQuoteInit() {
    document.querySelectorAll('samp, code, blockquote').forEach(item => {
        item.innerHTML = '<img loading="lazy" decoding="async"  src="/img/copy.svg" alt="Copy block" class="copyQuote">' + item.innerHTML;
    });
    document.querySelectorAll('.copyQuote, q').forEach(item => {
        item.addEventListener('click', copyQuote);
    });
}
function copyQuote(event) {
    let node = event.target;
    if (node.tagName.toLowerCase() !== 'q') {
        node = node.parentElement;
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
    navigator.clipboard.writeText(String(node.textContent)).then(function () {
        new Snackbar().add(tag.charAt(0).toUpperCase() + tag.slice(1) + ' copied to clipboard', 'success');
    }, function () {
        new Snackbar().add('Failed to copy ' + tag, 'failure');
    });
}
function placeholders() {
    Array.from(document.getElementsByTagName('textarea')).forEach(item => {
        if (!item.hasAttribute('placeholder')) {
            item.setAttribute('placeholder', item.value || item.type || 'placeholder');
        }
    });
}
function detailsInit() {
    document.querySelectorAll('details').forEach((details, _, list) => {
        details.ontoggle = _ => {
            if (details.open && !details.classList.contains('persistent')) {
                list.forEach(tag => {
                    if (tag !== details && !tag.classList.contains('persistent')) {
                        tag.open = false;
                    }
                });
            }
        };
    });
    window.addEventListener('click', function (event) {
        document.querySelectorAll('details').forEach((details) => {
            if (details.classList.contains('popup') && !details.contains(event.target)) {
                details.open = false;
            }
        });
    });
}
function fftrackerInit() {
    let select = document.getElementById('ff_track_type');
    if (select) {
        select.addEventListener('change', function (event) {
            ffTrackTypeChange(event.target);
        });
    }
    submitIntercept('ff_track_register');
}
function ffTrackAdd() {
    let idInput = document.getElementById('ff_track_id');
    let select = document.getElementById('ff_track_type');
    let selectedOption = select.selectedOptions[0];
    let selectText;
    if (selectedOption) {
        selectText = selectedOption.text;
    }
    else {
        selectText = 'Character';
    }
    if (idInput && select) {
        let spinner = document.getElementById('ff_track_spinner');
        spinner.classList.remove('hidden');
        ajax(location.protocol + '//' + location.host + '/api/fftracker/' + select.value + '/' + idInput.value + '/', null, 'json', 'POST', 60000, true).then(data => {
            if (data.data === true) {
                new Snackbar().add(selectText + ' with ID ' + idInput.value + ' was registered. Check <a href="' + location.protocol + '//' + location.host + '/fftracker/' + select.value + '/' + idInput.value + '/' + '" target="_blank">here</a>.', 'success', 0);
            }
            else if (data === '404') {
                new Snackbar().add(selectText + ' with ID ' + idInput.value + ' was not found on Lodestone.', 'failure', 10000);
            }
            else {
                new Snackbar().add(data.reason, 'failure', 10000);
            }
            spinner.classList.add('hidden');
        });
    }
}
function ffTrackTypeChange(target) {
    let idInput = document.getElementById('ff_track_id');
    let pattern = '^\\d+$';
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
    idInput.setAttribute('pattern', pattern);
}
const textInputTypes = ['email', 'password', 'search', 'tel', 'text', 'url',];
const nonTextInputTypes = ['checkbox', 'color', 'date', 'datetime-local', 'file', 'month', 'number', 'radio', 'time', 'week',];
function formInit() {
    document.querySelectorAll('form').forEach((item) => {
        item.addEventListener('keypress', formEnter);
    });
    document.querySelectorAll('form[data-baseURL] input[type=search]').forEach((item) => {
        item.addEventListener('input', searchAction);
        item.addEventListener('change', searchAction);
        item.addEventListener('focus', searchAction);
    });
    document.querySelectorAll('form input').forEach((item) => {
        if (textInputTypes.includes(item.type)) {
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
const submitFunctions = {
    'signinup': 'singInUpSubmit',
    'addMailForm': 'addMail',
    'ff_track_register': 'ffTrackAdd',
    'password_change': 'passwordChange',
};
function submitIntercept(formId) {
    let form = document.getElementById(formId);
    if (form && submitFunctions[formId]) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            event.stopPropagation();
            window[submitFunctions[formId]]();
            return false;
        });
        form.onkeydown = function (event) {
            if (event.code === 'Enter') {
                event.preventDefault();
                event.stopPropagation();
                window[submitFunctions[formId]]();
                return false;
            }
            return true;
        };
    }
}
function searchAction(event) {
    let search = event.target;
    let form = search.form;
    if (search.value === '') {
        form.action = String(form.getAttribute('data-baseURL'));
    }
    else {
        form.action = form.getAttribute('data-baseURL') + rawurlencode(search.value);
    }
    form.method = 'get';
}
function formEnter(event) {
    let form = event.target.form;
    if ((event.code === 'Enter' || event.code === 'NumpadEnter') && (!form.action || !(form.getAttribute('data-baseURL') && location.protocol + '//' + location.host + form.getAttribute('data-baseURL') !== form.action))) {
        event.stopPropagation();
        event.preventDefault();
        return false;
    }
}
function inputBackSpace(event) {
    let current = event.target;
    if (event.code === 'Backspace' && !current.value) {
        let moveTo = nextInput(current, true);
        if (moveTo) {
            moveTo.focus();
            moveTo.selectionStart = moveTo.selectionEnd = moveTo.value.length;
        }
    }
}
function autoNext(event) {
    let current = event.target;
    let maxLength = parseInt(current.getAttribute('maxlength') ?? '0');
    if (maxLength && current.value.length === maxLength && current.validity.valid) {
        let moveTo = nextInput(current, false);
        if (moveTo) {
            moveTo.focus();
        }
    }
}
async function pasteSplit(event) {
    let permission = await navigator.permissions.query({ name: 'clipboard-read', }).catch(() => {
        console.error('Your browser does not support clipboard-read permission.');
    });
    if (permission && permission.state !== 'denied') {
        navigator.clipboard.readText().then(result => {
            let buffer = result.toString();
            let current = event.target;
            let maxLength = parseInt(current.getAttribute('maxlength') ?? '0');
            while (current && maxLength && buffer.length > maxLength) {
                current.value = buffer.substring(0, maxLength);
                current.dispatchEvent(new Event('input', {
                    bubbles: true,
                    cancelable: true,
                }));
                if (!current.validity.valid) {
                    return false;
                }
                buffer = buffer.substring(maxLength);
                current = nextInput(current, false);
                if (current) {
                    current.focus();
                    maxLength = parseInt(current.getAttribute('maxlength') ?? '0');
                }
            }
            if (current) {
                current.value = buffer;
                current.dispatchEvent(new Event('input', {
                    bubbles: true,
                    cancelable: true,
                }));
            }
            return true;
        });
    }
}
function nextInput(initial, reverse = false) {
    let form = initial.form;
    if (form) {
        let previous;
        for (let moveTo of form.querySelectorAll('input')) {
            if (reverse) {
                if (moveTo === initial) {
                    if (previous) {
                        return previous;
                    }
                    else {
                        return false;
                    }
                }
            }
            else {
                if (previous && previous === initial) {
                    return moveTo;
                }
            }
            previous = moveTo;
        }
    }
    return false;
}
function rawurlencode(str) {
    str = str + '';
    return encodeURIComponent(str)
        .replace(/!/ug, '%21')
        .replace(/'/ug, '%27')
        .replace(/\(/ug, '%28')
        .replace(/\)/ug, '%29')
        .replace(/\*/ug, '%2A');
}
function idToHeader(hTag) {
    if (!hTag.hasAttribute('id')) {
        let id = String(hTag.textContent).replaceAll(/\s/gmu, `_`).replaceAll(/[^\p{L}\p{N}_\-]/gmu, ``).replaceAll(/(^.{1,64})(.*$)/gmu, `$1`);
        let index = 1;
        let altId = id;
        while (document.getElementById(altId)) {
            index++;
            altId = id + '_' + index;
        }
        hTag.setAttribute('id', altId);
    }
}
function anchorFromHeader(event) {
    navigator.clipboard.writeText(window.location.href.replaceAll(/(^[^#]*)(#.*)?$/gmu, `$1`) + '#' + event.target.getAttribute('id')).then(function () {
        new Snackbar().add('Anchor link for "' + event.target.textContent + '" copied to clipboard', 'success');
    }, function () {
        new Snackbar().add('Failed to copy anchor link for "' + event.target.textContent + '"', 'failure');
    });
}
function ucInit() {
    document.querySelectorAll('.showpassword').forEach(item => {
        item.addEventListener('click', showPassToggle);
    });
    document.querySelectorAll('#radio_signinup input[type=radio]').forEach(item => {
        item.addEventListener('change', loginRadioCheck);
    });
    loginRadioCheck();
    submitIntercept('signinup');
    submitIntercept('addMailForm');
    submitIntercept('password_change');
    document.querySelectorAll('.mail_activation').forEach(item => {
        item.addEventListener('click', activationMail);
    });
    document.querySelectorAll('[id^=subscription_checkbox_]').forEach(item => {
        item.addEventListener('click', subscribeMail);
    });
    document.querySelectorAll('.mail_deletion').forEach(item => {
        item.addEventListener('click', deleteMail);
    });
    let new_password = document.getElementById('new_password');
    if (new_password) {
        ['focus', 'change', 'input',].forEach(function (e) {
            new_password.addEventListener(e, passwordStrengthOnEvent);
        });
    }
}
function addMail() {
    let form = document.getElementById('addMailForm');
    let formData = new FormData(form);
    if (!formData.get('email')) {
        new Snackbar().add('Please, enter a valid email address', 'failure');
        return false;
    }
    let email = String(formData.get('email'));
    let spinner = document.getElementById('addMail_spinner');
    spinner.classList.remove('hidden');
    ajax(location.protocol + '//' + location.host + '/api/uc/emails/add/', formData, 'json', 'POST', 60000, true).then(data => {
        if (data.data === true) {
            let row = document.getElementById('emailsList').insertRow();
            row.classList.add('middle');
            let cell = row.insertCell();
            cell.innerHTML = email;
            cell = row.insertCell();
            cell.innerHTML = '<input type="button" value="Confirm" class="mail_activation" data-email="' + email + '" aria-invalid="false" placeholder="Confirm"><img class="hidden spinner inline" src="/img/spinner.svg" alt="Activating ' + email + '..." data-tooltip="Activating ' + email + '...">';
            cell = row.insertCell();
            cell.innerHTML = 'Confirm address to change setting';
            cell.classList.add('warning');
            cell = row.insertCell();
            cell.innerHTML = '<td><input class="mail_deletion" data-email="' + email + '" type="image" src="/img/close.svg" alt="Delete ' + email + '" aria-invalid="false" placeholder="image" data-tooltip="Delete ' + email + '" tabindex="0"><img class="hidden spinner inline" src="/img/spinner.svg" alt="Removing ' + email + '..." data-tooltip="Removing ' + email + '...">';
            blockDeleteMail();
            form.reset();
            new Snackbar().add('Mail added', 'success');
        }
        else {
            new Snackbar().add(data.reason, 'failure', 10000);
        }
        spinner.classList.add('hidden');
    });
}
function deleteMail(event) {
    let button = event.target;
    let table = button.parentElement.parentElement.parentElement;
    let tr = button.parentElement.parentElement.rowIndex - 1;
    let spinner = button.parentElement.getElementsByClassName('spinner')[0];
    let formData = new FormData();
    formData.set('email', button.getAttribute('data-email') ?? '');
    spinner.classList.remove('hidden');
    ajax(location.protocol + '//' + location.host + '/api/uc/emails/delete/', formData, 'json', 'DELETE', 60000, true).then(data => {
        if (data.data === true) {
            table.deleteRow(tr);
            blockDeleteMail();
            new Snackbar().add('Mail removed', 'success');
        }
        else {
            new Snackbar().add(data.reason, 'failure', 10000);
        }
        spinner.classList.add('hidden');
    });
}
function blockDeleteMail() {
    let confirmedMail = document.getElementsByClassName('mail_confirmed').length;
    document.querySelectorAll('.mail_deletion').forEach(item => {
        if (item.parentElement.parentElement.getElementsByClassName('mail_confirmed').length > 0) {
            item.disabled = confirmedMail < 2;
        }
        else {
            item.disabled = false;
        }
    });
}
function subscribeMail(event) {
    event.preventDefault();
    event.stopPropagation();
    let checkbox = event.target;
    let verb;
    if (checkbox.checked) {
        verb = 'subscribe';
    }
    else {
        verb = 'unsubscribe';
    }
    let label = checkbox.parentElement.getElementsByTagName('label')[0];
    let spinner = checkbox.parentElement.parentElement.getElementsByClassName('spinner')[0];
    let formData = new FormData();
    formData.set('verb', verb);
    formData.set('email', checkbox.getAttribute('data-email') ?? '');
    spinner.classList.remove('hidden');
    ajax(location.protocol + '//' + location.host + '/api/uc/emails/' + verb + '/', formData, 'json', 'PATCH', 60000, true).then(data => {
        if (data.data === true) {
            if (checkbox.checked) {
                checkbox.checked = false;
                label.innerText = 'Subscribe';
                new Snackbar().add('Email unsubscribed', 'success');
            }
            else {
                checkbox.checked = true;
                label.innerText = 'Unsubscribe';
                new Snackbar().add('Email subscribed', 'success');
            }
        }
        else {
            new Snackbar().add(data.reason, 'failure', 10000);
        }
        spinner.classList.add('hidden');
    });
}
function activationMail(event) {
    let button = event.target;
    let spinner = button.parentElement.getElementsByClassName('spinner')[0];
    let formData = new FormData();
    formData.set('verb', 'activate');
    formData.set('email', button.getAttribute('data-email') ?? '');
    spinner.classList.remove('hidden');
    ajax(location.protocol + '//' + location.host + '/api/uc/emails/activate/', formData, 'json', 'PATCH', 60000, true).then(data => {
        if (data.data === true) {
            new Snackbar().add('Activation email sent', 'success');
        }
        else {
            new Snackbar().add(data.reason, 'failure', 10000);
        }
        spinner.classList.add('hidden');
    });
}
function singInUpSubmit() {
    let formData = new FormData(document.getElementById('signinup'));
    if (!formData.get('signinup[type]')) {
        formData.set('signinup[type]', 'logout');
    }
    let spinner = document.getElementById('singinup_spinner');
    spinner.classList.remove('hidden');
    ajax(location.protocol + '//' + location.host + '/api/uc/signinup/' + formData.get('signinup[type]') + '/', formData, 'json', 'POST', 60000, true).then(data => {
        if (data.data === true) {
            if (formData.get('signinup[type]') === 'remind') {
                new Snackbar().add('If respective account is registered an email has been sent with password reset link.', 'success');
            }
            else {
                location.reload();
            }
        }
        else {
            new Snackbar().add(data.reason, 'failure', 10000);
        }
        spinner.classList.add('hidden');
    });
}
function passwordChange() {
    let formData = new FormData(document.getElementById('password_change'));
    let spinner = document.getElementById('pw_change_spinner');
    spinner.classList.remove('hidden');
    ajax(location.protocol + '//' + location.host + '/api/uc/password/', formData, 'json', 'PATCH', 60000, true).then(data => {
        if (data.data === true) {
            new Snackbar().add('Password changed', 'success');
        }
        else {
            new Snackbar().add(data.reason, 'failure', 10000);
        }
        spinner.classList.add('hidden');
    });
}
const emailRegex = '[a-zA-Z0-9.!#$%&\'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*';
const userRegex = '[^\\/\\\\\\[\\]:;|=$%#@&\\(\\)\\{\\}!,+*?<>\\0\\t\\r\\n\\x00-\\x1F\\x7F\\x0b\\f\\x85\\v\\cY\\b]{1,64}';
function showPassToggle(event) {
    event.preventDefault();
    let eyeIcon = event.target;
    let passField = eyeIcon.parentElement.getElementsByTagName('input').item(0);
    if (passField.type === 'password') {
        passField.type = 'text';
        eyeIcon.title = 'Hide password';
    }
    else {
        passField.type = 'password';
        eyeIcon.title = 'Show password';
    }
}
function passwordStrengthOnEvent(event) {
    let strengthField = document.querySelectorAll('.password_strength').item(0);
    let strength = passwordStrength(event.target.value);
    strengthField.innerHTML = strength;
    strengthField.classList.remove('password_weak', 'password_medium', 'password_strong', 'password_very_strong');
    if (strength === 'very strong') {
        strengthField.classList.add('password_very_strong');
    }
    else {
        strengthField.classList.add('password_' + strength);
    }
}
function passwordStrength(password) {
    let points = 0;
    if (/.{8,}/u.test(password)) {
        points++;
    }
    if (/.{16,}/u.test(password)) {
        points++;
    }
    if (/.{32,}/u.test(password)) {
        points++;
    }
    if (/.{64,}/u.test(password)) {
        points++;
    }
    if (/\p{Ll}/u.test(password)) {
        points++;
    }
    if (/\p{Lu}/u.test(password)) {
        points++;
    }
    if (/\p{Lo}/u.test(password)) {
        points++;
    }
    if (/\p{N}/u.test(password)) {
        points++;
    }
    if (/[\p{P}\p{S}]/u.test(password)) {
        points++;
    }
    if (/(.)\1{2,}/u.test(password)) {
        points--;
    }
    if (points <= 2) {
        return 'weak';
    }
    else if (2 < points && points < 5) {
        return 'medium';
    }
    else if (points === 5) {
        return 'strong';
    }
    else {
        return 'very strong';
    }
}
function loginRadioCheck() {
    let existUser = document.getElementById('radio_existuser');
    let newUser = document.getElementById('radio_newuser');
    let forget = document.getElementById('radio_forget');
    let login = document.getElementById('signinup_email');
    let loginLabel;
    if (login && login.labels) {
        loginLabel = login.labels[0];
    }
    let password = document.getElementById('signinup_password');
    let button = document.getElementById('signinup_submit');
    let rememberme = document.getElementById('rememberme');
    let username = document.getElementById('signinup_username');
    if (existUser && existUser.checked) {
        password.required = true;
        password.setAttribute('autocomplete', 'current-password');
        login.setAttribute('type', 'email');
        login.setAttribute('autocomplete', 'email');
        login.setAttribute('pattern', '^' + emailRegex + '$');
        password.setAttribute('minlength', '8');
        button.value = 'Sign in';
        ['focus', 'change', 'input',].forEach(function (e) {
            password.removeEventListener(e, passwordStrengthOnEvent);
        });
        password.parentElement.classList.remove('hidden');
        rememberme.parentElement.classList.remove('hidden');
        document.getElementById('password_req').classList.add('hidden');
        document.querySelectorAll('.pass_str_div').item(0).classList.add('hidden');
        username.parentElement.classList.add('hidden');
        username.required = false;
    }
    if (newUser && newUser.checked) {
        password.required = true;
        password.setAttribute('autocomplete', 'new-password');
        login.setAttribute('type', 'email');
        login.setAttribute('autocomplete', 'email');
        login.setAttribute('pattern', '^' + emailRegex + '$');
        password.setAttribute('minlength', '8');
        button.value = 'Join';
        ['focus', 'change', 'input',].forEach(function (e) {
            password.addEventListener(e, passwordStrengthOnEvent);
        });
        password.parentElement.classList.remove('hidden');
        rememberme.parentElement.classList.remove('hidden');
        document.getElementById('password_req').classList.remove('hidden');
        document.querySelectorAll('.pass_str_div').item(0).classList.remove('hidden');
        login.placeholder = 'Email';
        if (loginLabel) {
            loginLabel.innerHTML = 'Email';
        }
        username.parentElement.classList.remove('hidden');
        username.required = true;
    }
    if (forget && forget.checked) {
        password.required = false;
        password.removeAttribute('autocomplete');
        login.setAttribute('type', 'text');
        login.setAttribute('autocomplete', 'username');
        login.setAttribute('pattern', '^(' + userRegex + ')|(' + emailRegex + ')$');
        password.removeAttribute('minlength');
        button.value = 'Remind';
        ['focus', 'change', 'input',].forEach(function (e) {
            password.removeEventListener(e, passwordStrengthOnEvent);
        });
        password.parentElement.classList.add('hidden');
        rememberme.parentElement.classList.add('hidden');
        document.getElementById('password_req').classList.add('hidden');
        document.querySelectorAll('.pass_str_div').item(0).classList.add('hidden');
        rememberme.checked = false;
        login.placeholder = 'Email or name';
        if (loginLabel) {
            loginLabel.innerHTML = 'Email or name';
        }
        username.parentElement.classList.add('hidden');
        username.required = false;
    }
    if (password) {
        ariaNation(password);
    }
}
class WebShare {
    shareButton;
    constructor() {
        this.shareButton = document.getElementById('shareButton');
        if (this.shareButton) {
            if (navigator.share !== undefined) {
                this.shareButton.classList.remove('hidden');
                this.shareButton.addEventListener('click', this.share);
            }
            else {
                this.shareButton.classList.add('hidden');
            }
        }
    }
    share() {
        return navigator.share({
            title: document.title,
            text: getMeta('og:description') ?? getMeta('description') ?? '',
            url: document.location.href,
        });
    }
}
//# sourceMappingURL=min.js.map