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
    if (document.title !== title) {
        document.title = title;
    }
    if (document.location.href !== newUrl) {
        window.history.pushState(title, title, newUrl);
    }
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
function deleteRow(element) {
    let table = element.closest('table');
    let tr = element.closest('tr').rowIndex;
    if (table && tr) {
        table.deleteRow(tr);
        return true;
    }
    else {
        return false;
    }
}
function basename(text) {
    return text.replace(/^.*\/|\.[^.]*$/g, '');
}
function buttonToggle(button, enable = true) {
    let spinner;
    if (button.form) {
        spinner = button.form.querySelector('.spinner');
    }
    if (!spinner) {
        spinner = button.parentElement.querySelector('.spinner');
    }
    if (button.disabled) {
        if (enable) {
            button.disabled = false;
        }
        if (spinner) {
            spinner.classList.add('hidden');
        }
    }
    else {
        button.disabled = true;
        if (spinner) {
            spinner.classList.remove('hidden');
        }
    }
}
const pageTitle = ' on Simbiat Software';
const emailRegex = '[\\p{L}\\d.!#$%&\'*+\\/=?^_`{|}~-]+@[a-zA-Z\\d](?:[a-zA-Z\\d\\-]{0,61}[a-zA-Z\\d])?(?:\\.[a-zA-Z\\d](?:[a-zA-Z\\d\\-]{0,61}[a-zA-Z\\d])?)*';
const userRegex = '^[\\p{L}\\d.!#$%&\'*+\\\\/=?^_`{|}~\\- ]{1,64}$';
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
    customElements.define('like-dis', Likedis);
    customElements.define('vertical-tabs', VerticalTabs);
    customElements.define('image-upload', ImageUpload);
    new A();
    cleanGET();
    hashCheck();
    router();
    let tinyList = document.querySelectorAll('textarea.tinymce');
    if (tinyList.length > 0) {
        import('/js/tinymce/tinymce.min.js').then(() => {
            tinymce.init(tinySettings);
        });
    }
}
const customColorMap = {
    '#F5F0F0': 'text',
    '#9AD4EA': 'interactive',
    '#8AE59C': 'success',
    '#F3A0B6': 'failure',
    '#E6B63D': 'warning',
    '#808080': 'disabled',
    '#19424D': 'dark-border',
    '#266373': 'light-border',
    '#2E293D': 'article',
    '#231F2E': 'block',
    '#17141F': 'body',
};
const tinySettings = {
    selector: 'textarea.tinymce',
    relative_urls: false,
    remove_script_host: true,
    base_url: '/js/tinymce/',
    document_base_url: window.location.protocol + '//' + window.location.hostname + '/',
    referrer_policy: 'no-referer',
    content_security_policy: "default-src 'self'",
    skin: 'oxide-dark',
    content_css: '/css/tinymce.css',
    hidden_input: false,
    readonly: false,
    block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6;',
    branding: true,
    plugins: 'autolink autosave charmap code emoticons fullscreen help image insertdatetime link lists media preview quickbars searchreplace table visualblocks visualchars wordcount',
    contextmenu: 'emoticons link image',
    table_toolbar: 'tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol | tabledelete',
    quickbars_insert_toolbar: false,
    font_formats: '',
    fontsize_formats: '',
    lineheight_formats: '',
    menu: {
        file: { title: 'File', items: 'newdocument restoredraft' },
        edit: { title: 'Edit', items: 'undo redo | cut copy paste pastetext | selectall | searchreplace' },
        view: { title: 'View', items: 'code preview | visualaid visualchars visualblocks | fullscreen' },
        format: { title: 'Format', items: 'underline strikethrough superscript subscript | align' },
        insert: { title: 'Insert', items: 'link image media codeformat | emoticons charmap hr | insertdatetime' },
        table: { title: 'Table', items: 'inserttable | cell row column | deletetable' },
        help: { title: 'Help', items: 'help wordcount' }
    },
    valid_styles: {},
    menubar: 'file edit view format insert table help',
    toolbar: 'undo redo | blocks | bold italic | forecolor backcolor | blockquote bullist numlist | removeformat',
    theme_advanced_default_foreground_color: "#F5F0F0",
    style_formats: [],
    toolbar_mode: 'wrap',
    custom_colors: false,
    color_map: Object.keys(customColorMap).map(function (key) { return [key, customColorMap[key]]; }).flat(),
    formats: {
        forecolor: {
            inline: 'span',
            attributes: {
                class: (value) => 'tiny-color-' + customColorMap[value.value],
            },
            remove: 'none',
        },
        hilitecolor: {
            inline: 'span',
            remove: 'none',
            attributes: {
                class: (value) => 'tiny-bg-color-' + customColorMap[value.value],
            },
        },
        underline: {
            inline: 'span',
            classes: 'tiny-underline',
            remove: 'none',
        },
        alignleft: {
            selector: 'strong,em,sub,sup,s,a,time,p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,audio,video',
            classes: 'tiny-align-left',
            remove: 'none',
        },
        alignright: {
            selector: 'strong,em,sub,sup,s,a,time,p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,audio,video',
            classes: 'tiny-align-right',
            remove: 'none',
        },
        aligncenter: {
            selector: 'strong,em,sub,sup,s,a,time,p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,audio,video',
            classes: 'tiny-align-center',
            remove: 'none',
        },
        alignjustify: {
            selector: 'strong,em,sub,sup,s,a,time,p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,audio,video',
            classes: 'tiny-align-justify',
            remove: 'none',
        },
        valigntop: {
            selector: 'td,th,table',
            classes: 'tiny-valign-top',
            remove: 'none',
        },
        valignmiddle: {
            selector: 'td,th,table',
            classes: 'tiny-valign-middle',
            remove: 'none',
        },
        valignbottom: {
            selector: 'td,th,table',
            classes: 'tiny-valign-bottom',
            remove: 'none',
        }
    },
    visual: true,
    entity_encoding: 'numeric',
    invalid_styles: 'font-size line-height',
    schema: 'html5-strict',
    browser_spellcheck: true,
    resize_img_proportional: true,
    link_default_protocol: 'https',
    autosave_restore_when_empty: true,
    emoticons_database: 'emojis',
    image_caption: true,
    image_advtab: false,
    image_title: true,
    image_description: true,
    image_uploadtab: true,
    images_file_types: 'jpeg,jpg,png,gif,bmp,webp',
    images_upload_credentials: true,
    images_reuse_filename: true,
    images_upload_url: '/api/upload/',
    paste_data_images: false,
    paste_remove_styles_if_webkit: true,
    paste_webkit_styles: 'none',
    image_class_list: [
        { title: 'None', value: 'w25pc middle block' },
        { title: 'Fullwidth', value: 'w100pc middle block' },
        { title: 'Icon', value: 'linkIcon' }
    ],
    automatic_uploads: true,
    remove_trailing_brs: true,
    file_picker_types: 'image',
    block_unsupported_drop: true,
    image_dimensions: false,
    insertdatetime_element: true,
    link_target_list: [
        { title: 'New window', value: '_blank' },
        { title: 'Current window', value: '_self' }
    ],
    default_link_target: '_blank',
    link_assume_external_targets: 'https',
    link_context_toolbar: true,
    paste_block_drop: false,
    visualblocks_default_state: false,
    lists_indent_on_tab: true,
    promotion: false,
    table_appearance_options: false,
    table_border_widths: [
        { title: 'default', value: '0.125rem' },
    ],
    table_border_styles: [
        { title: 'Solid', value: 'solid' },
    ],
    table_advtab: false,
    table_cell_advtab: false,
    table_row_advtab: false,
    table_style_by_css: false,
    object_resizing: false,
    link_title: false,
};
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
    let Gallery = document.querySelector('gallery-overlay');
    const galleryLink = new RegExp('#gallery=\\d+', 'ui');
    if (Gallery) {
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
}
function router() {
    let url = new URL(document.location.href);
    let path = url.pathname.replace(/(\/)(.*)(\/)?/ui, '$2').toLowerCase().split('/');
    if (path[0]) {
        if (path[0] === 'bictracker') {
            if (path[1]) {
                if (path[1] === 'keying') {
                    import('/js/Pages/bictracker/keying.js').then((module) => { new module.bicKeying(); });
                }
                else if (path[1] === 'search') {
                    import('/js/Pages/bictracker/search.js').then((module) => { new module.bicRefresh(); });
                }
            }
        }
        else if (path[0] === 'fftracker') {
            if (path[1]) {
                if (path[1] === 'track') {
                    import('/js/Pages/fftracker/track.js').then((module) => { new module.ffTrack(); });
                }
                else if (['characters', 'freecompanies', 'linkshells', 'crossworldlinkshells', 'crossworld_linkshells', 'pvpteams',].includes(path[1])) {
                    import('/js/Pages/fftracker/entity.js').then((module) => { new module.ffEntity(); });
                }
            }
        }
        else if (path[0] === 'uc') {
            if (path[1]) {
                if (path[1] === 'emails') {
                    import('/js/Pages/uc/emails.js').then((module) => { new module.Emails(); });
                }
                else if (path[1] === 'password') {
                    import('/js/Pages/uc/password.js').then((module) => { new module.PasswordChange(); });
                }
                else if (path[1] === 'profile') {
                    import('/js/Pages/uc/profile.js').then((module) => { new module.EditProfile(); });
                }
                else if (path[1] === 'avatars') {
                    import('/js/Pages/uc/avatars.js').then((module) => { new module.EditAvatars(); });
                }
                else if (path[1] === 'sessions') {
                    import('/js/Pages/uc/sessions.js').then((module) => { new module.EditSessions(); });
                }
                else if (path[1] === 'fftracker') {
                    import('/js/Pages/uc/fftracker.js').then((module) => { new module.EditFFLinks(); });
                }
                else if (path[1] === 'removal') {
                    import('/js/Pages/uc/removal.js').then((module) => { new module.RemoveProfile(); });
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
        let image = link.querySelector('img');
        image.classList.remove('zoomedIn');
        let caption = link.parentElement.querySelector('figcaption');
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
        if (url !== new URL(newUrl)) {
            updateHistory(newUrl, newTitle);
        }
    }
}
class GalleryImage extends HTMLElement {
    image;
    zoomListener;
    constructor() {
        super();
        this.image = document.getElementById('galleryLoadedImage');
        this.zoomListener = this.zoom.bind(this);
        this.image.addEventListener('load', this.checkZoom.bind(this));
    }
    checkZoom() {
        this.image.classList.remove('zoomedIn');
        if (this.image.naturalHeight <= this.image.height) {
            this.image.removeEventListener('click', this.zoomListener);
            this.image.classList.add('noZoom');
        }
        else {
            this.image.classList.remove('noZoom');
            this.image.addEventListener('click', this.zoomListener);
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
        this.overlay = document.querySelector('gallery-overlay');
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
        this.overlay = document.querySelector('gallery-overlay');
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
            document.querySelector('gallery-overlay').close();
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
        this.list = this.querySelector('.imageCarouselList');
        this.next = this.querySelector('.imageCarouselNext');
        this.previous = this.querySelector('.imageCarouselPrev');
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
        let img = this.list.querySelector('img');
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
class Likedis extends HTMLElement {
    postId = 0;
    likeValue = 0;
    likesCount;
    dislikesCount;
    likeButton;
    dislikeButton;
    constructor() {
        super();
        this.likeValue = Number(this.getAttribute('data-liked') ?? 0);
        this.postId = Number(this.getAttribute('data-postid') ?? 0);
        this.likesCount = this.querySelector('.likes_count');
        this.dislikesCount = this.querySelector('.dislikes_count');
        this.likeButton = this.querySelector('.like_button');
        this.dislikeButton = this.querySelector('.dislike_button');
        this.likeButton.addEventListener('click', this.like.bind(this));
        this.dislikeButton.addEventListener('click', this.like.bind(this));
    }
    like(event) {
        let button = event.target;
        let action;
        if (button.classList.contains('like_button')) {
            action = 'like';
        }
        else {
            action = 'dislike';
        }
        if (this.postId === 0) {
            new Snackbar('No post ID', 'failure', 10000);
            return;
        }
        buttonToggle(button);
        ajax(location.protocol + '//' + location.host + '/api/talks/posts/' + this.postId + '/' + action + '/', null, 'json', 'PUT', 60000, true).then(data => {
            if (data.data === 0) {
                this.updateCounts(data.data);
            }
            else if (data.data === 1) {
                this.updateCounts(data.data);
            }
            else if (data.data === -1) {
                this.updateCounts(data.data);
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(button);
        });
    }
    updateCounts(newValue) {
        this.likesCount.classList.remove('success');
        this.dislikesCount.classList.remove('failure');
        if (newValue === 0) {
            if (this.likeValue === 1) {
                this.likesCount.innerHTML = String(Number(this.likesCount.innerHTML) - 1);
            }
            else if (this.likeValue === -1) {
                this.dislikesCount.innerHTML = String(Number(this.dislikesCount.innerHTML) - 1);
            }
            this.likeButton.setAttribute('data-tooltip', 'Like');
            this.dislikeButton.setAttribute('data-tooltip', 'Dislike');
        }
        else if (newValue === 1) {
            if (this.likeValue === -1) {
                this.dislikesCount.innerHTML = String(Number(this.dislikesCount.innerHTML) - 1);
            }
            this.likesCount.innerHTML = String(Number(this.likesCount.innerHTML) + 1);
            this.likesCount.classList.add('success');
            this.likeButton.setAttribute('data-tooltip', 'Remove like');
            this.dislikeButton.setAttribute('data-tooltip', 'Dislike');
        }
        else if (newValue === -1) {
            if (this.likeValue === 1) {
                this.likesCount.innerHTML = String(Number(this.likesCount.innerHTML) - 1);
            }
            this.dislikesCount.innerHTML = String(Number(this.dislikesCount.innerHTML) + 1);
            this.dislikesCount.classList.add('failure');
            this.likeButton.setAttribute('data-tooltip', 'Like');
            this.dislikeButton.setAttribute('data-tooltip', 'Remove dislike');
        }
        if (Number(this.likesCount.innerHTML) < 0) {
            this.likesCount.innerHTML = '0';
        }
        if (Number(this.dislikesCount.innerHTML) < 0) {
            this.dislikesCount.innerHTML = '0';
        }
        this.setAttribute('data-liked', String(newValue));
        this.likeValue = newValue;
    }
}
class PasswordShow extends HTMLElement {
    passwordInput;
    constructor() {
        super();
        this.passwordInput = this.parentElement.querySelector('input');
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
        this.passwordInput = this.parentElement.querySelector('input');
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
        this.passwordInput = this.parentElement.querySelector('input');
        this.strengthSpan = this.querySelector('span');
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
        let strength;
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
class Snackbar {
    snacks;
    static notificationIndex = 0;
    constructor(text, color = '', milliseconds = 3000) {
        this.snacks = document.querySelector('snack-bar');
        if (this.snacks) {
            let template = document.querySelector('#snackbar_template').content.cloneNode(true);
            let id = Snackbar.notificationIndex++;
            let snack = template.querySelector('dialog');
            snack.setAttribute('id', 'snackbar' + id);
            snack.querySelector('.snack_text').innerHTML = text;
            snack.querySelector('snack-close').setAttribute('data-close-in', String(milliseconds));
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
        this.snackbar = document.querySelector('snack-bar');
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
        if (tooltip && element !== this && matchMedia('(pointer:fine)').matches) {
            this.setAttribute('data-tooltip', 'true');
            this.innerHTML = tooltip;
        }
        else {
            this.removeAttribute('data-tooltip');
            this.innerHTML = '';
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
        let showSidebar = document.getElementById('showSidebar');
        let hideSidebar = document.getElementById('hideSidebar');
        if (showSidebar) {
            showSidebar.addEventListener('click', () => {
                this.sidebarDiv.classList.add('shown');
            });
        }
        if (hideSidebar) {
            hideSidebar.addEventListener('click', () => {
                this.sidebarDiv.classList.remove('shown');
            });
        }
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
            formData.set('signinup[timezone]', Intl.DateTimeFormat().resolvedOptions().timeZone);
            let button = this.loginForm.querySelector('#signinup_submit');
            buttonToggle(button);
            ajax(location.protocol + '//' + location.host + '/api/uc/' + formData.get('signinup[type]') + '/', formData, 'json', 'POST', 60000, true).then(data => {
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
                buttonToggle(button);
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
        Details.list = Array.from(document.querySelectorAll('details:not(.persistent):not(.spoiler):not(.adult)'));
        Details.list.forEach((item, _, list) => {
            item.ontoggle = _ => {
                if (item.open) {
                    list.forEach(tag => {
                        if (tag !== item) {
                            tag.open = false;
                        }
                    });
                }
            };
        });
        Details.list.forEach((item) => {
            item.addEventListener('click', (event) => {
                this.reset(event.target);
            });
        });
        Details._instance = this;
    }
    reset(target) {
        Details.list.forEach((details) => {
            if (details.open && details !== target && !details.contains(target)) {
                details.open = false;
            }
            else {
                if (details.classList.contains('popup')) {
                    document.addEventListener('click', (event) => {
                        this.clickOutsideDetails(event, details);
                    });
                }
            }
        });
    }
    clickOutsideDetails(event, details) {
        if (details !== event.target && !details.contains(event.target)) {
            details.open = false;
            document.removeEventListener('click', (event) => {
                this.clickOutsideDetails(event, details);
            });
        }
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
        if ((event.code === 'Enter' || event.code === 'NumpadEnter') && !form.action) {
            event.stopPropagation();
            event.preventDefault();
            return false;
        }
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
            for (let moveTo of form.querySelectorAll('input[type="email"], input[type="password"], input[type="search"], input[type="tel"], input[type="text"], input[type="url"]')) {
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
        let showNav = document.getElementById('showNav');
        let hideNav = document.getElementById('hideNav');
        if (showNav) {
            showNav.addEventListener('click', () => {
                this.navDiv.classList.add('shown');
            });
        }
        if (hideNav) {
            hideNav.addEventListener('click', () => {
                this.navDiv.classList.remove('shown');
            });
        }
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
        document.querySelectorAll('blockquote[data-author]').forEach(item => {
            if (!/^\s*$/ui.test(item.getAttribute('data-author') ?? '')) {
                item.innerHTML = '<span class="quoteAuthor">' + item.getAttribute('data-author') + '</span>' + item.innerHTML;
            }
        });
        document.querySelectorAll('samp[data-description], code[data-description]').forEach(item => {
            if (!/^\s*$/ui.test(item.getAttribute('data-description') ?? '')) {
                item.innerHTML = '<span class="codeDesc">' + item.getAttribute('data-description') + '</span>' + item.innerHTML;
            }
        });
        document.querySelectorAll('blockquote[data-source], samp[data-source], code[data-source]').forEach(item => {
            if (!/^\s*$/ui.test(item.getAttribute('data-source') ?? '')) {
                item.innerHTML = item.innerHTML + '<span class="quoteSource">' + item.getAttribute('data-source') + '</span>';
            }
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
        let tagName = node.tagName.toLowerCase();
        let quoteText = String(node.textContent);
        if (tagName === 'blockquote' && node.hasAttribute('data-author')) {
            let authorMatch = new RegExp('^(' + node.getAttribute('data-author') + ':)', 'ui');
            quoteText = quoteText.replace(authorMatch, '');
        }
        if ((tagName === 'samp' || tagName === 'code') && node.hasAttribute('data-description')) {
            let descMatch = new RegExp('^(' + node.getAttribute('data-description') + ':)', 'ui');
            quoteText = quoteText.replace(descMatch, '');
        }
        if ((tagName === 'blockquote' || tagName === 'samp' || tagName === 'code') && node.hasAttribute('data-source')) {
            let sourceMatch = new RegExp('(' + node.getAttribute('data-source') + ')$', 'ui');
            quoteText = quoteText.replace(sourceMatch, '');
        }
        navigator.clipboard.writeText(quoteText).then(function () {
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
class VerticalTabs extends HTMLElement {
    tabs;
    contents;
    constructor() {
        super();
        this.tabs = Array.from(this.querySelectorAll('tab-name'));
        this.contents = Array.from(this.querySelectorAll('tab-content'));
        this.tabs.forEach((item) => {
            item.addEventListener('click', (event) => {
                this.tabSwitch(event.target);
            });
        });
    }
    tabSwitch(target) {
        let tabIndex = 0;
        this.tabs.forEach((item, index) => {
            if (item === target) {
                tabIndex = index;
            }
            item.classList.remove('active');
            if (this.contents[index]) {
                this.contents[index].classList.remove('active');
            }
        });
        target.classList.add('active');
        if (this.contents[tabIndex]) {
            this.contents[tabIndex].classList.add('active');
        }
    }
}
class ImageUpload extends HTMLElement {
    preview = null;
    file = null;
    label = null;
    constructor() {
        super();
        this.file = this.querySelector('input[type=file]');
        this.label = this.querySelector('label');
        this.preview = this.querySelector('img');
        this.file.accept = 'image/avif,image/bmp,image/gif,image/jpeg,image/png,image/webp,image/svg+xml';
        this.file.placeholder = 'Image file';
        this.preview.alt = 'Preview of ' + this.label.innerText.charAt(0).toLowerCase() + this.label.innerText.slice(1);
        this.preview.setAttribute('data-tooltip', this.preview.alt);
        if (this.file) {
            this.file.addEventListener('change', () => {
                this.update();
            });
        }
    }
    update() {
        if (this.preview && this.file) {
            if (this.file.files && this.file.files[0]) {
                this.preview.src = URL.createObjectURL(this.file.files[0]);
                this.preview.classList.remove('hidden');
            }
            else {
                this.preview.classList.add('hidden');
            }
        }
    }
}
//# sourceMappingURL=main.js.map