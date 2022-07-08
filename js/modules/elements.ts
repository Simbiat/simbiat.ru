/*These are functions, that are used to somehow style or standardise different elements*/

function copyQuoteInit(): void
{
    document.querySelectorAll('samp, code, blockquote').forEach(item => {
        //Modifying innerHTML instead of insertBefore, since block may not have any actual children in the first place, and as per https://developer.mozilla.org/en-US/docs/Web/API/Node/insertBefore
        //"When the element does not have a first child, then firstChild is null. The element is still appended to the parent, after the last child."
        //This results in same effect as with appendChild, that the image is inserted at the end, which is not what we want
        item.innerHTML = '<img loading="lazy" decoding="async"  src="/img/copy.svg" alt="Copy block" class="copyQuote">' + item.innerHTML;
    });
    document.querySelectorAll('.copyQuote, q').forEach(item => {
        item.addEventListener('click', copyQuote);
    });
}
function copyQuote(event: Event):void
{
    let node = event.target as HTMLElement;
    if (node.tagName.toLowerCase() !== 'q') {
        node = node.parentElement as HTMLElement;
    }
    let tag: string;
    switch (node.tagName.toLowerCase()) {
        case 'samp':
            tag = 'sample';
            break;
        case 'code':
            tag = 'code';
            break;
        case 'blockquote':
        case 'q':
            tag = 'quote';
            break;
    }
    navigator.clipboard.writeText(String(node.textContent)).then(function() {
        new Snackbar(tag.charAt(0).toUpperCase() + tag.slice(1) + ' copied to clipboard', 'success');
    }, function() {
        new Snackbar('Failed to copy '+tag,'failure');
    });
}

function placeholders(): void
{
    //Enforce placeholder for textarea similar to text inputs
    Array.from(document.getElementsByTagName('textarea')).forEach(item => {
        if (!item.hasAttribute('placeholder')) {
            item.setAttribute('placeholder', item.value || item.type || 'placeholder');
        }
    });
}

//Function to handle details elements
function detailsInit(): void
{
    //Close all details except currently selected one
    document.querySelectorAll('details').forEach((details,_,list)=>{
        details.ontoggle =_=> {
            if(details.open && !details.classList.contains('persistent')) {
                list.forEach(tag =>{
                    if(tag !== details && !tag.classList.contains('persistent')) {
                        tag.open=false;
                    }
                });
            }
        };
    });
    window.addEventListener('click', function(event: MouseEvent){
        document.querySelectorAll('details').forEach((details: HTMLDetailsElement)=>{
            if(details.classList.contains('popup') && !details.contains(event.target as HTMLElement)) {
                details.open=false;
            }
        });
    });
}
