import { addSnackbar, submitIntercept } from 'Common/Helpers.ts';
import { ajax, type AjaxJSONResponse } from 'Common/Ajax.ts';
import { AJAX_TIMEOUT, SNACKBAR_FAIL_LIFE } from 'Common/Constants.ts';
import { buttonToggle } from 'Common/Inputs.ts';

export class PasswordChange {
  private readonly form: HTMLFormElement | null = null;
  private readonly button: HTMLInputElement | null = null;

  public constructor() {
    this.form = document.querySelector('#password_change');
    if (this.form) {
      submitIntercept(this.form, this.change.bind(this));
      this.button = this.form.querySelector('#password_submit');
    }
  }

  private change(): void {
    if (this.form && this.button) {
      //Get form data
      const form_data = new FormData(this.form);
      buttonToggle(this.button);
      void ajax(`${location.protocol}//${location.host}/api/uc/password`, form_data, 'json', 'PATCH', AJAX_TIMEOUT, true).then((response) => {
          const data = response as AjaxJSONResponse;
          if (data.data === true) {
            addSnackbar('Password changed', 'success');
          } else {
            addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
          }
          if (this.button) {
            buttonToggle(this.button);
          }
        });
    }
  }
}
