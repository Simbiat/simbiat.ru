/**
 * @file Handle scripts on the FFXIV linkage page.
 */
import { pageRefresh } from 'Common/Helpers.mts';
import { Ajax } from 'Common/Ajax.mts';
import { Snackbar } from 'Common/Snackbar.mts';
import { Form } from 'NativeElements/Form.mts';

/**
 * Handle scripts on the FFXIV linkage page.
 */
export class EditFFLinks {
  private readonly form = document.querySelector<HTMLFormElement>('#ff_link_user');
  private readonly button: HTMLButtonElement | null = null;

  public constructor() {
    if (this.form) {
      Form.submitIntercept(this.form, () => {
        void this.link();
      });
      this.button = this.form.querySelector<HTMLButtonElement>('#ff_link_submit');
    }
  }

  /**
   * Link FFXIV character.
   */
  private async link(): Promise<void> {
    if (this.form && this.button) {
      //Get form data
      const form_data = new FormData(this.form);
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/uc/fflink`,
        form_data,
        method: 'POST',
        button: this.button,
        /**
         * Further processing on success.
         */
        onSuccess: () => {
          void new Snackbar('Character linked successfully. Reloading page...', 'success');
          pageRefresh();
        },
        keep_disabled: true,
      });
    }
  }
}
