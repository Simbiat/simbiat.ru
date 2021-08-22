/*exported idToHeader, anchorFromHeader*/
/*globals addSnackbar*/

//Add ID attribute to header tags, if it's missing
function idToHeader(hTag) {
    if (hTag.hasAttribute('id') === false) {
        hTag.setAttribute('id', hTag.textContent.replaceAll(/\s/gmu, `_`).replaceAll(/[^\p{L}\p{N}_\-]/gmu, ``).replaceAll(/(^.{1,64})(.*$)/gmu, `$1`));
    }
}

//Copy anchor to the header tag on click
function anchorFromHeader(event) {
    //Generate and copy anchor link to clipboard
    navigator.clipboard.writeText(window.location.href.replaceAll(/(^[^#]*)(#.*)?$/gmu, `$1`) + '#' + event.target.getAttribute('id')).then(function() {
        addSnackbar('Anchor link for "' + event.target.textContent + '" copied to clipboard', 'success', 0);
    }, function() {
        addSnackbar('Failed to copy anchor link for "' + event.target.textContent + '"','failure');
    });
}
