//Custom initializers for built-in elements, as an alternative way to built-in custom elements, that are not supported in Safari
function inputInit(input: HTMLInputElement): void
{
    ['focus', 'change', 'input',].forEach((eventType: string) => {
        input.addEventListener(eventType, () => {
            ariaNation(input);
        });
    });
    //Initial application of the function
    ariaNation(input);
}

function textareaInit(textarea: HTMLTextAreaElement): void
{
    //Give elements a default placeholder, if there is none
    if (!textarea.hasAttribute('placeholder')) {
        textarea.setAttribute('placeholder', textarea.value || textarea.type || 'placeholder');
    }
    if (textarea.maxLength > 0) {
        //Attach listener
        ['change', 'keydown', 'keyup', 'input'].forEach((eventType: string) => {
            textarea.addEventListener(eventType, (event) => { countInTextarea(event.target as HTMLTextAreaElement); });
        });
        //Call to set initial value
        countInTextarea(textarea);
    }
    if (textarea.classList.contains('tinymce') && textarea.id) {
        loadTinyMCE(textarea.id);
    }
}

function headingInit(heading: HTMLHeadingElement): void
{
    //Add ID attribute to header tags, if it's missing (needed for unique anchor links)
    if (!heading.hasAttribute('id')) {
        //Get initial ID
        const id = String(heading.textContent).replaceAll(/\s/gmu, `_`).
                                                replaceAll(/[^\p{L}\p{N}_-]/gmu, ``).
                                                replaceAll(/(?<beginning>^.{1,64})(?<theRest>.*$)/gmu, `$<beginning>`);
        //Get ID index, in case it's already used
        let index = 1;
        let altId = id;
        //Check if altID exists
        while (document.querySelector(`#${altId}`)) {
            //Increase index
            index += 1;
            altId = `${id}_${index}`;
        }
        heading.setAttribute('id', altId);
    }
    heading.addEventListener('click', (event: Event) => {
        //Checking for selection. If it's present most likely the text in anchor is being selected with intention of copying it.
        //In this case, if we copy the anchor link, we may provide undesired effect (although ctrl+c will most likely fire after this).
        const selection = window.getSelection();
        if (selection && selection.type !== 'Range') {
            //Generate anchor link
            const link = `${window.location.href.replaceAll(/(?<beforeDies>^[^#]*)(?<afterDies>#.*)?$/gmu, `$<beforeDies>`)}#${(event.target as HTMLHeadingElement).getAttribute('id') ?? ''}`;
            // Copy anchor link to clipboard
            navigator.clipboard.writeText(link).then(() => {
                addSnackbar(`Anchor link for "${(event.target as HTMLHeadingElement).textContent ?? ''}" copied to clipboard`, 'success');
            }, () => {
                addSnackbar(`Failed to copy anchor link for "${(event.target as HTMLHeadingElement).textContent ?? ''}"`,'failure');
            });
        }
    });
}

//Below is a list of input types, that do not make much sense to be tracked through keypress or paste events, in case it will be required at some time
//Including date/time types, even though some of them may fall back to textual fields. Doing this, since you can't predict this by checking browser version.
//Not including hidden (since it's hidden), image (since its purpose is unclear by default),
//range (unclear how to track to actually determine, that user stopped interaction),
//reset and submit (due to their purpose)
//const nonTextInputTypes = ['checkbox', 'color', 'date', 'datetime-local', 'file', 'month', 'number', 'radio', 'time', 'week',];
function formInit(form: HTMLFormElement): void
{
    //Prevent form submit on Enter, if action is empty (otherwise this causes page reload with additional question mark in address
    form.addEventListener('keypress', (event: KeyboardEvent) => { formEnter(event); });
    //List of input types, that are "textual" by default, thus can be tracked through keypress and paste events. In essence, these are types, that support maxlength attribute
    form.querySelectorAll('input[type="email"], input[type="password"], input[type="search"], input[type="tel"], input[type="text"], input[type="url"]').forEach((item) => {
        //Somehow backspace can be tracked only on keydown, not keypress
        item.addEventListener('keydown', inputBackSpace);
        if (!empty(item.getAttribute('maxlength'))) {
            ['input', 'change', ].forEach((eventType: string) => {
                item.addEventListener(eventType, autoNext);
            });
            item.addEventListener('paste', (event) => { void pasteSplit(event); });
        }
    });
}

function sampInit(samp: HTMLElement): void
{
    //Add a visual button
    //Modifying innerHTML instead of insertBefore, since block may not have any actual children in the first place, and as per https://developer.mozilla.org/en-US/docs/Web/API/Node/insertBefore
    //"When the element does not have a first child, then firstChild is null. The element is still appended to the parent, after the last child."
    //This results in same effect as with appendChild, that the image is inserted at the end, which is not what we want
    //Same is true for codeInit and blockquoteInit
    samp.innerHTML = `<img loading="lazy" decoding="async"  src="/img/copy.svg" alt="Click to copy block" class="copyQuote">${samp.innerHTML}`;
    //Add description
    const description = samp.getAttribute('data-description') ?? '';
    if (!empty(description)) {
        samp.innerHTML = `<span class="codeDesc">${description}</span>${samp.innerHTML}`;
    }
    //Add source
    const source = samp.getAttribute('data-source') ?? '';
    if (!empty(source)) {
        samp.innerHTML = `${samp.innerHTML}<span class="quoteSource">${source}</span>`;
    }
    //Add listener to the button. Needs to be the last one due to manipulations with innerHTML
    samp.querySelector('.copyQuote')?.addEventListener('click', (event: Event) => { copyQuote(event.target as HTMLElement); });
}

