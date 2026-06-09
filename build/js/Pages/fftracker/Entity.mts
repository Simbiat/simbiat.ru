/**
 * @file Handle scripts on the FFXIV entity pages.
 */
import { pageRefresh } from 'Common/Helpers.mts';
import { Ajax } from 'Common/Ajax.mts';
import { Snackbar } from 'Common/Snackbar.mts';

/**
 * Handle scripts on the FFXIV entity pages.
 */
export class Entity {
  private readonly force_refresh = document.querySelector<HTMLButtonElement>('#ff_refresh');

  public constructor() {
    if (this.force_refresh) {
      //Attach on-click event
      this.force_refresh.addEventListener('click', () => {
        void this.refresh();
      });
    }
  }

  /**
   * Refresh the entity.
   */
  private async refresh(): Promise<void> {
    if (this.force_refresh) {
      this.force_refresh.classList.add('spin');
      await Ajax.request({
        url: `${location.protocol}//${location.host}${this.force_refresh.getAttribute('data-link') ?? ''}`,
        method: 'PATCH',
        button: this.force_refresh,
        /**
         * Further processing on success.
         */
        onSuccess: () => {
          void new Snackbar('Data updated. Reloading page...', 'success');
          pageRefresh();
        },
        /**
         * Further processing on error.
         */
        onError: () => {
          this.force_refresh?.classList.remove('spin');
        },
        keep_disabled: true,
      });
    }
  }
}
