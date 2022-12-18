class Input
{
    private static _instance: null | Input = null;

    constructor() {
        if (Input._instance) {
            return Input._instance;
        }
        //Register automated aria-invalid attribute adding
        Array.from(document.getElementsByTagName('input')).forEach(item => {
            this.init(item);
        });
        Input._instance = this;
    }

    //Simplifying attaching of listeners and initial run of ariaNation for new elements
    public init(inputElement: HTMLInputElement): void
    {
        ['focus', 'change', 'input',].forEach((eventType: string) => {
            inputElement.addEventListener(eventType, () => {this.ariaNation(inputElement);});
        });
        this.ariaNation(inputElement);
    }

    //Adding some aria attributes to input elements and doing other standardization stuff.
    public ariaNation(inputElement: HTMLInputElement): void
    {
        //Adjust aria-invalid based on whether input is valid or not
        inputElement.setAttribute('aria-invalid', String(!inputElement.validity.valid));
        //Add placeholder, if not present. Required more as a precaution for text-like inputs with no placeholder
        if (!inputElement.hasAttribute('placeholder')) {
            inputElement.setAttribute('placeholder', inputElement.value || inputElement.type || 'placeholder');
        }
        //Add missing type attribute
        if (!inputElement.getAttribute('type')) {
            inputElement.setAttribute('type', 'text');
        }
        //Get the type. For some reason sometimes .type does not return anything, so need to get .getAttribute()
        let type = inputElement.type ?? inputElement.getAttribute('type') ?? 'text';
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
}
