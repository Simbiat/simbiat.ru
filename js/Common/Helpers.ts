//Common function to add snackbars. Originally it was a separate class, but it does not make much sense to have it that way.
function addSnackbar(text: string, color = '', milliseconds = 3000): void
{
    const snacks = document.querySelector('snack-bar');
    const template = document.querySelector('#snackbar_template');
    if (snacks && template) {
        //Generate element
        const newSnack = (template as HTMLTemplateElement).content.cloneNode(true) as DocumentFragment;
        const snack = newSnack.querySelector('dialog');
        if (snack !== null) {
            //Add text
            const textBlock = snack.querySelector('.snack_text');
            if (textBlock !== null) {
                textBlock.innerHTML = text;
            }
            //Update milliseconds for auto-closure
            snack.querySelector('snack-close')?.setAttribute('data-close-in', String(milliseconds));
            //Add class for color
            if (color) {
                snack.classList.add(color);
            }
            //Add element to parent
            snacks.appendChild(snack);
            snack.show();
        }
    }
}

//Get meta content
function getMeta(metaName: string): string|null {
    const metas = Array.from(document.querySelectorAll('meta'));
    const tag = metas.find((obj) => {
        return obj.name === metaName;
    });
    if (tag) {
        return tag.getAttribute('content');
    }
    return null;
}

//Update document title and push to history. Required, since browsers mostly ignore title argument in pushState
function updateHistory(newUrl: string, title: string): void
{
    //Update title and/or URL only if there were changes
    if (document.title !== title) {
        document.title = title;
    }
    if (document.location.href !== newUrl) {
        window.history.pushState(title, title, newUrl);
    }
}

//Function to intercept both form submission and Enter key pressed in the form (which normally also submits it)
function submitIntercept(form: HTMLFormElement, callable: () => void): void
{
    form.addEventListener('submit', (event:SubmitEvent) => {
        event.preventDefault();
        event.stopPropagation();
        callable();
        return false;
    });
    form.addEventListener('keydown', (event: KeyboardEvent) => {
        if(event.code === 'Enter') {
            event.preventDefault();
            event.stopPropagation();
            callable();
            return false;
        }
        return true;
    });
}

//Remove table row based containing element
function deleteRow(element: HTMLElement): boolean
{
    const table = element.closest('table');
    //Get row number
    const tr = element.closest('tr');
    if (table && tr) {
        table.deleteRow(tr.rowIndex);
        return true;
    }
    return false;
}

//Simulation of basename() function to return only the name of the file (without path or extension)
function basename(text: string): string
{
    return text.replace(/^.*\/|\.[^.]*$/gu, '');
}

//Function replicating PHP's rawurlencode for consistency.
function rawurlencode(str: string): string
{
    const definitelyString = String(str);
    return encodeURIComponent(definitelyString).
        replace(/!/ug, '%21').
        replace(/'/ug, '%27').
        replace(/\(/ug, '%28').
        replace(/\)/ug, '%29').
        replace(/\*/ug, '%2A');
}

//Function to replicate PHP's empty()
function empty(variable: unknown): boolean
{
    if (typeof variable === 'undefined' || variable === null || variable === false || variable === 0 || variable === 'NaN') {
        return true;
    }
    if (typeof variable === 'string') {
        return (/^[\s\p{C}]*$/ui).test(variable);
    }
    if (Array.isArray(variable)) {
        return variable.length === 0;
    }
    if (variable instanceof NodeList) {
        return variable.length === 0;
    }
    if (variable instanceof HTMLCollection) {
        return variable.length === 0;
    }
    if (typeof variable === 'object') {
        return JSON.stringify(variable) === '{}';
    }
    return false;
}

//Function to force page refresh. Regular reload() often hits cache, thus not properly updating
function pageRefresh(): void
{
    const url = new URL(document.location.href);
    url.searchParams.set('forceReload', String(Date.now()));
    window.location.replace(url.toString());
}

//Copy the text of q tag or respective block
function copyQuote(target: HTMLElement): string
{
    let node;
    //Get parent node, if click was on the copy picture/button
    if (target.tagName.toLowerCase() === 'q') {
        node = target;
    } else {
        node = target.parentElement;
    }
    if (!node) {
        return '';
    }
    const tagName = node.tagName.toLowerCase();
    let tag: string;
    switch (tagName) {
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
        default:
            //Exit, since we do not support this tpy of node
            return '';
    }
    //Set text
    let quoteText = String(node.textContent);
    //Remove author from blockquotes
    if (tagName === 'blockquote' && node.hasAttribute('data-author')) {
        const authorMatch = new RegExp(`^(${String(node.getAttribute('data-author'))})`, 'ui');
        quoteText = quoteText.replace(authorMatch,'');
    }
    //Remove description from code and samp
    if ((tagName === 'samp' || tagName === 'code') && node.hasAttribute('data-description')) {
        const descMatch = new RegExp(`^(${String(node.getAttribute('data-description'))})`, 'ui');
        quoteText = quoteText.replace(descMatch,'');
    }
    //Remove source from blockquotes, code and samp
    if ((tagName === 'blockquote' || tagName === 'samp' || tagName === 'code') && node.hasAttribute('data-source')) {
        const sourceMatch = new RegExp(`(${String(node.getAttribute('data-source'))})$`, 'ui');
        quoteText = quoteText.replace(sourceMatch,'');
    }
    navigator.clipboard.writeText(quoteText).then(() => {
        addSnackbar(`${tag} copied to clipboard`, 'success');
    }, () => {
        addSnackbar(`Failed to copy ${tag.toLowerCase()}`,'failure');
    });
    return String(node.textContent);
}
