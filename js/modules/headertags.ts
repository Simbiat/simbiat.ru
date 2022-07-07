//Add ID attribute to header tags, if it's missing
function idToHeader(hTag: HTMLHeadingElement) {
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
}

//Copy anchor to the header tag on click
function anchorFromHeader(event: Event) {
    //Generate and copy anchor link to clipboard
    navigator.clipboard.writeText(window.location.href.replaceAll(/(^[^#]*)(#.*)?$/gmu, `$1`) + '#' + (event.target as HTMLHeadingElement).getAttribute('id')).then(function() {
        new Snackbar().add('Anchor link for "' + (event.target as HTMLHeadingElement).textContent + '" copied to clipboard', 'success');
    }, function() {
        new Snackbar().add('Failed to copy anchor link for "' + (event.target as HTMLHeadingElement).textContent + '"','failure');
    });
}
