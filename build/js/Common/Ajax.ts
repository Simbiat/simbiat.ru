import { AJAX_TIMEOUT, SNACKBAR_FAIL_LIFE, ACCESS_TOKEN } from 'Common/Constants.ts';
import { addSnackbar, empty, getMeta } from './Helpers.ts';

// Interface for common JSON responses from API endpoints. Doing this for the sake of strong typing
export interface AjaxJSONResponse extends JSON {
  status: number
  data: boolean | number | string
  location: string
  reason: string
  csrf: string
}

// noinspection OverlyComplexFunctionJS
export async function ajax(
  url: string,
  form_data: FormData | null = null,
  type = 'json',
  method = 'GET',
  timeout = AJAX_TIMEOUT,
  skip_error = false,
): Promise<AjaxJSONResponse | ArrayBuffer | Blob | FormData | boolean | string> {
  const is_bot = !empty(getMeta('is_bot'));
  if (is_bot) {
    addSnackbar('No Ajax calls are allowed for bots', 'error');
    return false;
  }
  let result;
  const controller = new AbortController();
  window.setTimeout(() => {
    controller.abort();
  }, timeout);
  // Add an access token to the URL, if present
  if (!empty(ACCESS_TOKEN) && url.startsWith(`${location.protocol}//${location.host}`)) {
    const url_obj = new URL(url, window.location.origin);
    url_obj.searchParams.set('access_token', `${ACCESS_TOKEN}`);
    url = url_obj.toString();
  }
  try {
    const response = await fetch(url, {
      body: ['POST', 'PUT', 'DELETE', 'PATCH'].includes(method) ? form_data : null,
      //Cache is allowed, but essentially, only if stale. While this may put some extra stress on the server, for API it's better this way
      cache: 'no-cache',
      credentials: 'same-origin',
      headers: {
        'X-CSRF-Token': getMeta('X-CSRF-Token') ?? '',
      },
      keepalive: false,
      method,
      mode: 'same-origin',
      //Do not follow redirects. If redirected - something is wrong on the API level
      redirect: 'error',
      referrer: window.location.href,
      referrerPolicy: 'same-origin',
      //integrity: '', useful if we know the expected hash of the response
      signal: controller.signal,
    });
    if (!response.ok && !skip_error) {
      addSnackbar(`Request to "${url}" returned code ${response.status}`, 'failure', SNACKBAR_FAIL_LIFE);
      return false;
    }
    switch (type) {
      case 'json':
        result = await response.json() as AjaxJSONResponse;
        if (result.csrf) {
          document.querySelector('meta[name="X-CSRF-Token"]')
                  ?.setAttribute('content', result.csrf);
        }
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
      addSnackbar(`Request to "${url}" timed out after ${timeout} milliseconds`, 'failure', SNACKBAR_FAIL_LIFE);
    } else {
      addSnackbar(`Request to "${url}" failed on fetch operation`, 'failure', SNACKBAR_FAIL_LIFE);
    }
    return false;
  }
}
