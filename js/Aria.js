//Accessibility related functions

//Adding some aria attributes to input elements.
function ariaNation(inputElement)
{
    'use strict';
    //Adjust aria-invalid based on whether input is valid or not
    inputElement.setAttribute('aria-invalid', !inputElement.validity.valid);
    //Add aria-required with value based on whether "required" attribute is present
    if (inputElement.required === true) {
        inputElement.setAttribute('aria-required', true);
    } else {
        inputElement.setAttribute('aria-required', false);
    }
    //Add checkbox role
    if (inputElement.hasAttribute('type') === true && inputElement.getAttribute('type') === 'checkbox') {
        inputElement.setAttribute('role', 'checkbox');
        //Add aria-checked value based on whether checkbox is checked
        inputElement.setAttribute('aria-checked', inputElement.checked);
        //Handle indeterminate state of checkboxes
        if (inputElement.indeterminate === true) {
            inputElement.setAttribute('aria-checked', 'mixed');
        }
    }
}

//This should be attached to all input tags to "change" and "input" events. Preferably to "focus" as well.
function ariaNationOnEvent(event)
{
    'use strict';
    ariaNation(event.target);
}
