class Input
{
    private static _instance: null | Input = null;

    constructor() {
        if (Input._instance) {
            return Input._instance;
        }
        //Register automated aria-invalid attribute adding
        Array.from(document.getElementsByTagName('input')).forEach(item => {
            ariaInit(item);
            //Add placeholder, if not present. Required more as a precaution for text-like inputs with no placeholder
            if (!item.hasAttribute('placeholder')) {
                item.setAttribute('placeholder', item.value || item.type || 'placeholder');
            }
            //Attach listeners for color picker
            if (item.type === 'color') {
                item.addEventListener('focus', colorValueOnEvent);
                item.addEventListener('change', colorValueOnEvent);
                item.addEventListener('input', colorValueOnEvent);
                colorValue(item);
            }
        });
        Input._instance = this;
    }
}

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

//Get and show color in attribute. For some reason, CSS's attr(value) does not show the value, if I do not do this
function colorValue(target: HTMLInputElement): void
{
    target.setAttribute('value', target.value);
}
function colorValueOnEvent(event: Event): void
{
    colorValue(event.target as HTMLInputElement);
}
