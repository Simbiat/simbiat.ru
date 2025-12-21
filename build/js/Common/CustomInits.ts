//Custom initializers for built-in elements, as an alternative way to built-in custom elements, that are not supported in Safari
function inputInit(input: HTMLInputElement): void {
  ['focus', 'change', 'input',].forEach((eventType: string) => {
    input.addEventListener(eventType, () => {
      ariaNation(input);
    });
  });
  //Initial application of the function
  ariaNation(input);
  if (input.getAttribute('type') === 'url' && !input.form) {
    input.addEventListener('paste', (event) => {
      void urlClean(event);
    });
  }
  if (input.classList.contains('toggle_details')) {
    input.addEventListener('click', () => {
      toggleDetailsButton(input);
    });
  }
  if (input.getAttribute('type') === 'file') {
    ['change', 'input',].forEach((eventType: string) => {
      input.addEventListener(eventType, () => {
        inputFileValidate(input);
      });
    });
  }
}

function textareaInit(textarea: HTMLTextAreaElement): void {
  //Give elements a default placeholder if there is none
  if (!textarea.hasAttribute('placeholder')) {
    textarea.setAttribute('placeholder', textarea.value || textarea.type || 'placeholder');
  }
  if (textarea.maxLength > 0) {
    //Attach listener
    ['change', 'keydown', 'keyup', 'input'].forEach((eventType: string) => {
      textarea.addEventListener(eventType, (event) => {
        countInTextarea(event.target as HTMLTextAreaElement);
      });
    });
    //Call to set initial value
    countInTextarea(textarea);
  }
  if (textarea.classList.contains('tinymce') && textarea.id) {
    loadTinyMCE(textarea.id);
  }
}

