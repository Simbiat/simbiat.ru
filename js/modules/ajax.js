/*globals addSnackbar, getMeta*/
/*exported ajax*/

async function ajax(url, request = null, type ='json', method = 'GET', timeout = 60000, skipError = false)
{
    let result;
    let controller = new AbortController();// jshint ignore:line
    setTimeout(() => controller.abort(), timeout);
    //Generate POST data if request is present
    let formData = new FormData();
    if (request && ['POST', 'PUT', 'DELETE', 'PATCH',].includes(method) > -1) {
        if (request instanceof FormData) {
            formData = request;
        } else {
            Object.entries(request).forEach(([key, value]) => {
                formData.append(key, value);
            });
        }
    }
    //Wrapping in try to allow timeout
    try {
        let response = await fetch(url, {
            method: method,
            mode: 'same-origin',
            //Cache is allowed, but essentially, only if stale. While this may put some extra stress on server, for API it's better this way
            cache: 'no-cache',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-Token': getMeta('X-CSRF-Token'),
            },
            //Do not follow redirects. If redirected - something is wrong on API level
            redirect: 'error',
            referrer: window.location.href,
            referrerPolicy: 'same-origin',
            //integrity: '', useful if we know expected hash of the response
            keepalive: false,
            signal: controller.signal,
            body: ['POST', 'PUT', 'DELETE', 'PATCH',].includes(method) > -1 ? formData : null,
        });
        if (!response.ok && !skipError) {
            addSnackbar('Request to "'+url+'" returned code '+response.status, 'failure', 10000);
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
    } catch(err) {
        if (err.name === 'AbortError') {
            addSnackbar('Request to "'+url+'" timed out after '+timeout+' milliseconds', 'failure', 10000);
            return false;
        } else {
            addSnackbar('Request to "'+url+'" failed on fetch operation', 'failure', 10000);
            return false;
        }
    }
}
