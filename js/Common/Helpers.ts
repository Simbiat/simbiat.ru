async function ajax(url: string, formData: FormData | null = null, type ='json', method = 'GET', timeout = 60000, skipError = false): Promise<any>
{
    let result;
    let controller = new AbortController();
    setTimeout(() => controller.abort(), timeout);
    try {
        let response = await fetch(url, {
            method: method,
            mode: 'same-origin',
            //Cache is allowed, but essentially, only if stale. While this may put some extra stress on server, for API it's better this way
            cache: 'no-cache',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-Token': getMeta('X-CSRF-Token') ?? '',
            },
            //Do not follow redirects. If redirected - something is wrong on API level
            redirect: 'error',
            referrer: window.location.href,
            referrerPolicy: 'same-origin',
            //integrity: '', useful if we know expected hash of the response
            keepalive: false,
            signal: controller.signal,
            body: ['POST', 'PUT', 'DELETE', 'PATCH',].includes(method) ? formData : null,
        });
        if (!response.ok && !skipError) {
            new Snackbar('Request to "'+url+'" returned code '+response.status, 'failure', 10000);
            return false;
        } else {
            if (type === 'json') {
                result = await response.json();
            } else if (type === 'blob') {
                result = await response.blob();
            } else if (type === 'array') {
                result = await response.arrayBuffer();
            } else if (type === 'form') {
                result = await response.formData();
            } else {
                result = await response.text();
            }
        }
        return result;
    } catch(err: any) {
        if (err.name === 'AbortError') {
            new Snackbar('Request to "'+url+'" timed out after '+timeout+' milliseconds', 'failure', 10000);
        } else {
            new Snackbar('Request to "'+url+'" failed on fetch operation', 'failure', 10000);
        }
    }
}

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
