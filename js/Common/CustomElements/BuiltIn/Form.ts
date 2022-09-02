class Form
{
    private static _instance: null | Form = null;

    //List of other input types, that do not make much sense to be tracked through keypress or paste events
    //Including date/time types, even though some of them may fall back to textual fields. Doing this, since you can't predict this by checking browser version.
    //Not including hidden (since it's hidden), image (since its purpose is unclear by default),
    //range (unclear how to track to actually determine, that user stopped interaction),
    //reset and submit (due to their purpose)
    //Currently not used
    //const nonTextInputTypes = ['checkbox', 'color', 'date', 'datetime-local', 'file', 'month', 'number', 'radio', 'time', 'week',];

    constructor() {
        if (Form._instance) {
            return Form._instance;
        }
        //Prevent form submit on Enter, if action is empty (otherwise this causes page reload with additional question mark in address
        document.querySelectorAll('form').forEach((item)=>{
            item.addEventListener('keypress', (event: KeyboardEvent) => {this.formEnter(event)});
        });
        //List of input types, that are "textual" by default, thus can be tracked through keypress and paste events. In essence, these are types, that support maxlength attribute
        document.querySelectorAll('form input[type="email"], form input[type="password"], form input[type="search"], form input[type="tel"], form input[type="text"], form input[type="url"]').forEach((item)=>{
            //Somehow backspace can be tracked only on keydown, not keypress
            item.addEventListener('keydown', this.inputBackSpace.bind(this));
            if (item.getAttribute('maxlength')) {
                item.addEventListener('input', this.autoNext.bind(this));
                item.addEventListener('change', this.autoNext.bind(this));
                item.addEventListener('paste', this.pasteSplit.bind(this));
            }
        });
        Form._instance = this;
    }

    public formEnter(event: KeyboardEvent): void | boolean
    {
        let form = (event.target as HTMLInputElement).form as HTMLFormElement;
        if ((event.code === 'Enter' || event.code === 'NumpadEnter') && !form.action) {
            event.stopPropagation();
            event.preventDefault();
            return false;
        }
    }

    //Function replicating PHP's rawurlencode for consistency.
    public rawurlencode(str: string): string
    {
        str = str + '';
        return encodeURIComponent(str)
            .replace(/!/ug, '%21')
            .replace(/'/ug, '%27')
            .replace(/\(/ug, '%28')
            .replace(/\)/ug, '%29')
            .replace(/\*/ug, '%2A');
    }

    //Track backspace and focus previous input field, if input is empty, when it's pressed
    private inputBackSpace(event: Event): void
    {
        let current = event.target as HTMLInputElement;
        if ((event as KeyboardEvent).code === 'Backspace' && !current.value) {
            let moveTo = this.nextInput(current, true) as HTMLInputElement;
            if (moveTo) {
                moveTo.focus();
                //Ensure, that cursor ends up at the end of the previous field
                moveTo.selectionStart = moveTo.selectionEnd = moveTo.value.length;
            }
        }
    }

    //Focus next field, if current is filled to the brim and valid
    private autoNext(event: Event): void
    {
        let current = event.target as HTMLInputElement;
        //Get length attribute
        let maxLength = parseInt(current.getAttribute('maxlength') ?? '0');
        //Check it against value length
        if (maxLength && current.value.length === maxLength && current.validity.valid) {
            let moveTo = this.nextInput(current, false) as HTMLInputElement;
            if (moveTo) {
                moveTo.focus();
            }
        }
    }

    //Find next/previous input
    public nextInput(initial: HTMLInputElement, reverse: boolean = false): HTMLInputElement | boolean
    {
        //Get form
        let form = initial.form;
        //Iterate textual inputs inside the form. Not using previousElementSibling, because next/previous input may not be a sibling on the same level
        if (form) {
            let previous;
            for (let moveTo of form.querySelectorAll('input[type="email"], input[type="password"], input[type="search"], input[type="tel"], input[type="text"], input[type="url"]')) {
                if (reverse) {
                    //Check if current element in loop is the initial one, meaning
                    if (moveTo === initial) {
                        //If previous is not empty - share it. Otherwise - false, since initial input is first in the form
                        if (previous) {
                            return previous as HTMLInputElement;
                        } else {
                            return false;
                        }
                    }
                } else {
                    //If we are moving forward and initial node is the previous one
                    if (previous && previous === initial) {
                        return moveTo as HTMLInputElement;
                    }
                }
                //Update previous input
                previous = moveTo;
            }
        }
        return false;
    }

    private async pasteSplit(event: Event): Promise<void>
    {
        //As of time of writing clipboard-read is not supported by Firefox and TypeScript complains about the value. But it is a valid one for Chrome, thus suppressing the error.
        // @ts-ignore
        let permission = await navigator.permissions.query({ name: 'clipboard-read',}).catch(() => {
            console.error('Your browser does not support clipboard-read permission.');
        });
        //Check permission is granted or not
        if (permission && permission.state !== 'denied') {
            //Get buffer
            navigator.clipboard.readText().then(result => {
                let buffer = result.toString();
                //Get initial element
                let current = event.target as HTMLInputElement;
                //Get initial length attribute
                let maxLength = parseInt(current.getAttribute('maxlength') ?? '0');
                //Loop while the buffer is too large
                while (current && maxLength && buffer.length > maxLength) {
                    //Ensure input value is updated
                    current.value = buffer.substring(0, maxLength);
                    //Trigger input event to bubble any bound events
                    current.dispatchEvent(new Event('input', {
                        bubbles: true,
                        cancelable: true,
                    }));
                    //Do not spill over if a field is invalid
                    if (!current.validity.valid) {
                        return false;
                    }
                    //Update buffer value (not the buffer itself)
                    buffer = buffer.substring(maxLength);
                    //Get next node
                    current = this.nextInput(current, false) as HTMLInputElement;
                    if (current) {
                        //Focus to provide visual identification of a switch
                        current.focus();
                        //Update maxLength
                        maxLength = parseInt(current.getAttribute('maxlength') ?? '0');
                    }
                }
                //Check if we still have a valid node
                if (current) {
                    //Dump everything we can from leftovers
                    current.value = buffer;
                    //Trigger input event to bubble any bound events
                    current.dispatchEvent(new Event('input', {
                        bubbles: true,
                        cancelable: true,
                    }));
                }
                return true;
            });
        }
    }
}
