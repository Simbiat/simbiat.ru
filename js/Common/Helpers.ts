//Get meta content
function getMeta(metaName: string): string|null {
    const metas = Array.from(document.getElementsByTagName('meta'));
    let tag = metas.find(obj => {
        return obj.name === metaName
    })
    if (tag) {
        return tag.getAttribute('content');
    } else {
        return null;
    }
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
function submitIntercept(form: HTMLFormElement, callable: Function): void
{
    form.addEventListener('submit', function(event:SubmitEvent) {
        event.preventDefault();
        event.stopPropagation();
        callable();
        return false;
    });
    form.addEventListener('keydown', function(event: KeyboardEvent) {
        if(event.code === 'Enter') {
            event.preventDefault();
            event.stopPropagation();
            callable();
            return false;
        } else {
            return true;
        }
    });
}

//Remove table row based containing element
function deleteRow(element: HTMLElement): boolean
{
    let table = element.closest('table') as HTMLTableElement;
    //Get row number
    let tr = (element.closest('tr') as HTMLTableRowElement).rowIndex;
    if (table && tr) {
        table.deleteRow(tr);
        return true;
    } else {
        return false;
    }
}

//Simulation of basename() function to return only the name of the file (without path or extension)
function basename(text: string): string
{
    return text.replace(/^.*\/|\.[^.]*$/g, '');
}

//Function to start/stop spinner and disable/enable respective button
function buttonToggle(button: HTMLInputElement, enable: boolean = true): void
{
    let spinner;
    //If the button is inside form, then search for spinner inside it first
    if (button.form) {
        spinner = button.form.querySelector('.spinner');
    }
    //If spinner is empty at this point, try to get it from parent element
    if (!spinner) {
        spinner = (button.parentElement as HTMLElement).querySelector('.spinner');
    }
    //Check if button is disabled
    if (button.disabled) {
        //Enabled button, if we do not want it to stay disabled
        if (enable) {
            button.disabled = false;
        }
        //Hide spinner
        if (spinner) {
            spinner.classList.add('hidden');
        }
    } else {
        //Disable button
        button.disabled = true;
        //Show spinner
        if (spinner) {
            spinner.classList.remove('hidden');
        }
    }
}

//Function replicating PHP's rawurlencode for consistency.
function rawurlencode(str: string): string
{
    str = str + '';
    return encodeURIComponent(str)
        .replace(/!/ug, '%21')
        .replace(/'/ug, '%27')
        .replace(/\(/ug, '%28')
        .replace(/\)/ug, '%29')
        .replace(/\*/ug, '%2A');
}
