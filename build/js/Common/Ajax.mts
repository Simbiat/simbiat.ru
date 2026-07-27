/**
 * @file Class to handle HTTP requests (usually from forms).
 */
import { ACCESS_TOKEN, TIMEZONE } from './Constants.mts';
import { empty, getMeta } from './Helpers.mts';
import { Snackbar, SNACKBAR_FAIL_TIMEOUT } from './Snackbar.mts';

/**
 * Interface for common JSON responses from API endpoints. Doing this for the sake of strong typing.
 */
interface AjaxJSONResponse extends JSON {
  /**
   * HTTP status of the request.
   */
  status: number
  /**
   * Actual server response.
   */
  data: boolean | number | string | null
  /**
   * URL associated with the data in `data`.
   */
  location: string
  /**
   * Reason for the error, as provided by the server.
   */
  reason: string
  /**
   * CSRF token.
   */
  csrf: string
}

/**
 * Interface for Ajax requests.
 */
interface AjaxRequest {
  /**
   * URL to use in the request.
   */
  url: string
  /**
   * Optional form data.
   */
  form_data?: FormData | null
  /**
   * HTTP Method to use for the request.
   */
  method?: string
  /**
   * Optional `button` or `input` element to disable and replace with a spinner during request processing.
   */
  button?: HTMLButtonElement | HTMLInputElement | null
  /**
   * Optional function to call on successful processing of an HTTP request.
   */
  onSuccess?: (response: AjaxJSONResponse) => Promise<void> | void
  /**
   * Optional function to call on failed processing of an HTTP request.
   */
  onError?: () => Promise<void> | void
  /**
   * Flag to indicate that `button` needs to be kept disabled in case of successful processing of an HTTP request.
   */
  keep_disabled?: boolean
  /**
   * Flag to indicate that `button` needs to be kept disabled in case of successful processing of an HTTP request and the `data` node of the response being `true`.
   */
  require_data_true?: boolean
}

/**
 * Class to handle HTTP requests (usually from forms).
 */
export class Ajax {
  private static readonly AJAX_TIMEOUT = 90000;

  /**
   * Issue an AJAX HTTP request.
   * @param url_string - URL to process.
   * @param form_data - Optional form data.
   * @param method - Method to use. Default is GET.
   */
  private static async fetchJson(
    url_string: string,
    form_data: FormData | null = null,
    method = 'GET',
  ): Promise<AjaxJSONResponse | false> {
    const is_bot = !empty(getMeta('is_bot'));
    if (is_bot) {
      void new Snackbar('No Ajax calls are allowed for bots', 'error');
      return false;
    }
    let result;
    const controller = new AbortController();
    const abort_timer = window.setTimeout(() => {
      controller.abort();
    }, this.AJAX_TIMEOUT);
    // Add an access token to the URL, if present
    let url;
    if (!empty(ACCESS_TOKEN) && url_string.startsWith(`${location.protocol}//${location.host}`)) {
      const url_obj = new URL(url_string, window.location.origin);
      url_obj.searchParams.set('access_token', `${ACCESS_TOKEN}`);
      url = url_obj.toString();
    } else {
      url = url_string;
    }
    try {
      const response = await fetch(url, {
        body: ['POST', 'PUT', 'DELETE', 'PATCH'].includes(method) ? form_data : null,
        //Cache is allowed, but essentially, only if stale. While this may put some extra stress on the server, for API it's better this way
        cache: 'no-cache',
        credentials: 'same-origin',
        headers: {
          'X-CSRF-Token': getMeta('X-CSRF-Token') ?? '',
          'X-Client-Timezone': TIMEZONE,
        },
        keepalive: false,
        method,
        mode: 'same-origin',
        //Do not follow redirects. If redirected, something is wrong on the API level
        redirect: 'error',
        referrer: window.location.href,
        referrerPolicy: 'same-origin',
        //integrity: '', useful if we know the expected hash of the response
        signal: controller.signal,
      });
      try {
        result = await response.json() as AjaxJSONResponse;
      } catch {
        if (!response.ok) {
          void new Snackbar(`Request to "${url}" returned code ${response.status}`, 'failure', SNACKBAR_FAIL_TIMEOUT);
          return false;
        }
        void new Snackbar(`Request to "${url}" failed to decode JSON request`, 'failure', SNACKBAR_FAIL_TIMEOUT);
        return false;
      }
      if (result.csrf) {
        document.querySelector<HTMLMetaElement>('meta[name="X-CSRF-Token"]')
                ?.setAttribute('content', result.csrf);
      }
      return result;
    } catch (err) {
      if (err instanceof DOMException && err.name === 'AbortError') {
        void new Snackbar(`Request to "${url}" timed out after ${this.AJAX_TIMEOUT} milliseconds`, 'failure', SNACKBAR_FAIL_TIMEOUT);
      } else {
        void new Snackbar(`Request to "${url}" failed on fetch operation`, 'failure', SNACKBAR_FAIL_TIMEOUT);
      }
      return false;
    } finally {
      window.clearTimeout(abort_timer);
    }
  }

