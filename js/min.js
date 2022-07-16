'use strict';
const pageTitle = ' on Simbiat Software';
document.addEventListener('DOMContentLoaded', init);
window.addEventListener('hashchange', function () { hashCheck(); });
function init() {
    new Input();
    new Textarea();
    new Form();
    ucInit();
    bicInit();
    new Details();
    new Quotes();
    fftrackerInit();
    new Aside();
    new Nav();
    new Headings();
    customElements.define('back-to-top', BackToTop);
    customElements.define('time-r', Timer);
    customElements.define('web-share', WebShare);
    customElements.define('tool-tip', Tooltip);
    customElements.define('snack-close', SnackbarClose);
    customElements.define('gallery-overlay', Gallery);
    customElements.define('image-carousel', CarouselList);
    new A();
    cleanGET();
    hashCheck();
}
class BackToTop extends HTMLElement {
    static content;
    static BTTs;
    constructor() {
        super();
        if (!BackToTop.content) {
            BackToTop.content = document.getElementById('content');
            BackToTop.BTTs = Array.from(document.getElementsByTagName('back-to-top'));
            BackToTop.content.addEventListener('scroll', this.toggleButtons.bind(this));
        }
        this.addEventListener('click', () => { BackToTop.content.scrollTop = 0; });
    }
    toggleButtons() {
        if (BackToTop.BTTs) {
            if (BackToTop.content.scrollTop === 0) {
                BackToTop.BTTs.forEach((item) => {
                    item.classList.add('hidden');
                });
            }
            else {
                BackToTop.BTTs.forEach((item) => {
                    item.classList.remove('hidden');
                });
            }
        }
    }
}
class Gallery extends HTMLElement {
    _current = 0;
    images = [];
    get current() {
        return this._current;
    }
    set current(value) {
        if (value < 0) {
            this._current = this.images.length - 1;
        }
        else if (value > this.images.length - 1) {
            this._current = 0;
        }
        else {
            this._current = value;
        }
        if (this.images.length > 1 || this.classList.contains('hidden')) {
            this.open();
        }
    }
    constructor() {
        super();
        this.images = Array.from(document.querySelectorAll('.galleryZoom'));
        if (this.images.length > 0) {
            customElements.define('gallery-close', GalleryClose);
            customElements.define('gallery-prev', GalleryPrev);
            customElements.define('gallery-next', GalleryNext);
            customElements.define('gallery-image', GalleryImage);
            this.images.forEach((item, index) => {
                item.addEventListener('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    this.current = index;
                    return false;
                });
            });
            this.addEventListener('keydown', this.keyNav.bind(this));
        }
    }
    open() {
        this.tabIndex = 99;
        let link = this.images[this.current];
        let image = link.getElementsByTagName('img')[0];
        let caption = link.parentElement.getElementsByTagName('figcaption')[0];
        let name = link.getAttribute('data-tooltip') ?? link.getAttribute('title') ?? image.getAttribute('alt') ?? link.href.replace(/^.*[\\\/]/u, '');
        document.getElementById('galleryName').innerHTML = caption ? caption.innerHTML : name;
        document.getElementById('galleryNameLink').href = document.getElementById('galleryLoadedImage').src = link.href;
        document.getElementById('galleryTotal').innerText = this.images.length.toString();
        document.getElementById('galleryCurrent').innerText = (this.current + 1).toString();
        this.classList.remove('hidden');
        this.history();
        this.focus();
    }
    close() {
        this.tabIndex = -1;
        this.classList.add('hidden');
        this.history();
        document.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])')[0].focus();
    }
    previous() {
        this.current--;
    }
    next() {
        this.current++;
    }
    keyNav(event) {
        event.stopPropagation();
        if (['ArrowDown', 'ArrowRight', 'PageDown'].includes(event.code)) {
            this.next();
            return false;
        }
        else if (['ArrowUp', 'ArrowLeft', 'PageUp'].includes(event.code)) {
            this.previous();
            return false;
        }
        else if (event.code === 'End') {
            this.current = this.images.length - 1;
            return false;
        }
        else if (event.code === 'Home') {
            this.current = 0;
            return false;
        }
        else if (['Escape', 'Backspace'].includes(event.code)) {
            this.close();
            return false;
        }
        else {
            return true;
        }
    }
    history() {
        const url = new URL(document.location.href);
        const newIndex = (this.current + 1).toString();
        const regexTitle = new RegExp('(.+' + pageTitle + ')(, Image \\d+)?', 'ui');
        let newUrl;
        let newTitle;
        if (this.classList.contains('hidden')) {
            newTitle = document.title.replace(/(.*)(, Image )(\d+)/ui, '$1');
            newUrl = document.location.href.replace(url.hash, '');
        }
        else {
            newTitle = document.title.replace(regexTitle, '$1, Image ' + newIndex);
            newUrl = document.location.href.replace(/([^#]+)((#gallery=\d+)|$)/ui, '$1#gallery=' + newIndex);
        }
        if (document.location.href !== newUrl) {
            updateHistory(newUrl, newTitle);
        }
    }
}
class GalleryImage extends HTMLElement {
    image;
    constructor() {
        super();
        this.image = document.getElementById('galleryLoadedImage');
        this.image.addEventListener('load', this.checkZoom.bind(this));
    }
    checkZoom() {
        if (this.image.naturalHeight <= this.image.height) {
            this.image.classList.add('noZoom');
            this.image.removeEventListener('click', this.zoom.bind(this));
        }
        else {
            this.image.classList.remove('noZoom');
            this.image.addEventListener('click', this.zoom.bind(this));
        }
    }
    zoom() {
        if (this.image.classList.contains('zoomedIn')) {
            this.image.classList.remove('zoomedIn');
        }
        else {
            this.image.classList.add('zoomedIn');
        }
    }
}
class GalleryPrev extends HTMLElement {
    overlay;
    constructor() {
        super();
        this.overlay = document.getElementsByTagName('gallery-overlay')[0];
        if (this.overlay.images.length > 1) {
            this.addEventListener('click', () => {
                this.overlay.previous();
            });
        }
        else {
            this.classList.add('disabled');
        }
    }
}
class GalleryNext extends HTMLElement {
    overlay;
    constructor() {
        super();
        this.overlay = document.getElementsByTagName('gallery-overlay')[0];
        if (this.overlay.images.length > 1) {
            this.addEventListener('click', () => {
                this.overlay.next();
            });
        }
        else {
            this.classList.add('disabled');
        }
    }
}
class GalleryClose extends HTMLElement {
    constructor() {
        super();
        this.addEventListener('click', () => {
            document.getElementsByTagName('gallery-overlay')[0].close();
        });
    }
}
class CarouselList extends HTMLElement {
    list;
    next;
    previous;
    maxScroll = 0;
    constructor() {
        super();
        this.list = this.getElementsByClassName('imageCarouselList')[0];
        this.next = this.getElementsByClassName('imageCarouselNext')[0];
        this.previous = this.getElementsByClassName('imageCarouselPrev')[0];
        if (this.list && this.next && this.previous) {
            this.maxScroll = this.list.scrollWidth - this.list.offsetWidth;
            this.list.addEventListener('scroll', () => {
                this.disableScroll();
            });
            [this.next, this.previous].forEach(item => {
                item.addEventListener('click', (event) => {
                    this.toScroll(event);
                });
            });
            this.disableScroll();
        }
    }
    toScroll(event) {
        let scrollButton = event.target;
        let img = this.list.getElementsByTagName('img')[0];
        let width = img.width;
        if (scrollButton.classList.contains('imageCarouselPrev')) {
            this.list.scrollLeft -= width;
        }
        else {
            this.list.scrollLeft += width;
        }
        this.disableScroll();
    }
    disableScroll() {
        if (this.list.scrollLeft === 0) {
            this.previous.classList.add('disabled');
        }
        else {
            this.previous.classList.remove('disabled');
        }
        if (this.list.scrollLeft >= this.maxScroll) {
            this.next.classList.add('disabled');
        }
        else {
            this.next.classList.remove('disabled');
        }
    }
}
class Snackbar {
    snacks;
    static notificationIndex = 0;
    constructor(text, color = '', milliseconds = 3000) {
        this.snacks = document.getElementsByTagName('snack-bar')[0];
        if (this.snacks) {
            let snack = document.createElement('dialog');
            let id = Snackbar.notificationIndex++;
            snack.setAttribute('id', 'snackbar' + id);
            snack.setAttribute('role', 'alert');
            snack.classList.add('snackbar');
            snack.innerHTML = '<span class="snack_text">' + text + '</span><snack-close data-close-in="' + milliseconds + '"><input class="navIcon snack_close" alt="Close notification" type="image" src="/img/close.svg" aria-invalid="false" placeholder="image"></snack-close>';
            if (color) {
                snack.classList.add(color);
            }
            this.snacks.appendChild(snack);
            snack.classList.add('fadeIn');
        }
    }
}
class SnackbarClose extends HTMLElement {
    snackbar;
    snack;
    constructor() {
        super();
        this.snack = this.parentElement;
        this.snackbar = document.getElementsByTagName('snack-bar')[0];
        this.addEventListener('click', this.close);
        let closeIn = parseInt(this.getAttribute('data-close-in') ?? '0');
        if (closeIn > 0) {
            setTimeout(() => {
                this.close();
            }, closeIn);
        }
    }
    close() {
        this.snack.classList.remove('fadeIn');
        this.snack.classList.add('fadeOut');
        this.snack.addEventListener('animationend', () => { this.snackbar.removeChild(this.snack); });
    }
}
class Timer extends HTMLElement {
    interval = null;
    constructor() {
        super();
        this.interval = setInterval(() => {
            if (parseInt(this.innerHTML) > 0 || Boolean(this.getAttribute('data-negative'))) {
                if (Boolean(this.getAttribute('data-increase'))) {
                    this.innerHTML = String(parseInt(this.innerHTML) + 1);
                }
                else {
                    this.innerHTML = String(parseInt(this.innerHTML) - 1);
                }
            }
            else {
                clearInterval(Number(this.interval));
            }
        }, 1000);
    }
}
class Tooltip extends HTMLElement {
    x = 0;
    y = 0;
    constructor() {
        super();
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
            item.addEventListener('focus', () => { this.removeAttribute('data-tooltip'); });
        });
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
        this.y = coordinates.y - this.offsetHeight * 1.5;
        this.tooltipCursor();
    }
    tooltipCursor() {
        if (this.y + this.offsetHeight > window.innerHeight) {
            this.y = window.innerHeight - this.offsetHeight * 2;
        }
        if (this.x + this.offsetWidth > window.innerWidth) {
            this.x = window.innerWidth - this.offsetWidth * 1.5;
        }
        document.documentElement.style.setProperty('--cursorX', this.x + 'px');
        document.documentElement.style.setProperty('--cursorY', this.y + 'px');
    }
    update(element) {
        let parent = element.parentElement;
        if (element.hasAttribute('data-tooltip') || parent.hasAttribute('data-tooltip')) {
            this.setAttribute('data-tooltip', element.getAttribute('data-tooltip') ?? parent.getAttribute('data-tooltip') ?? '');
        }
        else {
            this.removeAttribute('data-tooltip');
        }
    }
}
class WebShare extends HTMLElement {
    constructor() {
        super();
        if (this) {
            if (navigator.share !== undefined) {
                this.classList.remove('hidden');
                this.addEventListener('click', this.share);
            }
            else {
                this.classList.add('hidden');
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
class A {
    static _instance = null;
    constructor() {
        if (A._instance) {
            return A._instance;
        }
        document.querySelectorAll('a[target="_blank"]').forEach(anchor => {
            if (!anchor.innerHTML.includes('img/newtab.svg') && !anchor.classList.contains('galleryZoom') && !anchor.classList.contains('footerLink')) {
                anchor.innerHTML += '<img class="newTabIcon" src="/img/newtab.svg" alt="Opens in new tab">';
            }
        });
        A._instance = this;
    }
}
class Aside {
    static _instance = null;
    sidebarDiv = null;
    constructor() {
        if (Aside._instance) {
            return Aside._instance;
        }
        this.sidebarDiv = document.getElementById('sidebar');
        document.getElementById('showSidebar').addEventListener('click', () => { this.sidebarDiv.classList.add('shown'); });
        document.getElementById('hideSidebar').addEventListener('click', () => { this.sidebarDiv.classList.remove('shown'); });
        Aside._instance = this;
    }
}
class Details {
    static list;
    static _instance = null;
    constructor() {
        if (Details._instance) {
            return Details._instance;
        }
        Details.list = Array.from(document.getElementsByTagName('details'));
        Details.list.forEach((item, _, list) => {
            item.ontoggle = _ => {
                if (item.open && !item.classList.contains('persistent')) {
                    list.forEach(tag => {
                        if (tag !== item && !tag.classList.contains('persistent')) {
                            tag.open = false;
                        }
                    });
                }
            };
        });
        Details.list.forEach((item) => {
            item.addEventListener('click', (event) => { this.reset(event.target); });
        });
        Details._instance = this;
    }
    reset(target) {
        Details.list.forEach((details) => {
            if (details.open && details !== target && !details.contains(target)) {
                details.open = false;
            }
        });
    }
}
class Form {
    static _instance = null;
    constructor() {
        if (Form._instance) {
            return Form._instance;
        }
        document.querySelectorAll('form').forEach((item) => {
            item.addEventListener('keypress', (event) => { this.formEnter(event); });
        });
        document.querySelectorAll('form[data-baseURL] input[type=search]').forEach((item) => {
            item.addEventListener('input', this.searchAction.bind(this));
            item.addEventListener('change', this.searchAction.bind(this));
            item.addEventListener('focus', this.searchAction.bind(this));
        });
        document.querySelectorAll('form input[type="email"], form input[type="password"], form input[type="search"], form input[type="tel"], form input[type="text"], form input[type="url"]').forEach((item) => {
            item.addEventListener('keydown', this.inputBackSpace.bind(this));
            if (item.getAttribute('maxlength')) {
                item.addEventListener('input', this.autoNext.bind(this));
                item.addEventListener('change', this.autoNext.bind(this));
                item.addEventListener('paste', this.pasteSplit.bind(this));
            }
        });
        Form._instance = this;
    }
    formEnter(event) {
        let form = event.target.form;
        if ((event.code === 'Enter' || event.code === 'NumpadEnter') && (!form.action || !(form.getAttribute('data-baseURL') && location.protocol + '//' + location.host + form.getAttribute('data-baseURL') !== form.action))) {
            event.stopPropagation();
            event.preventDefault();
            return false;
        }
    }
    searchAction(event) {
        let search = event.target;
        let form = search.form;
        if (search.value === '') {
            form.action = String(form.getAttribute('data-baseURL'));
        }
        else {
            form.action = form.getAttribute('data-baseURL') + this.rawurlencode(search.value);
        }
        form.method = 'get';
    }
    rawurlencode(str) {
        str = str + '';
        return encodeURIComponent(str)
            .replace(/!/ug, '%21')
            .replace(/'/ug, '%27')
            .replace(/\(/ug, '%28')
            .replace(/\)/ug, '%29')
            .replace(/\*/ug, '%2A');
    }
    inputBackSpace(event) {
        let current = event.target;
        if (event.code === 'Backspace' && !current.value) {
            let moveTo = this.nextInput(current, true);
            if (moveTo) {
                moveTo.focus();
                moveTo.selectionStart = moveTo.selectionEnd = moveTo.value.length;
            }
        }
    }
    autoNext(event) {
        let current = event.target;
        let maxLength = parseInt(current.getAttribute('maxlength') ?? '0');
        if (maxLength && current.value.length === maxLength && current.validity.valid) {
            let moveTo = this.nextInput(current, false);
            if (moveTo) {
                moveTo.focus();
            }
        }
    }
    nextInput(initial, reverse = false) {
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
    async pasteSplit(event) {
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
                    current = this.nextInput(current, false);
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
}
class Headings {
    static _instance = null;
    constructor() {
        if (Headings._instance) {
            return Headings._instance;
        }
        document.querySelectorAll('h1:not(#h1title), h2, h3, h4, h5, h6').forEach(hTag => {
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
            hTag.addEventListener('click', (event) => { this.copyLink(event.target); });
        });
        Headings._instance = this;
    }
    copyLink(target) {
        if (window.getSelection().type !== 'Range') {
            let link = window.location.href.replaceAll(/(^[^#]*)(#.*)?$/gmu, `$1`) + '#' + target.getAttribute('id');
            navigator.clipboard.writeText(link).then(function () {
                new Snackbar('Anchor link for "' + target.textContent + '" copied to clipboard', 'success');
            }, function () {
                new Snackbar('Failed to copy anchor link for "' + target.textContent + '"', 'failure');
            });
            return link;
        }
        else {
            return '';
        }
    }
}
class Input {
    static _instance = null;
    constructor() {
        if (Input._instance) {
            return Input._instance;
        }
        Array.from(document.getElementsByTagName('input')).forEach(item => {
            this.init(item);
        });
        Input._instance = this;
    }
    init(inputElement) {
        inputElement.addEventListener('focus', () => { this.ariaNation(inputElement); });
        inputElement.addEventListener('change', () => { this.ariaNation(inputElement); });
        inputElement.addEventListener('input', () => { this.ariaNation(inputElement); });
        this.ariaNation(inputElement);
    }
    ariaNation(inputElement) {
        inputElement.setAttribute('aria-invalid', String(!inputElement.validity.valid));
        if (!inputElement.hasAttribute('placeholder')) {
            inputElement.setAttribute('placeholder', inputElement.value || inputElement.type || 'placeholder');
        }
        if (!inputElement.getAttribute('type')) {
            inputElement.setAttribute('type', 'text');
        }
        let type = inputElement.type ?? inputElement.getAttribute('type') ?? 'text';
        if (['text', 'search', 'url', 'tel', 'email', 'password', 'date', 'month', 'week', 'time', 'datetime-local', 'number', 'checkbox', 'radio', 'file',].includes(String(type))) {
            if (inputElement.required) {
                inputElement.setAttribute('aria-required', String(true));
            }
            else {
                inputElement.setAttribute('aria-required', String(false));
            }
        }
        if (type === 'checkbox') {
            inputElement.setAttribute('role', 'checkbox');
            inputElement.setAttribute('aria-checked', String(inputElement.checked));
            if (inputElement.indeterminate) {
                inputElement.setAttribute('aria-checked', 'mixed');
            }
        }
        if (type === 'checkbox') {
            inputElement.setAttribute('value', inputElement.value);
        }
    }
}
class Nav {
    static _instance = null;
    navDiv = null;
    constructor() {
        if (Nav._instance) {
            return Nav._instance;
        }
        this.navDiv = document.getElementById('navigation');
        document.getElementById('showNav').addEventListener('click', () => { this.navDiv.classList.add('shown'); });
        document.getElementById('hideNav').addEventListener('click', () => { this.navDiv.classList.remove('shown'); });
        Nav._instance = this;
    }
}
class Quotes {
    static _instance = null;
    constructor() {
        if (Quotes._instance) {
            return Quotes._instance;
        }
        document.querySelectorAll('samp, code, blockquote').forEach(item => {
            item.innerHTML = '<img loading="lazy" decoding="async"  src="/img/copy.svg" alt="Click to copy block" class="copyQuote">' + item.innerHTML;
        });
        Array.from(document.getElementsByTagName('q')).forEach(item => {
            item.setAttribute('data-tooltip', 'Click to copy quote');
        });
        document.querySelectorAll('.copyQuote, q').forEach(item => {
            item.addEventListener('click', (event) => { this.copy(event.target); });
        });
        Quotes._instance = this;
    }
    copy(node) {
        if (node.tagName.toLowerCase() !== 'q') {
            node = node.parentElement;
        }
        let tag;
        switch (node.tagName.toLowerCase()) {
            case 'samp':
                tag = 'Sample';
                break;
            case 'code':
                tag = 'Code';
                break;
            case 'blockquote':
            case 'q':
                tag = 'Quote';
                break;
        }
        navigator.clipboard.writeText(String(node.textContent)).then(function () {
            new Snackbar(tag + ' copied to clipboard', 'success');
        }, function () {
            new Snackbar('Failed to copy ' + tag.toLowerCase(), 'failure');
        });
        return String(node.textContent);
    }
}
class Textarea {
    static _instance = null;
    constructor() {
        if (Textarea._instance) {
            return Textarea._instance;
        }
        Array.from(document.getElementsByTagName('textarea')).forEach(item => {
            if (!item.hasAttribute('placeholder')) {
                item.setAttribute('placeholder', item.value || item.type || 'placeholder');
            }
        });
        Textarea._instance = this;
    }
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
            new Snackbar('Request to "' + url + '" returned code ' + response.status, 'failure', 10000);
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
            new Snackbar('Request to "' + url + '" timed out after ' + timeout + ' milliseconds', 'failure', 10000);
        }
        else {
            new Snackbar('Request to "' + url + '" failed on fetch operation', 'failure', 10000);
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
function updateHistory(newUrl, title) {
    document.title = title;
    window.history.pushState(title, title, newUrl);
}
function cleanGET() {
    let url = new URL(document.location.href);
    let params = new URLSearchParams(url.search);
    params.delete('cacheReset');
    if (params.toString() === '') {
        window.history.replaceState(document.title, document.title, location.pathname + location.hash);
    }
    else {
        window.history.replaceState(document.title, document.title, '?' + params + location.hash);
    }
}
function hashCheck() {
    let url = new URL(document.location.href);
    let hash = url.hash;
    let Gallery = document.getElementsByTagName('gallery-overlay')[0];
    const galleryLink = new RegExp('#gallery=\\d+', 'ui');
    if (galleryLink.test(hash)) {
        let imageID = Number(hash.replace(/(#gallery=)(\d+)/ui, '$2'));
        if (imageID) {
            if (Gallery.images[imageID - 1]) {
                Gallery.current = imageID - 1;
            }
            else {
                new Snackbar('Image number ' + imageID + ' not found on page', 'failure');
                window.history.replaceState(document.title, document.title, document.location.href.replace(hash, ''));
            }
        }
    }
    else {
        Gallery.close();
    }
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
                    new Snackbar('Библиотека БИК обновлена', 'success');
                    refresh.classList.remove('spin');
                }
                else if (typeof data.data === 'number') {
                    let timestamp = new Date(data.data * 1000);
                    let dateTime = document.getElementsByClassName('bic_date')[0];
                    dateTime.setAttribute('datetime', timestamp.toISOString());
                    dateTime.innerHTML = ('0' + String(timestamp.getUTCDate())).slice(-2) + '.' + ('0' + String(timestamp.getMonth() + 1)).slice(-2) + '.' + String(timestamp.getUTCFullYear());
                    new Snackbar('Применено обновление за ' + dateTime.innerHTML, 'success');
                    refresh.classList.remove('spin');
                    bicRefresh(event);
                }
                else {
                    new Snackbar('Не удалось обновить библиотеку БИК', 'failure', 10000);
                    refresh.classList.remove('spin');
                }
            });
        }, 500);
    }
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
                new Snackbar(selectText + ' with ID ' + idInput.value + ' was registered. Check <a href="' + location.protocol + '//' + location.host + '/fftracker/' + select.value + '/' + idInput.value + '/' + '" target="_blank">here</a>.', 'success', 0);
            }
            else if (data === '404') {
                new Snackbar(selectText + ' with ID ' + idInput.value + ' was not found on Lodestone.', 'failure', 10000);
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
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
        new Snackbar('Please, enter a valid email address', 'failure');
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
            let input = cell.getElementsByTagName('input')[0];
            new Input().init(input);
            blockDeleteMail();
            form.reset();
            new Snackbar('Mail added', 'success');
        }
        else {
            new Snackbar(data.reason, 'failure', 10000);
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
            new Snackbar('Mail removed', 'success');
        }
        else {
            new Snackbar(data.reason, 'failure', 10000);
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
                new Snackbar('Email unsubscribed', 'success');
            }
            else {
                checkbox.checked = true;
                label.innerText = 'Unsubscribe';
                new Snackbar('Email subscribed', 'success');
            }
        }
        else {
            new Snackbar(data.reason, 'failure', 10000);
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
            new Snackbar('Activation email sent', 'success');
        }
        else {
            new Snackbar(data.reason, 'failure', 10000);
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
                new Snackbar('If respective account is registered an email has been sent with password reset link.', 'success');
            }
            else {
                location.reload();
            }
        }
        else {
            new Snackbar(data.reason, 'failure', 10000);
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
            new Snackbar('Password changed', 'success');
        }
        else {
            new Snackbar(data.reason, 'failure', 10000);
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
        new Input().ariaNation(password);
    }
}
//# sourceMappingURL=min.js.map