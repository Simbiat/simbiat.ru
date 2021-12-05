/*globals addSnackbar*/
/*exported copyQuoteInit, placeholders, detailsInit*/
/*These are functions, that are used to somehow style or standardise different elements*/

function copyQuoteInit()
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
function copyQuote(event)
{
    let node;
    if (event.target.tagName.toLowerCase() === 'q') {
        node = event.target;
    } else {
        node = event.target.parentElement;
    }
    let tag;
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
    navigator.clipboard.writeText(node.textContent).then(function() {
        addSnackbar(tag.charAt(0).toUpperCase() + tag.slice(1) + ' copied to clipboard', 'success');
    }, function() {
        addSnackbar('Failed to copy '+tag,'failure');
    });
}

function placeholders()
{
    //Enforce placeholder for textarea similar to text inputs
    Array.from(document.getElementsByTagName('textarea')).forEach(item => {
        if (item.hasAttribute('placeholder') === false) {
            item.setAttribute('placeholder', item.value || item.type || 'placeholder');
        }
    });
}

//Function to handle details elements
function detailsInit()
{
    //Close all details except currently selected one
    document.querySelectorAll('details').forEach((details,_,list)=>{
        details.ontoggle =_=> { // jshint ignore:line
            if(details.open && details.classList.contains('persistent') === false) {
                list.forEach(tag =>{
                    if(tag !== details && tag.classList.contains('persistent') === false) {
                        tag.open=false;
                    }
                });
            }
        };
    });
    window.addEventListener('click', function(event){
        document.querySelectorAll('details').forEach((details)=>{
            if(details.classList.contains('popup') === true && details.contains(event.target) === false) {
                details.open=false;
            }
        });
    });
}
