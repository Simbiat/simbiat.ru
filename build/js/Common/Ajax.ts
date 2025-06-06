//Interface for common JSON responses from API endpoints. Doing this for the sake of strong typing
interface ajaxJSONResponse extends JSON
{
    status: number;
    data: boolean | number | string;
    location: string;
    reason: string;
}

// noinspection OverlyComplexFunctionJS
async function ajax(
    url: string,
    formData: FormData | null = null,
    type = 'json',
    method = 'GET',
    timeout = ajaxTimeout,
    skipError = false
): Promise<ajaxJSONResponse | ArrayBuffer | Blob | FormData | boolean | string>
{
    let result;
    const controller = new AbortController();
    window.setTimeout(() => {
        controller.abort();
    }, timeout);
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
            addSnackbar(`Request to "${url}" returned code ${response.status}`, 'failure', snackbarFailLife);
            return false;
        }
        switch (type) {
            case 'json':
                result = await response.json() as ajaxJSONResponse;
                break;
            case 'blob':
                result = await response.blob();
                break;
            case 'array':
                result = await response.arrayBuffer();
                break;
            case 'form':
                result = await response.formData();
                break;
            default:
                result = await response.text();
                break;
        }
        return result;
    } catch (err) {
        if (err instanceof DOMException && err.name === 'AbortError') {
            addSnackbar(`Request to "${url}" timed out after ${timeout} milliseconds`, 'failure', snackbarFailLife);
        } else {
            addSnackbar(`Request to "${url}" failed on fetch operation`, 'failure', snackbarFailLife);
        }
        return false;
    }
}
