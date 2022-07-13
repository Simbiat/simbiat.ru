//List of input types, that are "textual" by default, thus can be tracked through keypress and paste events. In essence,
//these are types, that support maxlength attribute
const textInputTypes = ['email', 'password', 'search', 'tel', 'text', 'url', ];

//List of other input types, that do not make much sense to be tracked through keypress or paste events
//Including date/time types, even though some of them may fall back to textual fields. Doing this, since you can't predict this by checking browser version.
//Not including hidden (since it's hidden), image (since its purpose is unclear by default),
//range (unclear how to track to actually determine, that user stopped interaction),
//reset and submit (due to their purpose)
const nonTextInputTypes = ['checkbox', 'color', 'date', 'datetime-local', 'file', 'month', 'number', 'radio', 'time', 'week',];

//Handle dynamic action attribute for search inputs
function formInit(): void
{
    document.querySelectorAll('form').forEach((item)=>{
        item.addEventListener('keypress', formEnter);
    });
    //Forms with dynamic actions (expected to be search forms only at the time of writing)
    document.querySelectorAll('form[data-baseURL] input[type=search]').forEach((item)=>{
        item.addEventListener('input', searchAction);
        item.addEventListener('change', searchAction);
        item.addEventListener('focus', searchAction);
    });
    document.querySelectorAll('form input').forEach((item)=>{
        if (textInputTypes.includes((item as HTMLInputElement).type)) {
            //Somehow backspace can be tracked only on keydown, not keypress
            item.addEventListener('keydown', inputBackSpace);
            if (item.getAttribute('maxlength')) {
                item.addEventListener('input', autoNext);
                item.addEventListener('change', autoNext);
                item.addEventListener('paste', pasteSplit);
            }
        }
        if (nonTextInputTypes.includes((item as HTMLInputElement).type)) {

        }
    });
}


//Switch to determine which function to call on submit
const submitFunctions: { [key: string]: string } = {
    'signinup': 'singInUpSubmit',
    'addMailForm': 'addMail',
    'ff_track_register': 'ffTrackAdd',
    'password_change': 'passwordChange',
};

//Function to intercept form submission
function submitIntercept(formId: string): void
{
    let form = document.getElementById(formId);
    if (form && submitFunctions[formId]) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            event.stopPropagation();
            //Have no idea what it does not like
            // @ts-ignore
            window[submitFunctions[formId]]();
            return false;
        });
        form.onkeydown = function(event){
            if(event.code === 'Enter'){
                event.preventDefault();
                event.stopPropagation();
                //Have no idea what it does not like
                // @ts-ignore
                window[submitFunctions[formId]]();
                return false;
            }
            return true;
        };
    }
}

function searchAction(event: Event): void
{
    let search = event.target as HTMLInputElement;
    let form = search.form as HTMLFormElement;
    if (search.value === '') {
        form.action = String(form.getAttribute('data-baseURL'));
    } else {
        form.action = form.getAttribute('data-baseURL') + rawurlencode(search.value);
    }
    //Ensure that form will use GET method. This adds unnecessary question mark to the end of the URL, but it's better than form resubmit prompt
    form.method = 'get';
}


//Prevent form submit on Enter, if action is empty (otherwise this causes page reload with additional question mark in address
function formEnter(event: KeyboardEvent): void | boolean
{
    let form = (event.target as HTMLInputElement).form as HTMLFormElement;
    if ((event.code === 'Enter' || event.code === 'NumpadEnter') && (!form.action || !(form.getAttribute('data-baseURL') && location.protocol + '//' + location.host+form.getAttribute('data-baseURL') !== form.action))) {
        event.stopPropagation();
        event.preventDefault();
        return false;
    }
}

//Track backspace and focus previous input field, if input is empty, when it's pressed
function inputBackSpace(event: Event): void
{
    let current = event.target as HTMLInputElement;
    if ((event as KeyboardEvent).code === 'Backspace' && !current.value) {
        let moveTo = nextInput(current, true) as HTMLInputElement;
        if (moveTo) {
            moveTo.focus();
            //Ensure, that cursor ends up at the end of the previous field
            moveTo.selectionStart = moveTo.selectionEnd = moveTo.value.length;
        }
    }
}

//Focus next field, if current is filled to the brim and valid
function autoNext(event: Event): void
{
    let current = event.target as HTMLInputElement;
    //Get length attribute
    let maxLength = parseInt(current.getAttribute('maxlength') ?? '0');
    //Check it against value length
    if (maxLength && current.value.length === maxLength && current.validity.valid) {
        let moveTo = nextInput(current, false) as HTMLInputElement;
        if (moveTo) {
            moveTo.focus();
        }
    }
}

async function pasteSplit(event: Event): Promise<void>
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
                current = nextInput(current, false) as HTMLInputElement;
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

//Find next/previous input
function nextInput(initial: HTMLInputElement, reverse: boolean = false): HTMLInputElement | boolean
{
    //Get form
    let form = initial.form;
    //Iterate inputs inside the form. Not using previousElementSibling, because next/previous input may not be a sibling on the same level
    if (form) {
        let previous;
        for (let moveTo of form.querySelectorAll('input')) {
            if (reverse) {
                //Check if current element in loop is the initial one, meaning
                if (moveTo === initial) {
                    //If previous is not empty - share it. Otherwise - false, since initial input is first in the form
                    if (previous) {
                        return previous;
                    } else {
                        return false;
                    }
                }
            } else {
                //If we are moving forward and initial node is the previous one
                if (previous && previous === initial) {
                    return moveTo;
                }
            }
            //Update previous input
            previous = moveTo;
        }
    }
    return false;
}