function codeInit(code: HTMLElement): void
{
    //Add a visual button
    code.innerHTML = `<img loading="lazy" decoding="async"  src="/img/copy.svg" alt="Click to copy block" class="copyQuote">${code.innerHTML}`;
    //Add description
    const description = code.getAttribute('data-description') ?? '';
    if (!empty(description)) {
        code.innerHTML = `<span class="codeDesc">${description}</span>${code.innerHTML}`;
    }
    //Add source
    const source = code.getAttribute('data-source') ?? '';
    if (!empty(source)) {
        code.innerHTML = `${code.innerHTML}<span class="quoteSource">${source}</span>`;
    }
    //Add listener to the button. Needs to be the last one due to manipulations with innerHTML
    code.querySelector('.copyQuote')?.addEventListener('click', (event: Event) => { copyQuote(event.target as HTMLElement); });
}

function blockquoteInit(quote: HTMLElement): void
{
    //Add a visual button
    quote.innerHTML = `<img loading="lazy" decoding="async"  src="/img/copy.svg" alt="Click to copy block" class="copyQuote">${quote.innerHTML}`;
    //Add author
    const author = quote.getAttribute('data-author') ?? '';
    if (!empty(author)) {
        quote.innerHTML = `<span class="quoteAuthor">${author}</span>${quote.innerHTML}`;
    }
    //Add source
    const source = quote.getAttribute('data-source') ?? '';
    if (!empty(source)) {
        quote.innerHTML = `${quote.innerHTML}<span class="quoteSource">${source}</span>`;
    }
    //Add listener to the button. Needs to be the last one due to manipulations with innerHTML
    quote.querySelector('.copyQuote')?.addEventListener('click', (event: Event) => { copyQuote(event.target as HTMLElement); });
}

function qInit(quote: HTMLQuoteElement): void
{
    //q tag is inline and a visual button does not suit it, so we add tooltip to it
    quote.setAttribute('data-tooltip', 'Click to copy quote');
    //Add listener
    quote.addEventListener('click', (event: Event) => { copyQuote(event.target as HTMLElement); });
}

function detailsInit(details: HTMLDetailsElement): void
{
    if (!details.classList.contains('persistent') && !details.classList.contains('spoiler') && !details.classList.contains('adult')) {
        //Close all details except currently selected one
        //details.addEventListener('click', (event) => {
        //    closeAllDetailsTags(event.target as HTMLDetailsElement);
        //});
        //Attach listener for clicks. Technically we can (and probably should) use 'toggle', but I was not able to achieve consistent behavior with it.
        const summary = details.querySelector('summary');
        if (summary) {
            summary.addEventListener('click', (event) => {
                closeAllDetailsTags(event.target as HTMLDetailsElement);
                resetDetailsTags(event.target as HTMLDetailsElement);
            });
        }
    }
}

function imgInit(img: HTMLImageElement): void
{
    //Add alt, if empty
    if (empty(img.alt)) {
        img.alt = basename(String(img.src));
    }
    //Wrap galleryZoom images in anchor
    if (img.classList.contains('galleryZoom')) {
        //Check if parent is already a link
        const parent = img.parentElement;
        if (parent && parent.nodeName.toLowerCase() !== 'a') {
            //Prepare link
            const link = document.createElement('a');
            link.href = img.src;
            link.target = '_blank';
            link.setAttribute('data-tooltip', (img.hasAttribute('data-tooltip') ? String(img.getAttribute('data-tooltip')) : String(img.alt)));
            link.classList.add('galleryZoom');
            //Create a clone of the image, and remove galleryZoom class for cleanliness
            const clone = img.cloneNode(true) as HTMLImageElement;
            clone.classList.remove('galleryZoom');
            //Append the clone to link
            link.appendChild(clone);
            //Replace original image with link
            img.replaceWith(link);
        }
    }
}

//Function to apply custom initializers from observer
function customizeNewElements(newNode: Node): void
{
    if (newNode.nodeType === 1) {
        const nodeName = newNode.nodeName.toLowerCase();
        switch(nodeName) {
            case 'a':
                newTabStyle(newNode as HTMLAnchorElement);
                break;
            case 'blockquote':
                blockquoteInit(newNode as HTMLElement);
                break;
            case 'code':
                codeInit(newNode as HTMLElement);
                break;
            case 'details':
                detailsInit(newNode as HTMLDetailsElement);
                break;
            case 'form':
                formInit(newNode as HTMLFormElement);
                break;
            case 'h1':
            case 'h2':
            case 'h3':
            case 'h4':
            case 'h5':
            case 'h6':
                headingInit(newNode as HTMLHeadingElement);
                break;
            case 'img':
                imgInit(newNode as HTMLImageElement);
                break;
            case 'input':
                inputInit(newNode as HTMLInputElement);
                break;
            case 'q':
                qInit(newNode as HTMLQuoteElement);
                break;
            case 'samp':
                sampInit(newNode as HTMLElement);
                break;
            case 'textarea':
                textareaInit(newNode as HTMLTextAreaElement);
                break;
            default:
                //Do nothing
                break;
        }
    }
}
