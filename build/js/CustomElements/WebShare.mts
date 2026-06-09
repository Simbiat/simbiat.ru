/**
 * @file Custom logic for the `web-share` element.
 */
import { getMeta } from '../Common/Helpers.mts';
import { Snackbar } from '../Common/Snackbar.mts';

/**
 * Custom logic for the `web-share` element.
 */
export class WebShare extends HTMLElement {
  private readonly share_data = {
    text: getMeta('og:description') ?? getMeta('description') ?? '',
    title: document.title,
    url: document.location.href,
  };

  public constructor() {
    super();
    //Register WebShare if supported
    this.addEventListener('click', () => {
      void this.share();
    });
  }

  /**
   * Attempt to share the current page.
   */
  private async share(): Promise<void> {
    if (typeof navigator.share === 'function') {
      try {
        await navigator.share(this.share_data);
      } catch {
        await this.toClipboard();
      }
    } else {
      await this.toClipboard();
    }
  }

  /**
   * Copy the current page's URL to the clipboard.
   */
  private async toClipboard(): Promise<void> {
    try {
      await navigator.clipboard.writeText(window.location.href);
      void new Snackbar(`Page link copied to clipboard`, 'success');
    } catch {
      void new Snackbar(`Failed to copy page link to clipboard`, 'failure');
    }
  }
}
