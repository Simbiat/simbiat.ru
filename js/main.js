"use strict";
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
function submitIntercept(form, callable) {
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        event.stopPropagation();
        callable();
        return false;
    });
    form.addEventListener('keydown', function (event) {
        if (event.code === 'Enter') {
            event.preventDefault();
            event.stopPropagation();
            callable();
            return false;
        }
        else {
            return true;
        }
    });
}
const pageTitle = ' on Simbiat Software';
const emailRegex = '[a-zA-Z0-9.!#$%&\'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*';
const userRegex = '[^\\/\\\\\\[\\]:;|=$%#@&\\(\\)\\{\\}!,+*?<>\\0\\t\\r\\n\\x00-\\x1F\\x7F\\x0b\\f\\x85\\v\\cY\\b]{1,64}';
document.addEventListener('DOMContentLoaded', init);
window.addEventListener('hashchange', function () { hashCheck(); });
function init() {
    new Input();
    new Textarea();
    new Form();
    new Details();
    new Quotes();
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
    customElements.define('password-show', PasswordShow);
    customElements.define('password-requirements', PasswordRequirements);
    customElements.define('password-strength', PasswordStrength);
    new A();
    cleanGET();
    hashCheck();
    router();
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
function router() {
    let url = new URL(document.location.href);
    let path = url.pathname.replace(/(\/)(.*)(\/)/ui, '$2').toLowerCase().split('/');
    if (path[0]) {
        if (path[0] === 'bictracker') {
            if (path[1]) {
                if (path[1] === 'keying') {
                    new bicKeying();
                }
                else if (path[1] === 'search') {
                    new bicRefresh();
                }
            }
        }
        else if (path[0] === 'fftracker') {
            if (path[1] && path[1] === 'track') {
                new ffTrack();
            }
        }
        else if (path[0] === 'uc') {
            if (path[1]) {
                if (path[1] === 'emails') {
                    new Emails();
                }
                else if (path[1] === 'password') {
                    new PasswordChange();
                }
            }
        }
    }
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
            snack.querySelectorAll('a[target="_blank"]').forEach(anchor => {
                new A().newTabStyle(anchor);
            });
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
        this.snack.addEventListener('animationend', () => {
            if (this.snack) {
                this.snackbar.removeChild(this.snack);
            }
        });
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
                if (this.id === 'refresh_timer') {
                    location.reload();
                }
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
        document.querySelectorAll('[data-tooltip]:not([data-tooltip=""])').forEach(item => {
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
        let tooltip = element.getAttribute('data-tooltip') ?? parent.getAttribute('data-tooltip') ?? null;
        if (tooltip) {
            this.setAttribute('data-tooltip', tooltip);
        }
        else {
            this.removeAttribute('data-tooltip');
        }
    }
}
class WebShare extends HTMLElement {
    constructor() {
        super();
        if (navigator.share !== undefined) {
            this.classList.remove('hidden');
            this.addEventListener('click', this.share);
        }
        else {
            this.classList.add('hidden');
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
            this.newTabStyle(anchor);
        });
        A._instance = this;
    }
    newTabStyle(anchor) {
        if (!anchor.innerHTML.includes('img/newtab.svg') && !anchor.classList.contains('galleryZoom') && !anchor.classList.contains('footerLink')) {
            anchor.innerHTML += '<img class="newTabIcon" src="/img/newtab.svg" alt="Opens in new tab">';
        }
    }
}
class Aside {
    static _instance = null;
    sidebarDiv = null;
    loginForm = null;
    constructor() {
        if (Aside._instance) {
            return Aside._instance;
        }
        this.sidebarDiv = document.getElementById('sidebar');
        document.getElementById('showSidebar').addEventListener('click', () => { this.sidebarDiv.classList.add('shown'); });
        document.getElementById('hideSidebar').addEventListener('click', () => { this.sidebarDiv.classList.remove('shown'); });
        this.loginForm = document.getElementById('signinup');
        if (this.loginForm) {
            this.loginForm.querySelectorAll('#radio_signinup input[type=radio]').forEach(item => {
                item.addEventListener('change', this.loginRadioCheck);
            });
            this.loginRadioCheck();
            submitIntercept(this.loginForm, this.singInUpSubmit.bind(this));
        }
        Aside._instance = this;
    }
    singInUpSubmit() {
        if (this.loginForm) {
            let formData = new FormData(this.loginForm);
            if (!formData.get('signinup[type]')) {
                formData.set('signinup[type]', 'logout');
            }
            let spinner = document.getElementById('signinup_spinner');
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
    }
    loginRadioCheck() {
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
            password.parentElement.classList.remove('hidden');
            rememberme.parentElement.classList.remove('hidden');
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
            password.parentElement.classList.remove('hidden');
            rememberme.parentElement.classList.remove('hidden');
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
            password.parentElement.classList.add('hidden');
            rememberme.parentElement.classList.add('hidden');
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
class bicKeying {
    constructor() {
        document.getElementById('bic_key').addEventListener('input', () => { this.calc(); });
        document.getElementById('account_key').addEventListener('input', () => { this.calc(); });
    }
    calc() {
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
            this.styleBic(bicKeySample, 'warning', 'БИК');
            return;
        }
        else {
            this.styleBic(bicKeySample, 'success', bicKey);
        }
        if (/^\d{5}[\dАВСЕНКМРТХавсенкмртх]\d{14}$/u.exec(accKey) === null) {
            result.classList.add('failure');
            result.innerHTML = 'Неверный формат счёта';
            this.styleBic(accKeySample, 'warning', 'СЧЁТ');
            return;
        }
        else {
            this.styleBic(accKeySample, 'success', accKey);
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
    styleBic(element, newClass, text = '') {
        element.classList.remove(...element.classList);
        element.classList.add(newClass);
        element.innerHTML = text;
    }
}
class bicRefresh {
    refreshButton;
    constructor() {
        this.refreshButton = document.getElementById('bicRefresh');
        this.refreshButton.addEventListener('click', (event) => { this.refresh(event); });
    }
    refresh(event) {
        if (this.refreshButton.classList.contains('spin')) {
            event.stopPropagation();
            event.preventDefault();
        }
        else {
            this.refreshButton.classList.add('spin');
            setTimeout(async () => {
                await ajax(location.protocol + '//' + location.host + '/api/bictracker/dbupdate/', null, 'json', 'PUT', 300000).then(data => {
                    if (data.data === true) {
                        new Snackbar('Библиотека БИК обновлена', 'success');
                        this.refreshButton.classList.remove('spin');
                    }
                    else if (typeof data.data === 'number') {
                        let timestamp = new Date(data.data * 1000);
                        let dateTime = document.getElementsByClassName('bic_date')[0];
                        dateTime.setAttribute('datetime', timestamp.toISOString());
                        dateTime.innerHTML = ('0' + String(timestamp.getUTCDate())).slice(-2) + '.' + ('0' + String(timestamp.getMonth() + 1)).slice(-2) + '.' + String(timestamp.getUTCFullYear());
                        new Snackbar('Применено обновление за ' + dateTime.innerHTML, 'success');
                        this.refreshButton.classList.remove('spin');
                        this.refresh(event);
                    }
                    else {
                        new Snackbar('Не удалось обновить библиотеку БИК', 'failure', 10000);
                        this.refreshButton.classList.remove('spin');
                    }
                });
            }, 500);
        }
    }
}
class ffTrack {
    select;
    idInput;
    constructor() {
        this.idInput = document.getElementById('ff_track_id');
        this.select = document.getElementById('ff_track_type');
        this.select.addEventListener('change', () => {
            this.typeChange();
        });
        submitIntercept(document.getElementById('ff_track_register'), this.add.bind(this));
    }
    add() {
        let selectedOption = this.select.selectedOptions[0];
        let selectText;
        if (selectedOption) {
            selectText = selectedOption.text;
        }
        else {
            selectText = 'Character';
        }
        if (this.idInput && this.select) {
            let spinner = document.getElementById('ff_track_spinner');
            spinner.classList.remove('hidden');
            ajax(location.protocol + '//' + location.host + '/api/fftracker/' + this.select.value + '/' + this.idInput.value + '/', null, 'json', 'POST', 60000, true).then(data => {
                if (data.data === true) {
                    new Snackbar(selectText + ' with ID ' + this.idInput.value + ' was registered. Check <a href="' + location.protocol + '//' + location.host + '/fftracker/' + this.select.value + '/' + this.idInput.value + '/' + '" target="_blank">here</a>.', 'success', 0);
                }
                else if (data === '404') {
                    new Snackbar(selectText + ' with ID ' + this.idInput.value + ' was not found on Lodestone.', 'failure', 10000);
                }
                else {
                    if (data.reason.match(/^ID `.*` is already registered$/ui)) {
                        new Snackbar(data.reason + '. Check <a href="' + location.protocol + '//' + location.host + '/fftracker/' + this.select.value + '/' + this.idInput.value + '/' + '" target="_blank">here</a>.', 'warning', 0);
                    }
                    else {
                        new Snackbar(data.reason, 'failure', 10000);
                    }
                }
                spinner.classList.add('hidden');
            });
        }
    }
    typeChange() {
        let pattern = '^\\d{1,20}$';
        if (this.select.value === 'pvpteam' || this.select.value === 'crossworld_linkshell') {
            pattern = '^[0-9a-z]{40}$';
        }
        this.idInput.setAttribute('pattern', pattern);
    }
}
class Emails {
    addMailForm = null;
    constructor() {
        this.addMailForm = document.getElementById('addMailForm');
        if (this.addMailForm) {
            submitIntercept(this.addMailForm, this.add.bind(this));
            document.querySelectorAll('.mail_activation').forEach(item => {
                item.addEventListener('click', (event) => {
                    this.activate(event.target);
                });
            });
            document.querySelectorAll('[id^=subscription_checkbox_]').forEach(item => {
                item.addEventListener('click', (event) => {
                    this.subscribe(event);
                });
            });
            document.querySelectorAll('.mail_deletion').forEach(item => {
                item.addEventListener('click', (event) => {
                    this.delete(event.target);
                });
            });
        }
    }
    add() {
        let formData = new FormData(this.addMailForm);
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
                this.blockDelete();
                this.addMailForm.reset();
                new Snackbar(email + ' added', 'success');
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            spinner.classList.add('hidden');
        });
    }
    delete(button) {
        let table = button.parentElement.parentElement.parentElement;
        let tr = button.parentElement.parentElement.rowIndex - 1;
        let spinner = button.parentElement.getElementsByClassName('spinner')[0];
        let formData = new FormData();
        let email = button.getAttribute('data-email') ?? '';
        formData.set('email', email);
        spinner.classList.remove('hidden');
        ajax(location.protocol + '//' + location.host + '/api/uc/emails/delete/', formData, 'json', 'DELETE', 60000, true).then(data => {
            if (data.data === true) {
                table.deleteRow(tr);
                this.blockDelete();
                new Snackbar(email + ' removed', 'success');
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            spinner.classList.add('hidden');
        });
    }
    blockDelete() {
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
    subscribe(event) {
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
        let email = checkbox.getAttribute('data-email') ?? '';
        let formData = new FormData();
        formData.set('verb', verb);
        formData.set('email', email);
        spinner.classList.remove('hidden');
        ajax(location.protocol + '//' + location.host + '/api/uc/emails/' + verb + '/', formData, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                if (checkbox.checked) {
                    checkbox.checked = false;
                    label.innerText = 'Subscribe';
                    new Snackbar(email + ' unsubscribed', 'success');
                }
                else {
                    checkbox.checked = true;
                    label.innerText = 'Unsubscribe';
                    new Snackbar(email + ' subscribed', 'success');
                }
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            spinner.classList.add('hidden');
        });
    }
    activate(button) {
        let spinner = button.parentElement.getElementsByClassName('spinner')[0];
        let email = button.getAttribute('data-email') ?? '';
        let formData = new FormData();
        formData.set('verb', 'activate');
        formData.set('email', email);
        spinner.classList.remove('hidden');
        ajax(location.protocol + '//' + location.host + '/api/uc/emails/activate/', formData, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                new Snackbar('Activation email sent to ' + email, 'success');
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            spinner.classList.add('hidden');
        });
    }
}
class PasswordChange {
    form = null;
    constructor() {
        this.form = document.getElementById('password_change');
        if (this.form) {
            submitIntercept(this.form, this.change.bind(this));
        }
    }
    change() {
        let formData = new FormData(this.form);
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
}
class PasswordShow extends HTMLElement {
    passwordInput;
    constructor() {
        super();
        this.passwordInput = this.parentElement.getElementsByTagName('input').item(0);
        this.addEventListener('click', this.toggle);
    }
    toggle(event) {
        event.preventDefault();
        if (this.passwordInput.type === 'password') {
            this.passwordInput.type = 'text';
            this.title = 'Hide password';
        }
        else {
            this.passwordInput.type = 'password';
            this.title = 'Show password';
        }
    }
}
class PasswordRequirements extends HTMLElement {
    passwordInput;
    constructor() {
        super();
        this.classList.add('hidden');
        this.innerHTML = 'Only password requirement: at least 8 symbols';
        this.passwordInput = this.parentElement.getElementsByTagName('input').item(0);
        this.passwordInput.addEventListener('focus', this.show.bind(this));
        this.passwordInput.addEventListener('focusout', this.hide.bind(this));
        ['focus', 'change', 'input',].forEach((eventType) => {
            this.passwordInput.addEventListener(eventType, this.validate.bind(this));
        });
    }
    validate() {
        if (this.passwordInput.validity.valid) {
            this.classList.remove('error');
            this.classList.add('success');
        }
        else {
            this.classList.add('error');
            this.classList.remove('success');
        }
    }
    show() {
        let autocomplete = this.passwordInput.getAttribute('autocomplete') ?? null;
        if (autocomplete === 'new-password') {
            this.classList.remove('hidden');
        }
        else {
            this.classList.add('hidden');
        }
    }
    hide() {
        this.classList.add('hidden');
    }
}
class PasswordStrength extends HTMLElement {
    passwordInput;
    strengthSpan;
    constructor() {
        super();
        this.classList.add('hidden');
        this.innerHTML = 'New password strength: <span class="password_strength">weak</span>';
        this.passwordInput = this.parentElement.getElementsByTagName('input').item(0);
        this.strengthSpan = this.getElementsByTagName('span')[0];
        this.passwordInput.addEventListener('focus', this.show.bind(this));
        this.passwordInput.addEventListener('focusout', this.hide.bind(this));
        ['focus', 'change', 'input',].forEach((eventType) => {
            this.passwordInput.addEventListener(eventType, this.calculate.bind(this));
        });
    }
    calculate() {
        let password = this.passwordInput.value;
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
        let strength = 'weak';
        if (points <= 2) {
            strength = 'weak';
        }
        else if (2 < points && points < 5) {
            strength = 'medium';
        }
        else if (points === 5) {
            strength = 'strong';
        }
        else {
            strength = 'very strong';
        }
        this.strengthSpan.innerHTML = strength;
        this.strengthSpan.classList.remove('password_weak', 'password_medium', 'password_strong', 'password_very_strong');
        if (strength === 'very strong') {
            this.strengthSpan.classList.add('password_very_strong');
        }
        else {
            this.strengthSpan.classList.add('password_' + strength);
        }
        return strength;
    }
    show() {
        let autocomplete = this.passwordInput.getAttribute('autocomplete') ?? null;
        if (autocomplete === 'new-password') {
            this.classList.remove('hidden');
        }
        else {
            this.classList.add('hidden');
        }
    }
    hide() {
        this.classList.add('hidden');
    }
}
//# sourceMappingURL=main.js.map