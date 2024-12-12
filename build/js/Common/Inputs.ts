//Functions related to various input, form and related elements

//Adding some aria attributes to input elements and doing other standardization stuff.
function ariaNation(inputElement: HTMLInputElement): void
{
    //Adjust aria-invalid based on whether input is valid or not
    inputElement.setAttribute('aria-invalid', String(!inputElement.validity.valid));
    //Add placeholder, if not present. Required more as a precaution for text-like inputs with no placeholder
    if (!inputElement.hasAttribute('placeholder')) {
        inputElement.setAttribute('placeholder', inputElement.value || inputElement.type || 'placeholder');
    }
    //Add missing type attribute
    if (empty(inputElement.getAttribute('type'))) {
        inputElement.setAttribute('type', 'text');
    }
    let type;
    if (empty(inputElement.type)) {
        type = 'text';
    } else {
        type = inputElement.type;
    }
    //Add aria-required with value based on whether "required" attribute is present
    if (['text', 'search', 'url', 'tel', 'email', 'password', 'date', 'month', 'week', 'time', 'datetime-local', 'number', 'checkbox', 'radio', 'file',].includes(String(type))) {
        if (inputElement.required) {
            inputElement.setAttribute('aria-required', String(true));
        } else {
            inputElement.setAttribute('aria-required', String(false));
        }
    }
    //Add checkbox role
    if (type === 'checkbox') {
        inputElement.setAttribute('role', 'checkbox');
        //Add aria-checked value based on whether checkbox is checked
        inputElement.setAttribute('aria-checked', String(inputElement.checked));
        //Handle indeterminate state of checkboxes
        if (inputElement.indeterminate) {
            inputElement.setAttribute('aria-checked', 'mixed');
        }
    }
    //Get and show color in attribute. For some reason, CSS's attr(value) does not show the value, if I do not do this
    if (type === 'checkbox') {
        inputElement.setAttribute('value', inputElement.value);
    }
}

//Function to start/stop spinner and disable/enable respective button
function buttonToggle(button: HTMLInputElement, enable = true): void
{
    let spinner;
    //If the button is inside form, then search for spinner inside it first
    if (button.form) {
        spinner = button.form.querySelector('.spinner');
    }
    //If spinner is empty at this point, try to get it from parent element
    if (!spinner) {
        const buttonParent = button.parentElement;
        if (buttonParent) {
            spinner = buttonParent.querySelector('.spinner');
        }
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

//Function to count characters inside textarea elements and update their respective labels
function countInTextarea(textarea: HTMLTextAreaElement): void
{
    if (textarea.labels[0] && textarea.maxLength) {
        const label = textarea.labels[0];
        label.setAttribute('data-curlength', `(${textarea.value.length}/${textarea.maxLength}ch)`);
        label.classList.remove('at_the_limit', 'close_to_limit');
        if (textarea.value.length >= textarea.maxLength) {
            label.classList.add('at_the_limit');
        } else if (((100 * textarea.value.length) / textarea.maxLength) >= 75) {
            label.classList.add('close_to_limit');
        }
    }
}

//Find next/previous input
function nextInput(initial: HTMLInputElement, reverse = false): HTMLInputElement | null
{
    //Get form
    const form = initial.form;
    //Iterate textual inputs inside the form. Not using previousElementSibling, because next/previous input may not be a sibling on the same level
    if (form) {
        let previous;
        for (const moveTo of form.querySelectorAll('input[type="email"], input[type="password"], input[type="search"], input[type="tel"], input[type="text"], input[type="url"]')) {
            if (reverse) {
                //Check if current element in loop is the initial one, meaning
                if (moveTo === initial) {
                    //If previous is not empty - share it. Otherwise - false, since initial input is first in the form
                    if (previous) {
                        return previous as HTMLInputElement;
                    }
                    return null;
                }
                //If we are moving forward and initial node is the previous one
            } else if (previous && previous === initial) {
                return moveTo as HTMLInputElement;
            }
            //Update previous input
            previous = moveTo;
        }
    }
    return null;
}

async function pasteSplit(event: Event): Promise<void>
{
    // @ts-expect-error: As of time of writing clipboard-read is not supported by Safari and Firefox and TypeScript complains about the value. But it is a valid one for Chrome, thus suppressing the error.
    const permission = await navigator.permissions.query({ 'name': 'clipboard-read',}).catch(() => {
                                          //Do nothing, if clipboard reading is not supported, it will fail on next check
                                      });
    //Check permission is granted or not
    if (permission && permission.state !== 'denied') {
        //Get buffer
        void navigator.clipboard.readText().then((result) => {
                          let buffer = result.toString();
                          //Get initial element
                          let current = event.target;
                          if (current === null) {
                              //If somehow we got here - exit early
                              return;
                          }
                          //Get initial length attribute
                          let maxLength = parseInt((current as HTMLInputElement).getAttribute('maxlength') ?? '0', 10);
                          //Loop while the buffer is too large
                          while (current !== null && maxLength && buffer.length > maxLength) {
                              //Ensure input value is updated
                              (current as HTMLInputElement).value = buffer.substring(0, maxLength);
                              //Trigger input event to bubble any bound events
                              current.dispatchEvent(new Event('input', {
                                  'bubbles': true,
                                  'cancelable': true,
                              }));
                              //Do not spill over if a field is invalid
                              if (!(current as HTMLInputElement).validity.valid) {
                                  return;
                              }
                              //Update buffer value (not the buffer itself)
                              buffer = buffer.substring(maxLength);
                              //Get next node
                              current = nextInput((current as HTMLInputElement), false);
                              if (current) {
                                  //Focus to provide visual identification of a switch
                                  (current as HTMLInputElement).focus();
                                  //Update maxLength
                                  maxLength = parseInt((current as HTMLInputElement).getAttribute('maxlength') ?? '0', 10);
                              }
                          }
                          //Check if we still have a valid node
                          if (current) {
                              //Dump everything we can from leftovers
                              (current as HTMLInputElement).value = buffer;
                              //Trigger input event to bubble any bound events
                              current.dispatchEvent(new Event('input', {
                                  'bubbles': true,
                                  'cancelable': true,
                              }));
                          }
                      });
    }
}

function formEnter(event: KeyboardEvent): boolean
{
    if (event.target) {
        const form = (event.target as HTMLInputElement).form;
        if (form && (event.code === 'Enter' || event.code === 'NumpadEnter') && !form.action) {
            event.stopPropagation();
            event.preventDefault();
            return false;
        }
    }
    return true;
}

//Track backspace and focus previous input field, if input is empty, when it's pressed
function inputBackSpace(event: Event): void
{
    const current = event.target as HTMLInputElement;
    if ((event as KeyboardEvent).code === 'Backspace' && !current.value) {
        const moveTo = nextInput(current, true);
        if (moveTo) {
            moveTo.focus();
            //Ensure, that cursor ends up at the end of the previous field
            moveTo.selectionEnd = moveTo.value.length;
            moveTo.selectionStart = moveTo.value.length;
        }
    }
}

//Focus next field, if current is filled to the brim and valid
function autoNext(event: Event): void
{
    const current = event.target as HTMLInputElement;
    //Get length attribute
    const maxLength = parseInt(current.getAttribute('maxlength') ?? '0', 10);
    //Check it against value length
    if (maxLength && current.value.length === maxLength && current.validity.valid) {
        const moveTo = nextInput(current, false);
        if (moveTo) {
            moveTo.focus();
        }
    }
}
