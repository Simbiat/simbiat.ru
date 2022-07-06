/*exported ariaInit*/

//Accessibility related functions

//General initialization
function ariaInit(item: HTMLInputElement): void
{
    item.addEventListener('focus', ariaNationOnEvent);
    item.addEventListener('change', ariaNationOnEvent);
    item.addEventListener('input', ariaNationOnEvent);
    //Force the update of the values right now
    ariaNation(item);
}

//Adding some aria attributes to input elements.
function ariaNation(inputElement: HTMLInputElement): void
{
    //Adjust aria-invalid based on whether input is valid or not
    inputElement.setAttribute('aria-invalid', String(!inputElement.validity.valid));
    //Add aria-required with value based on whether "required" attribute is present
    if (inputElement.hasAttribute('type') && ['text', 'search', 'url', 'tel', 'email', 'password', 'date', 'month', 'week', 'time', 'datetime-local', 'number', 'checkbox', 'radio', 'file',].includes(String(inputElement.getAttribute('type')))) {
        if (inputElement.required) {
            inputElement.setAttribute('aria-required', String(true));
        } else {
            inputElement.setAttribute('aria-required', String(false));
        }
    }
    //Add checkbox role
    if (inputElement.hasAttribute('type') && inputElement.getAttribute('type') === 'checkbox') {
        inputElement.setAttribute('role', 'checkbox');
        //Add aria-checked value based on whether checkbox is checked
        inputElement.setAttribute('aria-checked', String(inputElement.checked));
        //Handle indeterminate state of checkboxes
        if (inputElement.indeterminate) {
            inputElement.setAttribute('aria-checked', 'mixed');
        }
    }
}

//This should be attached to all input tags to "change" and "input" events. Preferably to "focus" as well.
function ariaNationOnEvent(event: Event): void
{
    ariaNation(event.target as HTMLInputElement);
}
