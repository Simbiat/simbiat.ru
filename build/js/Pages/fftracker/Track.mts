/**
 * @file Handle scripts on the FFXIV "add to tracker" page.
 */
import { Ajax } from 'Common/Ajax.mts';
import { HTTP_NOT_FOUND } from 'Common/Constants.mts';
import { Snackbar, SNACKBAR_FAIL_TIMEOUT } from 'Common/Snackbar.mts';
import { Form } from 'NativeElements/Form.mts';

/**
 * Handle scripts on the FFXIV "add to tracker" page.
 */
export class Track {
  private readonly form = document.querySelector<HTMLFormElement>('#ff_track_register');
  private readonly select = document.querySelector<HTMLSelectElement>('#ff_track_type');
  private readonly id_input = document.querySelector<HTMLInputElement>('#ff_track_id');

  public constructor() {
    if (this.select) {
      this.select.addEventListener('change', () => {
        this.typeChange();
      });
    }
    if (this.form) {
      Form.submitIntercept(this.form, () => {
        void this.add();
      });
    }
  }

  /**
   * Track the entity.
   */
  private async add(): Promise<void> {
    if (this.select) {
      const selected_option = this.select.selectedOptions[0];
      let select_text: string;
      if (selected_option) {
        select_text = selected_option.text;
      } else {
        select_text = 'Character';
      }
      if (this.id_input) {
        const button = document.querySelector<HTMLButtonElement>('#ff_track_submit');
        await Ajax.request({
          url: `${location.protocol}//${location.host}/api/fftracker/${this.select.value}/${this.id_input.value}`,
          form_data: null,
          method: 'POST',
          button,
          /**
           * Further processing on success.
           * @param response - Response from API endpoint.
           */
          onSuccess: (response) => {
            if (response.data === true) {
              void new Snackbar(`${select_text} with ID ${this.id_input?.value ?? ''} was registered. Check <a href="${response.location}" target="_blank" rel="noopener noreferrer">here</a>.`, 'success', 0);
            } else if (response.status === HTTP_NOT_FOUND) {
              void new Snackbar(`${select_text} with ID ${this.id_input?.value ?? ''} was not found on Lodestone.`, 'failure', SNACKBAR_FAIL_TIMEOUT);
            } else if ((/^id `.*` is already registered$/iv).exec(response.reason)) {
              void new Snackbar(`${response.reason}. Check <a href="${response.location}" target="_blank" rel="noopener noreferrer">here</a>.`, 'warning', 0);
            } else {
              void new Snackbar(response.reason, 'failure', SNACKBAR_FAIL_TIMEOUT);
            }
          },
        });
      }
    }
  }

  /**
   * Updates pattern for the input field.
   */
  private typeChange(): void {
    if (this.select && this.id_input) {
      //Set default value for pattern
      let pattern = '^\\d{1,20}$';
      //Update pattern value
      if (this.select.value === 'pvpteams' || this.select.value === 'crossworld_linkshells') {
        pattern = '^[\\da-z]{40}$';
      }
      //Set a pattern for the element
      this.id_input.setAttribute('pattern', pattern);
    }
  }
}
