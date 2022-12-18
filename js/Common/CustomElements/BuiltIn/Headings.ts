class Headings
{
    private static _instance: null | Headings = null;

    constructor()
    {
        if (Headings._instance) {
            return Headings._instance;
        }
        //Add IDs to H1-H6 tags and handle onclick events to copy anchor links, except for page title (since it's at the top)
        document.querySelectorAll('h1:not(#h1title), h2, h3, h4, h5, h6').forEach(hTag => {
            //Add ID attribute to header tags, if it's missing (needed for unique anchor links)
            if (!hTag.hasAttribute('id')) {
                //Get initial ID
                let id = String(hTag.textContent).replaceAll(/\s/gmu, `_`).replaceAll(/[^\p{L}\p{N}_\-]/gmu, ``).replaceAll(/(^.{1,64})(.*$)/gmu, `$1`);
                //Get ID index, in case it's already used
                let index = 1;
                let altId = id;
                //Check if altID exists
                while (document.getElementById(altId)) {
                    //Increase index
                    index++;
                    altId = id + '_' + index;
                }
                hTag.setAttribute('id', altId);
            }
            hTag.addEventListener('click', (event: Event) => {this.copyLink(event.target as HTMLHeadingElement) });
        });
        Headings._instance = this;
    }

    //Copy anchor to the header tag on click
    private copyLink(target: HTMLHeadingElement): string
    {
        //Checking for selection. If it's present most likely the text in anchor is being selected with intention of copying it.
        //In this case, if we copy the anchor link, we may provide undesired effect (although ctrl+c will most likely fire after this).
        if ((window.getSelection() as Selection).type !== 'Range') {
            //Generate anchor link
            let link = window.location.href.replaceAll(/(^[^#]*)(#.*)?$/gmu, `$1`) + '#' + (target as HTMLHeadingElement).getAttribute('id');
            // Copy anchor link to clipboard
            navigator.clipboard.writeText(link).then(function() {
                new Snackbar('Anchor link for "' + (target as HTMLHeadingElement).textContent + '" copied to clipboard', 'success');
            }, function() {
                new Snackbar('Failed to copy anchor link for "' + (target as HTMLHeadingElement).textContent + '"','failure');
            });
            return link;
        } else {
            return '';
        }
    }
}
