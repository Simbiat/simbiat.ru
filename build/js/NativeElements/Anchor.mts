/**
 * @file Customization of the native anchor elements.
 */
import { empty } from '../Common/Helpers.mts';

/**
 * Customization of the native anchor elements.
 */
export class Anchor {
  /**
   * Apply customizations.
   * @param anchor - Anchor element to customize.
   */
  public static init(anchor: HTMLAnchorElement): void {
    // If `href` is empty, do not do anything
    if (empty(anchor.href)) {
      return;
    }
    const current_url = new URL(anchor.href, window.location.origin);
    // Add `target="_blank"` if the link is not from the current domain
    if (current_url.host !== window.location.host) {
      anchor.target = '_blank';
      // Add noopener and noreferrer for some level of security/privacy.
      // Technically, all modern browsers are supposed to add them during onclick, but can't trust that.
      // Also, noreferrer already implies noopener, but allegedly some browsers at least used to require both, not following the spec.
      if (empty(anchor.rel)) {
        anchor.rel = 'noopener noreferrer';
      } else {
        if (!anchor.rel.includes('noopener')) {
          anchor.rel += ' noopener';
        }
        if (!anchor.rel.includes('noreferrer')) {
          anchor.rel += ' noreferrer';
        }
      }
    }
    // Add an icon indicating that the link will open in a new tab
    if (anchor.target === '_blank' && anchor.querySelector<HTMLImageElement>('img[src*="newtab.svg"]') === null && !anchor.classList.contains('no_new_tab_icon')) {
      const new_tab_icon = document.createElement('img');
      new_tab_icon.loading = 'lazy';
      new_tab_icon.decoding = 'async';
      new_tab_icon.alt = 'Opens in new tab';
      new_tab_icon.src = '/assets/images/newtab.svg';
      new_tab_icon.classList.add('new_tab_icon');
      anchor.insertAdjacentElement('beforeend', new_tab_icon);
      // I am aware of some extensions adding blank anchors that can break the code, so we need to check if the href is empty
    } else if (
      ((anchor.getAttribute('href')
              ?.startsWith('#tab_name_')) === false)
      // False-positive https://github.com/eslint-stylistic/eslint-stylistic/issues/1209
      // eslint-disable-next-line @stylistic/indent-binary-ops
      && !empty(current_url.hash)
      && current_url.origin + current_url.host + current_url.pathname === window.location.origin + window.location.host + window.location.pathname
    ) {
      // Logic to update URL if this is a hash link for the current page
      anchor.addEventListener('click', () => {
        if (!window.location.hash.toLowerCase()
                   .startsWith('#gallery=')) {
          history.replaceState(document.title, document.title, `${current_url.hash}`);
        }
      });
    }
  }
}