function headingInit(heading: HTMLHeadingElement): void {
  //Add ID attribute to header tags, if it's missing (needed for unique anchor links)
  if (!heading.hasAttribute('id')) {
    //Get initial ID
    let id = String(heading.textContent)
      .replaceAll(/\s/gmu, '_')
      .replaceAll(/[^a-zA-Z0-9_-]/gmu, '')
      .replaceAll(/^\d+/gmu, '')
      .replaceAll(/_{2,}/gmu, '_')
      .replaceAll(/(?<beginning>^.{1,64})(?<theRest>.*$)/gmu, `$<beginning>`)
      .replaceAll(/^_+$/gmu, '');
    if (empty(id)) {
      id = 'heading';
    }
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
  heading.addEventListener('click', (event: MouseEvent) => {
    //Get the element under the mouse pointer
    const elementUnderMouse = document.elementFromPoint(event.clientX, event.clientY);
    //Check if it's an <a> element
    if (elementUnderMouse && elementUnderMouse.tagName === 'A') {
      //Cancel this event if we clicked on an anchor, because it can confuse if we get notification about copy, and follow the link right away
      return;
    }
    //Checking for selection. If it's present most likely the text in anchor is being selected with intention of copying it.
    //In this case, if we copy the anchor link, we may provide undesired effect (although ctrl+c will most likely fire after this).
    const selection = window.getSelection();
    if (selection && selection.type !== 'Range') {
      //Generate anchor link
      const link = `${window.location.href.replaceAll(/(?<beforeSharp>^[^#]*)(?<afterSharp>#.*)?$/gmu, `$<beforeSharp>`)}#${(event.target as HTMLHeadingElement).getAttribute('id') ?? ''}`;
      // Copy anchor link to clipboard
      navigator.clipboard.writeText(link)
               .then(() => {
                 addSnackbar(`Anchor link for "${(event.target as HTMLHeadingElement).textContent ?? ''}" copied to clipboard`, 'success');
               }, () => {
                 addSnackbar(`Failed to copy anchor link for "${(event.target as HTMLHeadingElement).textContent ?? ''}"`, 'failure');
               });
    }
  });
}

// Below is a list of input types, that do not make much sense to be tracked through keypress or paste events, in case it will be required at some time
// Including date/time types, even though some of them may fall back to textual fields. Doing this, since you can't predict this by checking browser version.
// Not including hidden (since it's hidden), image (since its purpose is unclear by default),
// range (unclear how to track to actually determine, that user stopped interaction),
// reset and submit (due to their purpose)
// const nonTextInputTypes = ['checkbox', 'color', 'date', 'datetime-local', 'file', 'month', 'number', 'radio', 'time', 'week',];
function formInit(form: HTMLFormElement): void {
  //Prevent form submit on Enter, if action is empty (otherwise this causes page reload with additional question mark in address
  form.addEventListener('keypress', (event: KeyboardEvent) => {
    formEnter(event);
  });
  //For all elements that can be used inside a form add name, if it's missing. Make it equal to ID.
  form.querySelectorAll('button, datalist, fieldset, input, meter, progress, select, textarea')
      .forEach((item) => {
        if (!item.hasAttribute('data-noname') && (!item.hasAttribute('name') || empty(item.getAttribute('name'))) && !empty(item.id)) {
          item.setAttribute('name', item.id);
        }
      });
  //List of input types, that are "textual" by default, thus can be tracked through keypress and paste events. In essence, these are types, that support maxlength attribute
  form.querySelectorAll('input[type="email"], input[type="password"], input[type="search"], input[type="tel"], input[type="text"], input[type="url"]')
      .forEach((item) => {
        //Somehow backspace can be tracked only on keydown, not keypress
        item.addEventListener('keydown', inputBackSpace);
        if (!empty(item.getAttribute('maxlength'))) {
          ['input', 'change',].forEach((eventType: string) => {
            item.addEventListener(eventType, autoNext);
          });
          item.addEventListener('paste', (event) => {
            void pasteSplit(event);
          });
        }
      });
}

function sampInit(samp: HTMLElement): void {
  // Add a visual button
  // Modifying innerHTML instead of insertBefore, since block may not have any actual children in the first place, and as per https://developer.mozilla.org/en-US/docs/Web/API/Node/insertBefore
  // "When the element does not have a first child, then firstChild is null. The element is still appended to the parent, after the last child."
  // This results in same effect as with appendChild, that the image is inserted at the end, which is not what we want
  // Same is true for codeInit and blockquoteInit
  samp.innerHTML = `<img loading="lazy" decoding="async"  src="/assets/images/copy.svg" alt="Click to copy block" class="copy_quote">${samp.innerHTML}`;
  //Add description
  const description = samp.getAttribute('data-description') ?? '';
  if (!empty(description)) {
    samp.innerHTML = `<span class="code_desc">${description}</span>${samp.innerHTML}`;
  }
  //Add source
  const source = samp.getAttribute('data-source') ?? '';
  if (!empty(source)) {
    samp.innerHTML = `${samp.innerHTML}<span class="quote_source">${source}</span>`;
  }
  //Add listener to the button. Needs to be the last one due to manipulations with innerHTML
  samp.querySelector('.copy_quote')
      ?.addEventListener('click', (event: MouseEvent) => {
        copyQuote(event.target as HTMLElement);
      });
}

function codeInit(code: HTMLElement): void {
  //Add a visual button
  code.innerHTML = `<img loading="lazy" decoding="async"  src="/assets/images/copy.svg" alt="Click to copy block" class="copy_quote">${code.innerHTML}`;
  //Add description
  const description = code.getAttribute('data-description') ?? '';
  if (!empty(description)) {
    code.innerHTML = `<span class="code_desc">${description}</span>${code.innerHTML}`;
  }
  //Add source
  const source = code.getAttribute('data-source') ?? '';
  if (!empty(source)) {
    code.innerHTML = `${code.innerHTML}<span class="quote_source">${source}</span>`;
  }
  //Add listener to the button. Needs to be the last one due to manipulations with innerHTML
  code.querySelector('.copy_quote')
      ?.addEventListener('click', (event: MouseEvent) => {
        copyQuote(event.target as HTMLElement);
      });
}

function blockquoteInit(quote: HTMLElement): void {
  //Add a visual button
  quote.innerHTML = `<img loading="lazy" decoding="async"  src="/assets/images/copy.svg" alt="Click to copy block" class="copy_quote">${quote.innerHTML}`;
  //Add author
  const author = quote.getAttribute('data-author') ?? '';
  if (!empty(author)) {
    quote.innerHTML = `<span class="quote_author">${author}</span>${quote.innerHTML}`;
  }
  //Add source
  const source = quote.getAttribute('data-source') ?? '';
  if (!empty(source)) {
    quote.innerHTML = `${quote.innerHTML}<span class="quote_source">${source}</span>`;
  }
  //Add listener to the button. Needs to be the last one due to manipulations with innerHTML
  quote.querySelector('.copy_quote')
       ?.addEventListener('click', (event: MouseEvent) => {
         copyQuote(event.target as HTMLElement);
       });
}

function qInit(quote: HTMLQuoteElement): void {
  // q tag is inline and a visual button does not suit it, so we add tooltip to it
  if (!quote.hasAttribute('data-tooltip')) {
    quote.setAttribute('data-tooltip', 'Click to copy quote');
  }
  // Add listener
  quote.addEventListener('click', (event: MouseEvent) => {
    copyQuote(event.target as HTMLElement);
  });
}

function varInit(variable: HTMLElement): void {
  // var tag is inline and a visual button does not suit it, so we add tooltip to it
  if (!variable.hasAttribute('data-tooltip')) {
    variable.setAttribute('data-tooltip', 'Click to copy variable');
  }
  // Add listener
  variable.addEventListener('click', (event: MouseEvent) => {
    copyQuote(event.target as HTMLElement);
  });
}

function detailsInit(details: HTMLDetailsElement): void {
  if (!details.classList.contains('persistent') && !details.classList.contains('spoiler') && !details.classList.contains('adult')) {
    // Attach listener for clicks. Technically we can (and probably should) use 'toggle', but I was not able to achieve consistent behavior with it.
    const summary = details.querySelector('summary');
    if (summary) {
      summary.addEventListener('click', (event) => {
        closeAllDetailsTags(event.target as HTMLDetailsElement);
        resetDetailsTags(event.target as HTMLDetailsElement);
      });
    }
  }
}

function imgInit(img: HTMLImageElement): void {
  //Add alt, if empty
  if (empty(img.alt)) {
    img.alt = basename(String(img.src));
  }
  if (empty(img.loading)) {
    img.loading = 'lazy';
  }
  if (empty(img.decoding)) {
    img.decoding = 'async';
  }
  //Wrap gallery_zoom images in anchor
  if (img.classList.contains('gallery_zoom')) {
    //Check if parent is already a link
    const parent = img.parentElement;
    if (parent && parent.nodeName.toLowerCase() !== 'a') {
      //Prepare link
      const link = document.createElement('a');
      link.href = img.src;
      link.target = '_blank';
      if (!link.hasAttribute('data-tooltip')) {
        link.setAttribute('data-tooltip', (img.hasAttribute('data-tooltip') ? String(img.getAttribute('data-tooltip')) : String(img.alt)));
      }
      link.classList.add('gallery_zoom');
      //Create a clone of the image, and remove gallery_zoom class for cleanliness
      const clone = img.cloneNode(true) as HTMLImageElement;
      clone.classList.remove('gallery_zoom');
      //Append the clone to link
      link.appendChild(clone);
      //Replace original image with link
      img.replaceWith(link);
    } else if (parent && parent.nodeName.toLowerCase() === 'a') {
      //Handle existing anchor
      (parent as HTMLAnchorElement).href = img.src;
      (parent as HTMLAnchorElement).target = '_blank';
      if (!parent.hasAttribute('data-tooltip')) {
        parent.setAttribute('data-tooltip', (img.hasAttribute('data-tooltip') ? String(img.getAttribute('data-tooltip')) : String(img.alt)));
      }
      parent.classList.add('gallery_zoom');
      img.classList.contains('gallery_zoom');
    }
  }
}

function dialogInit(dialog: HTMLDialogElement): void {
  if (dialog.classList.contains('modal')) {
    dialog.addEventListener('click', (event) => {
      const target = event.target;
      if (target) {
        if (target === dialog) {
          dialog.close();
        }
      }
    });
  }
}

function anchorInit(anchor: HTMLAnchorElement): void {
  // If `href` is empty - do not do anything
  if (empty(anchor.href)) {
    return;
  }
  const current_URL = new URL(anchor.href);
  // Add `target="_blank"` if link is not from the current domain
  if (current_URL.host !== window.location.host) {
    anchor.target = '_blank';
    // Add noopener and noreferrer for some level of security/privacy.
    // Technically all modern browsers are supposed to add them during onclick, but can't trust that.
    // Also, noreferrer already implies noopener, but allegedly some browsers at least used to require both, not following the spec.
    if (empty(anchor.rel)) {
      anchor.rel = 'noopener noreferrer';
    } else {
      if (!anchor.rel.includes('noopener')) {
        anchor.rel += ' noopener';
      }
      if (!anchor.rel.includes('noreferrer')) {
        anchor.rel += ' noreferrer';
      }
    }
  }
  // Add an icon indicating that link will open in new tab
  if (anchor.target === '_blank' && !anchor.innerHTML.includes('assets/images/newtab.svg') && !anchor.classList.contains('no_new_tab_icon')) {
    anchor.innerHTML += '<img class="new_tab_icon" src="/assets/images/newtab.svg" alt="Opens in new tab" loading="lazy" decoding="async">';
    // I am aware of some extensions adding blank anchors, that can break the code, so we need to check if href is empty
  } else if (!empty(anchor.href) && !empty(current_URL.hash) && current_URL.origin + current_URL.host + current_URL.pathname === window.location.origin + window.location.host + window.location.pathname) {
    // Logic to update URL if this is a hash link for current page
    anchor.addEventListener('click', () => {
      if (!window.location.hash.toLowerCase()
                 .startsWith('#gallery=')) {
        history.replaceState(document.title, document.title, `${current_URL.hash}`);
      }
    });
  }
}

// Function to apply custom initializers from observer
function customizeNewElements(new_node: Node): void {
  if (new_node.nodeType === 1) {
    const node_name = new_node.nodeName.toLowerCase();
    switch (node_name) {
      case 'a':
        anchorInit(new_node as HTMLAnchorElement);
        break;
      case 'blockquote':
        blockquoteInit(new_node as HTMLElement);
        break;
      case 'code':
        codeInit(new_node as HTMLElement);
        break;
      case 'details':
        detailsInit(new_node as HTMLDetailsElement);
        break;
      case 'form':
        formInit(new_node as HTMLFormElement);
        break;
      case 'h1':
      case 'h2':
      case 'h3':
      case 'h4':
      case 'h5':
      case 'h6':
        headingInit(new_node as HTMLHeadingElement);
        break;
      case 'img':
        imgInit(new_node as HTMLImageElement);
        break;
      case 'input':
        inputInit(new_node as HTMLInputElement);
        break;
      case 'q':
        qInit(new_node as HTMLQuoteElement);
        break;
      case 'var':
        varInit(new_node as HTMLElement);
        break;
      case 'samp':
        sampInit(new_node as HTMLElement);
        break;
      case 'textarea':
        textareaInit(new_node as HTMLTextAreaElement);
        break;
      default:
        // Do nothing
        break;
    }
  }
}
