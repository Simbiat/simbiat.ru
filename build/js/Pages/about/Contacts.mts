/**
 * @file Handle scripts on the contact page.
 */
import { TIMEZONE } from 'Common/Constants.mts';
import { empty, pageRefresh } from 'Common/Helpers.mts';
import { Ajax } from 'Common/Ajax.mts';
import { Snackbar } from 'Common/Snackbar.mts';
import { saveTinyMCE } from 'Common/TinyMCE.mts';
import { Form } from 'NativeElements/Form.mts';

/**
 * Handle scripts on the contact page.
 */
export class Contacts {
  private readonly add_thread_form = document.querySelector<HTMLFormElement>('#thread_form');

  public constructor() {
    if (this.add_thread_form) {
      Form.submitIntercept(this.add_thread_form, () => {
        void this.addThread();
      });
    }
  }

  /**
   * Create a new thread.
   */
  private async addThread(): Promise<void> {
    if (this.add_thread_form) {
      //Get the `submit` button
      const button = this.add_thread_form.querySelector<HTMLButtonElement>('button[type=submit]');
      //Get form data
      const form_data = new FormData(this.add_thread_form);
      //Add time zone
      form_data.append('thread_data[timezone]', TIMEZONE);
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/contact`,
        form_data,
        method: 'POST',
        button,
        /**
         * Further processing on success.
         * @param response - Response from API endpoint.
         */
        onSuccess: async (response) => {
          if (response.data === true) {
            //Notify TinyMCE that data was saved
            const textarea = this.add_thread_form?.querySelector<HTMLTextAreaElement>('textarea');
            if (textarea && !empty(textarea.id)) {
              await saveTinyMCE(textarea.id);
            }
            void new Snackbar('Thread created. Reloading...', 'success');
            pageRefresh(response.location);
          } else if (response.location) {
            void new Snackbar(`${response.reason} View the thread <a href="${response.location}" target="_blank" rel="noopener noreferrer">here</a>.`, 'failure', 0);
          }
        },
        keep_disabled: true,
        require_data_true: true,
      });
    }
  }
}
