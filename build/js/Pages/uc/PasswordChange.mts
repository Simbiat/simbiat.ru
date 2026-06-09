/**
 * @file Handle scripts on the password page.
 */
import { Ajax } from 'Common/Ajax.mts';
import { Snackbar } from 'Common/Snackbar.mts';
import { Form } from 'NativeElements/Form.mts';

/**
 * Handle scripts on the password page.
 */
export class PasswordChange {
  private readonly form = document.querySelector<HTMLFormElement>('#password_change');
  private readonly button: HTMLButtonElement | null = null;

  public constructor() {
    if (this.form) {
      Form.submitIntercept(this.form, () => {
        void this.change();
      });
      this.button = this.form.querySelector<HTMLButtonElement>('#password_submit');
    }
  }

  /**
   * Change password.
   */
  private async change(): Promise<void> {
    if (this.form && this.button) {
      //Get form data
      const form_data = new FormData(this.form);
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/uc/password`,
        form_data,
        method: 'PATCH',
        button: this.button,
        /**
         * Further processing on success.
         */
        onSuccess: () => {
          void new Snackbar('Password changed', 'success');
        },
      });
    }
  }
}
