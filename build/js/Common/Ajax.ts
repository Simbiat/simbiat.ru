//Interface for common JSON responses from API endpoints. Doing this for the sake of strong typing
interface ajaxJSONResponse extends JSON {
    status: number;
    data: boolean | number | string;
    location: string;
    reason: string;
}

async function ajax(
    url: string, formData: FormData | null = null,
    type = 'json', method = 'GET',
    timeout = 60000, skipError = false
): Promise<ajaxJSONResponse | ArrayBuffer | Blob | FormData | boolean | string>
{
    let result;
    const controller = new AbortController();
    window.setTimeout(() => { controller.abort(); }, timeout);
    try {
        const response = await fetch(url, {
            'body': ['POST', 'PUT', 'DELETE', 'PATCH',].includes(method) ? formData : null,
            //Cache is allowed, but essentially, only if stale. While this may put some extra stress on server, for API it's better this way
            'cache': 'no-cache',
            'credentials': 'same-origin',
            'headers': {
                'X-CSRF-Token': getMeta('X-CSRF-Token') ?? '',
            },
            'keepalive': false,
            method,
            'mode': 'same-origin',
            //Do not follow redirects. If redirected - something is wrong on API level
            'redirect': 'error',
            'referrer': window.location.href,
            'referrerPolicy': 'same-origin',
            //integrity: '', useful if we know expected hash of the response
            'signal': controller.signal,
        });
        if (!response.ok && !skipError) {
            addSnackbar(`Request to "${url}" returned code ${response.status}`, 'failure', 10000);
            return false;
        }
        if (type === 'json') {
            result = await response.json() as ajaxJSONResponse;
        } else if (type === 'blob') {
            result = await response.blob();
        } else if (type === 'array') {
            result = await response.arrayBuffer();
        } else if (type === 'form') {
            result = await response.formData();
        } else {
            result = await response.text();
        }
        return result;
    } catch(err) {
        if (err instanceof DOMException && err.name === 'AbortError') {
            addSnackbar(`Request to "${url}" timed out after ${timeout} milliseconds`, 'failure', 10000);
        } else {
            addSnackbar(`Request to "${url}" failed on fetch operation`, 'failure', 10000);
        }
        return false;
    }
}
