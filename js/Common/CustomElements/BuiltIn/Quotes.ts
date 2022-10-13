class Quotes
{
    private static _instance: null | Quotes = null;

    constructor()
    {
        if (Quotes._instance) {
            return Quotes._instance;
        }
        //Add a visual button for sample, code and blockquote tags
        document.querySelectorAll('samp, code, blockquote').forEach(item => {
            //Modifying innerHTML instead of insertBefore, since block may not have any actual children in the first place, and as per https://developer.mozilla.org/en-US/docs/Web/API/Node/insertBefore
            //"When the element does not have a first child, then firstChild is null. The element is still appended to the parent, after the last child."
            //This results in same effect as with appendChild, that the image is inserted at the end, which is not what we want
            item.innerHTML = '<img loading="lazy" decoding="async"  src="/img/copy.svg" alt="Click to copy block" class="copyQuote">' + item.innerHTML;
        });
        //Add author
        document.querySelectorAll('blockquote[data-author]').forEach(item => {
            item.innerHTML = '<span class="quoteAuthor">'+item.getAttribute('data-author')+':</span>' + item.innerHTML;
        });
        //Add description
        document.querySelectorAll('samp[data-description], code[data-description]').forEach(item => {
            item.innerHTML = '<span class="codeDesc">'+item.getAttribute('data-description')+':</span>' + item.innerHTML;
        });
        //q tag is inline and a visual button does not suit it, so we add tooltip to it
        Array.from(document.getElementsByTagName('q')).forEach(item => {
            item.setAttribute('data-tooltip', 'Click to copy quote');
        });
        document.querySelectorAll('.copyQuote, q').forEach(item => {
            item.addEventListener('click', (event: Event) => {this.copy(event.target as HTMLElement)});
        });
        Quotes._instance = this;
    }

    //Copy the text of q tag or respective block
    public copy(node: HTMLElement): string
    {
        //Get parent node, if click was on the copy picture/button
        if (node.tagName.toLowerCase() !== 'q') {
            node = node.parentElement as HTMLElement;
        }
        let tag: string;
        switch (node.tagName.toLowerCase()) {
            case 'samp':
                tag = 'Sample';
                break;
            case 'code':
                tag = 'Code';
                break;
            case 'blockquote':
            case 'q':
                tag = 'Quote';
                break;
        }
        //Set text
        let quoteText = String(node.textContent);
        //Get author for blockquotes
        if (node.tagName.toLowerCase() === 'blockquote' && node.hasAttribute('data-author')) {
            let authorMatch = new RegExp('^('+node.getAttribute('data-author')+':)', 'ui');
            quoteText = quoteText.replace(authorMatch,'');
        }
        navigator.clipboard.writeText(quoteText).then(function() {
            new Snackbar(tag + ' copied to clipboard', 'success');
        }, function() {
            new Snackbar('Failed to copy '+tag.toLowerCase(),'failure');
        });
        return String(node.textContent);
    }
}
