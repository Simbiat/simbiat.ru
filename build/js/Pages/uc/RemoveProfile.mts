/**
 * @file Handle scripts on the profile removal page.
 */
import { pageRefresh } from 'Common/Helpers.mts';
import { Ajax } from 'Common/Ajax.mts';
import { Snackbar, SNACKBAR_FAIL_TIMEOUT } from 'Common/Snackbar.mts';

/**
 * Handle scripts on the profile removal page.
 */
export class RemoveProfile {
  private readonly button = document.querySelector<HTMLButtonElement>('#remove_user');
  private readonly checkbox = document.querySelector<HTMLInputElement>('#hard_removal');

  public constructor() {
    //Check if the form exists
    if (document.querySelector<HTMLFormElement>('#user_removal')) {
      //Add event listeners
      if (this.checkbox) {
        this.checkbox.addEventListener('change', this.style.bind(this));
      }
      if (this.button) {
        this.button.addEventListener('click', () => {
          void this.remove();
        });
      }
    }
  }

  /**
   * Remove the profile.
   */
  private async remove(): Promise<void> {
    if (this.checkbox && this.button) {
      if (confirm(`This is the last chance to back out.\nIf you press 'OK' your user will be ${this.checkbox.checked ? 'permanently deleted' : 'removed'}.\nPress 'Cancel' to cancel the action.`)) {
        //Get form data
        const form_data = new FormData();
        //Append value of the checkbox
        form_data.append('hard', (this.checkbox.checked ? 'true' : 'false'));
        await Ajax.request({
          url: `${location.protocol}//${location.host}/api/uc/remove`,
          form_data,
          method: 'PATCH',
          button: this.button,
          /**
           * Further processing on success.
           * @param response - Response from API endpoint.
           */
          onSuccess: (response) => {
            if (response.data === true) {
              void new Snackbar('Sad to see you go 😭', 'success', SNACKBAR_FAIL_TIMEOUT);
              pageRefresh();
            } else {
              void new Snackbar('Gods gave you another chance with this failure. 😇 Time to rethink your decision, maybe? 🤔', 'failure', SNACKBAR_FAIL_TIMEOUT);
            }
          },
          keep_disabled: true,
          require_data_true: true,
        });
      } else {
        void new Snackbar('Phew... That was a close one. 😅 No need to rush with drastic measures. 😊', 'success');
      }
    }
  }

  /**
   * Style checkbox.
   */
  private style(): void {
    if (this.checkbox?.parentNode) {
      if (this.checkbox.checked) {
        this.checkbox.parentNode.querySelector<HTMLLabelElement>('label')
            ?.classList
            .add('failure');
        this.button?.classList.replace('warning', 'failure');
      } else {
        this.checkbox.parentNode.querySelector<HTMLLabelElement>('label')
            ?.classList
            .remove('failure');
        this.button?.classList.replace('failure', 'warning');
      }
    }
  }
}
