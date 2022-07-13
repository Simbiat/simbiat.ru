//Get meta content
function getMeta(metaName: string): string|null {
    const metas = Array.from(document.getElementsByTagName('meta'));
    let tag = metas.find(obj => {
        return obj.name === metaName
    })
    if (tag) {
        return tag.getAttribute('content');
    } else {
        return null;
    }
}

//Update document title and push to history. Required, since browsers mostly ignore title argument in pushState
function updateHistory(newUrl: string, title: string): void
{
    document.title = title;
    window.history.pushState(title, title, newUrl);
}

//Remove cacheReset flag
function cleanGET()
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
function hashCheck()
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

//Function replicating PHP's rawurlencode for consistency.
function rawurlencode(str: string): string
{
    str = str + '';
    return encodeURIComponent(str)
        .replace(/!/ug, '%21')
        .replace(/'/ug, '%27')
        .replace(/\(/ug, '%28')
        .replace(/\)/ug, '%29')
        .replace(/\*/ug, '%2A');
}
