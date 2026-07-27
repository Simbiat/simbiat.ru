/**
 * @file Various helper functions.
 */
import type { GalleryOverlay } from '../CustomElements/GalleryOverlay.mts';
import type { TabMenu } from '../CustomElements/TabMenu.mts';
import { Input } from '../NativeElements/Input.mts';
import shared_with_php from '../shared_with_php.json';
import { Snackbar } from './Snackbar.mts';

/**
 * Get value of a `meta` tag.
 * @param meta_name - Name of the meta tag.
 */
export function getMeta(meta_name: string): string | null {
  return document.querySelector<HTMLMetaElement>(`meta[name="${meta_name}"]`)
                 ?.getAttribute('content') ?? null;
}

/**
 * Update the document title and push to history. Required, since browsers mostly ignore title argument in pushState.
 * @param new_url - New URL to push to history.
 * @param title - Title to use for the URL.
 */
export function updateHistory(new_url: string, title: string): void {
  //Update title and/or URL only if there were changes
  if (document.title !== title) {
    document.title = title;
  }
  if (document.location.href !== new_url) {
    window.history.pushState(title, title, new_url);
  }
}

/**
 * Function to replicate PHP's `empty()`.
 * @param variable - Variable to check.
 */
export function empty(variable: unknown): boolean {
  if (typeof variable === 'undefined' || variable === null || variable === false || variable === 0 || variable === 'NaN') {
    return true;
  }
  if (typeof variable === 'number' && isNaN(variable)) {
    return true;
  }
  if (typeof variable === 'string') {
    return (/^[\s\p{C}]*$/v).test(variable);
  }
  if (Array.isArray(variable) || variable instanceof NodeList || variable instanceof HTMLCollection) {
    return variable.length === 0;
  }
  if (typeof variable === 'object') {
    return JSON.stringify(variable) === '{}';
  }
  return false;
}

/**
 * Remove a table row based on containing the element.
 * @param element - HTML element to use to find the closest "tr" element to remove.
 */
export function deleteRow(element: HTMLElement): boolean {
  const table = element.closest<HTMLTableElement>('table');
  //Get row number
  const tr = element.closest<HTMLTableRowElement>('tr');
  if (table && tr) {
    table.deleteRow(tr.rowIndex);
    return true;
  }
  return false;
}

/**
 * Simulation of the `basename()` function to return only the name of the file (without path or extension).
 * @param path - Path to get the filename from.
 */
export function basename(path: string): string {
  return path.replace(/^.*\/|\.[^.]*$/gv, '');
}

/**
 * Function to force page refresh. Regular `reload()` often hits the cache, thus not properly updating.
 * @param new_url - URL to load.
 */
export function pageRefresh(new_url?: string): void {
  let url;
  if (empty(new_url)) {
    url = new URL(document.location.href, window.location.origin);
  } else {
    window.location.assign(encodeURI(new_url ?? ''));
    url = new URL(new_url ?? '', document.location.href);
  }
  url.searchParams.set('force_reload', String(Date.now()));
  window.location.replace(url.toString());
}

/**
 * Remove certain GET parameters to avoid them being saved to favourites or shared.
 */
export function cleanGET(): void {
  const url = new URL(document.location.href, window.location.origin);
  //Flag for resetting cache on the server side
  url.searchParams.delete('cache_reset');
  //Flag used to attempt to force proper reload (with cache clear) of a page.
  url.searchParams.delete('force_reload');
  //Access token needs to be removed to minimize the potential for token leak
  url.searchParams.delete('access_token');
  //window.location.reload seems to always hit the browser cache, which results in, for example, a page showing you as logged in/out, when in fact it's the reverse.
  //Thus, I am using direct window.location.replace with a flag instead of reload, but we need to clear the flag itself
  window.history.replaceState(document.title, document.title, url.toString());
}

/**
 * Remove forbidden parameters from a URL string.
 * @param url - URL to process.
 */
export function urlCleanString(url: string): string {
  const params_to_delete = shared_with_php.tracking_query_parameters;
  const url_new = new URL(url, window.location.origin);
  for (const param of params_to_delete) {
    url_new.searchParams.delete(param);
  }
  return decodeURI(url_new.toString());
}

/**
 * URL decode pasted links and strip some common marketing GET parameters.
 * @param event - ClipboardEvent to process.
 */
export function urlClean(event: ClipboardEvent): void {
  const original_string = event.clipboardData?.getData('text/plain');
  event.preventDefault();
  event.stopImmediatePropagation();
  const current = event.target;
  if (current === null) {
    //If somehow we got here, exit early
    return;
  }
  //Update the value
  Input.pasteAndMove((current as HTMLInputElement), urlCleanString(original_string ?? ''));
  current.dispatchEvent(new Event('input', {
    bubbles: true,
    cancelable: true,
  }));
}

/**
 * Processing for special hash links.
 */
export function hashCheck(): void {
  const url = new URL(document.location.href, window.location.origin);
  const hash = url.hash;
  const gallery = document.querySelector<GalleryOverlay>('gallery-overlay');
  const gallery_link = /#gallery=\d+/iv;
  const tab_name = /#tab_name_.+/iv;
  if (gallery) {
    if (gallery_link.test(hash)) {
      const image_id = Number(hash.replace(/^#gallery=/iv, ''));
      if (image_id) {
        if (gallery.images[image_id - 1]) {
          gallery.current = image_id - 1;
        } else {
          void new Snackbar(`Image number ${image_id} not found on page`, 'failure');
          window.history.replaceState(document.title, document.title, document.location.href.replace(hash, ''));
        }
      }
    } else {
      gallery.close();
    }
  }
  if (tab_name.test(hash)) {
    const tab_name_id = hash.replace(/^#(?=tab_name_)/iv, '');
    const tab_element = document.querySelector<HTMLElement>(`#${tab_name_id}`);
    if (tab_element?.tagName.toLowerCase() === 'a') {
      //Open respective tab
      const tab_menu = tab_element.parentElement;
      if (tab_menu?.tagName.toLowerCase() === 'tab-menu') {
        (tab_menu as TabMenu).tabSwitch(tab_element as HTMLAnchorElement);
      }
    } else {
      url.hash = '';
      window.history.replaceState(document.title, document.title, url.toString());
    }
  }
}

/**
 * Get value of a GET parameter from current URL.
 * @param parameter - Parameter name.
 */
export function getSearchParam(parameter: string): string {
  const query_string = window.location.search;
  const params = new URLSearchParams(query_string);
  return params.get(parameter) ?? '';
}

/**
 * Format file size to human-readable format. Based on https://stackoverflow.com/a/20463021/2992851.
 * @param size - Size in bytes.
 * @param use_si - Whether to use SI format (1000 base) or IEC (1024). IEC by default.
 */
export function formatFileSize(size: number, use_si = false): string {
  const si_base = 1_000;
  const iec_base = 1_024;
  const suffixes = 'kMGTPEZYRQ';
  const bytes_unit = 'Bytes';
  const base = use_si ? si_base : iec_base;
  const prefix = use_si ? 'k' : 'K';
  const suffix = use_si ? 'B' : 'iB';

  if (size < base) {
    return `${size.toFixed(2)} ${bytes_unit}`;
  }

  const exponent = Math.floor(Math.log(size) / Math.log(base));
  const normalized = size / base ** exponent;
  const unit = `${suffixes[exponent - 1] ?? ''}${suffix}`;

  return `${normalized.toFixed(2)}${prefix === 'k' && exponent === 1 ? 'kB' : unit}`;
}
