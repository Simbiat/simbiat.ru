//Remove cacheReset flag
function cleanGET(): void
{
    let url = new URL(document.location.href);
    let params = new URLSearchParams(url.search);
    params.delete('cacheReset');
    if (params.toString() === '') {
        window.history.replaceState(document.title, document.title, location.pathname + location.hash);
    } else {
        window.history.replaceState(document.title, document.title, '?' + params + location.hash);
    }
}

//Special processing for special hash links
function hashCheck(): void
{
    let url = new URL(document.location.href);
    let hash = url.hash;
    let Gallery = document.getElementsByTagName('gallery-overlay')[0] as Gallery;
    const galleryLink = new RegExp('#gallery=\\d+', 'ui');
    if (galleryLink.test(hash)) {
        let imageID = Number(hash.replace(/(#gallery=)(\d+)/ui, '$2'));
        if (imageID) {
            if (Gallery.images[imageID - 1]) {
                Gallery.current = imageID - 1;
            } else {
                new Snackbar('Image number '+imageID+' not found on page', 'failure');
                window.history.replaceState(document.title, document.title, document.location.href.replace(hash, ''));
            }
        }
    } else {
        Gallery.close();
    }
}

//Function to initialize page-specific code
function router(): void
{
    let url = new URL(document.location.href);
    let path = url.pathname.replace(/(\/)(.*)(\/)/ui, '$2').toLowerCase().split('/');
    if (path[0]) {
        if (path[0] === 'bictracker') {
            if (path[1]) {
                if (path[1] === 'keying') {
                    new bicKeying();
                } else if (path[1] === 'search') {
                    new bicRefresh();
                }
            }
        } else if (path[0] === 'fftracker') {
            if (path[1] && path[1] === 'track') {
                new ffTrack();
            }
        } else if (path[0] === 'uc') {
            if (path[1]) {
                if (path[1] === 'emails') {
                    new Emails();
                } else if (path[1] === 'password') {
                    new PasswordChange();
                }
            }
        }
    }
}
