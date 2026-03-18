//Common function to add snackbars. Originally it was a separate class, but it makes little sense to have it that way.
export function addSnackbar(text: string, color = '', milliseconds = 3000): void {
  const snacks = document.querySelector('snack-bar');
  const template = document.querySelector('#snackbar_template');
  if (snacks && template) {
    //Generate element
    const new_snack = (template as HTMLTemplateElement).content.cloneNode(true) as DocumentFragment;
    const snack = new_snack.querySelector('dialog');
    if (snack !== null) {
      //Add text
      const text_block = snack.querySelector('.snack_text');
      if (text_block !== null) {
        text_block.innerHTML = text;
      }
      //Update milliseconds for auto-closure
      snack.querySelector('snack-close')
           ?.setAttribute('data-close-in', String(milliseconds));
      //Add class for color
      if (color) {
        snack.classList.add(color);
      }
      //Add the element to the parent
      snacks.appendChild(snack);
      snack.show();
    }
  }
}

//Get meta content
export function getMeta(meta_name: string): string | null {
  const metas = Array.from(document.querySelectorAll('meta'));
  const tag = metas.find((obj) => {
    return obj.name === meta_name;
  });
  if (tag) {
    return tag.getAttribute('content');
  }
  return null;
}

//Update the document title and push to history. Required, since browsers mostly ignore title argument in pushState
export function updateHistory(new_url: string, title: string): void {
  //Update title and/or URL only if there were changes
  if (document.title !== title) {
    document.title = title;
  }
  if (document.location.href !== new_url) {
    window.history.pushState(title, title, new_url);
  }
}

//Function to intercept both form submission and Enter key pressed in the form (which normally also submits it)
export function submitIntercept(form: HTMLFormElement, callable: () => void): void {
  const is_bot = !empty(getMeta('is_bot'));
  form.removeEventListener('submit', submitDefaultIntercept);
  form.removeEventListener('keypress', submitDefaultIntercept);
  form.setAttribute('data-intercepted', 'true');
  form.addEventListener('submit', (event: SubmitEvent) => {
    event.preventDefault();
    event.stopPropagation();
    if (is_bot) {
      addSnackbar('No form submissions are allowed for bots', 'error');
    } else {
      callable();
    }
    return false;
  });
  form.addEventListener('keydown', (event: KeyboardEvent) => {
    if (event.code === 'Enter') {
      event.preventDefault();
      event.stopPropagation();
      if (is_bot) {
        addSnackbar('No form submissions are allowed for bots', 'error');
      } else {
        callable();
      }
      return false;
    }
    return true;
  });
}

export function submitDefaultIntercept(event: SubmitEvent | KeyboardEvent): boolean {
  if (event.type === 'submit' || (event.type === 'keydown' && (event as KeyboardEvent).code === 'Enter')) {
    event.preventDefault();
    event.stopPropagation();
    addSnackbar('Default form events blocked', 'warning');
    return false;
  }
  return true;
}

//Remove table row based containing the element
export function deleteRow(element: HTMLElement): boolean {
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
export function basename(text: string): string {
  return text.replace(/^.*\/|\.[^.]*$/gu, '');
}

//Function replicating PHP's rawurlencode for consistency.
export function rawurlencode(str: string): string {
  const definitely_string = String(str);
  return encodeURIComponent(definitely_string)
    .replace(/!/ug, '%21')
    .replace(/'/ug, '%27')
    .replace(/\(/ug, '%28')
    .replace(/\)/ug, '%29')
    .replace(/\*/ug, '%2A');
}

//Function to replicate PHP's empty()
export function empty(variable: unknown): boolean {
  if (typeof variable === 'undefined' || [null, false, 0, 'NaN', undefined].includes(variable as any)) {
    return true;
  }
  if (typeof variable === 'string') {
    return (/^[\s\p{C}]*$/ui).test(variable);
  }
  if (Array.isArray(variable) || variable instanceof NodeList || variable instanceof HTMLCollection) {
    return variable.length === 0;
  }
  if (typeof variable === 'object') {
    return JSON.stringify(variable) === '{}';
  }
  return false;
}

//Function to force page refresh. Regular reload() often hits cache, thus not properly updating
export function pageRefresh(new_url?: string): void {
  let url;
  if (empty(new_url)) {
    url = new URL(document.location.href);
  } else {
    window.location.assign(encodeURI(new_url as string));
    url = new URL(new_url as string, document.location.href);
  }
  url.searchParams.set('force_reload', String(Date.now()));
  window.location.replace(url.toString());
}

//Copy the text of tags like q, samp, code, blockquote, var
export function copyQuote(target: HTMLElement): string {
  let node;
  //Get the parent node if click was on the copy picture/button
  if (target.tagName.toLowerCase() === 'q' || target.tagName.toLowerCase() === 'var') {
    node = target;
  } else {
    node = target.parentElement;
  }
  if (!node) {
    return '';
  }
  const tag_name = node.tagName.toLowerCase();
  let tag: string;
  switch (tag_name) {
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
    case 'var':
      tag = 'Variable';
      break;
    default:
      //Exit, since we do not support this tpy of node
      return '';
  }
  //Set text
  let quote_text = String(node.textContent);
  //Remove author from blockquotes
  if (tag_name === 'blockquote' && node.hasAttribute('data-author')) {
    const author_match = new RegExp(`^(${String(node.getAttribute('data-author'))})`, 'ui');
    quote_text = quote_text.replace(author_match, '');
  }
  //Remove description from code and samp
  if ((tag_name === 'samp' || tag_name === 'code') && node.hasAttribute('data-description')) {
    const desc_match = new RegExp(`^(${String(node.getAttribute('data-description'))})`, 'ui');
    quote_text = quote_text.replace(desc_match, '');
  }
  //Remove source from blockquotes, code and samp
  if (['blockquote', 'samp', 'code'].includes(tag_name) && node.hasAttribute('data-source')) {
    const source_match = new RegExp(`(${String(node.getAttribute('data-source'))})$`, 'ui');
    quote_text = quote_text.replace(source_match, '');
  }
  navigator.clipboard.writeText(quote_text)
           .then(() => {
             addSnackbar(`${tag} copied to clipboard`, 'success');
           }, () => {
             addSnackbar(`Failed to copy ${tag.toLowerCase()}`, 'failure');
           });
  return String(node.textContent);
}

//Check if a remote file exists
// noinspection FunctionNamingConventionJS Want to keep the same name as in PHP
export async function is_file(url: string): Promise<boolean> {
  return new Promise((resolve, reject) => {
    fetch(url, {'method': 'HEAD'})
      .then((response) => {
        if (response.ok) {
          resolve(true);
        } else {
          resolve(false);
        }
      })
      .catch((error) => {
        reject(error);
      });
  });
}