  // noinspection ParameterNamingConventionJS (this is a function, but PHPStorm falsely considers it a parameter)
  /**
   * Submit and process AJAX request.
   */
  public static async request({
                                url,
                                form_data = null,
                                method = 'GET',
                                button = null,
                                onSuccess,
                                onError,
                                keep_disabled = false,
                                require_data_true = false,
                              }: AjaxRequest): Promise<AjaxJSONResponse | false> {
    let to_replace = null;
    let replace_with = null;
    let spinner = null;
    if (button) {
      if (button instanceof HTMLButtonElement) {
        // Use Array.from to actually "copy" contents and not to reference live node list
        to_replace = Array.from(button.childNodes);
      } else {
        to_replace = button;
      }
      const template = document.querySelector<HTMLTemplateElement>('#spinner');
      if (template) {
        replace_with = template.content.cloneNode(true) as DocumentFragment;
        const alt_value = button.getAttribute('data-alt-attribute') ?? '';
        spinner = replace_with.querySelector<HTMLImageElement>('img');
        if (spinner && alt_value !== '') {
          spinner.alt = alt_value;
        }
      }
    }
    let response: AjaxJSONResponse | false = false;
    try {
      if (button) {
        button.disabled = true;
        if (replace_with) {
          if (button instanceof HTMLButtonElement) {
            button.replaceChildren(replace_with);
          } else {
            button.replaceWith(replace_with);
          }
        }
      }
      response = await this.fetchJson(url, form_data, method);
      if (response === false) {
        await onError?.();
      } else {
        // Generic error processing if there is a `reason`. If there is `location` - return the full response and let the calling function handle redirection, if required.
        if ((response.data === false || response.data === null || typeof response.data === 'undefined') && response.reason && !response.location) {
          void new Snackbar(response.reason, 'failure', SNACKBAR_FAIL_TIMEOUT);
          return false;
        }
        await onSuccess?.(response);
        return response;
      }
      return false;
    } finally {
      // Enable button back again if `keep_disabled` is `false` or if it is `true` and `require_data_true` are `response` false or if `require_data_true` is `true` and either `response` is `false` or `response.data` is not `true`
      if (button
        && (
          !keep_disabled
          || (
            keep_disabled
            && (
              (
                require_data_true
                && (
                  response === false
                  || response.data !== true
                )
              )
              || (
                !require_data_true
                && response === false
              )
            )
          )
        )
      ) {
        button.disabled = false;
        if (to_replace) {
          if (button instanceof HTMLButtonElement && !(to_replace instanceof Node)) {
            button.replaceChildren(...to_replace);
          } else if (spinner && to_replace instanceof Node) {
            spinner.replaceWith(to_replace);
          }
        }
      }
    }
  }
}
