/**
 * @file Handle scripts on the BIC search page.
 */
import { Ajax } from 'Common/Ajax.mts';
import { Snackbar } from 'Common/Snackbar.mts';
import { Form } from 'NativeElements/Form.mts';

/**
 * Handle scripts on the BIC search page.
 */
export class Refresh {
  private readonly refresh_button = document.querySelector<HTMLButtonElement>('#bic_refresh');

  public constructor() {
    if (this.refresh_button) {
      this.refresh_button.addEventListener('click', (event: MouseEvent) => {
        void this.refresh(event);
      });
    }
    Form.searchForm();
  }

  /**
   * Refresh BIC library through API.
   * @param event - Event to process. Required to allow recursive update.
   */
  private async refresh(event: Event): Promise<void> {
    if (!this.refresh_button) {
      return;
    }
    this.refresh_button.classList.add('spin');
    await Ajax.request({
      url: `${location.protocol}//${location.host}/api/bictracker/dbupdate`,
      method: 'PUT',
      button: this.refresh_button,
      /**
       * Further processing on success.
       * @param response - Response from API endpoint.
       */
      onSuccess: async (response) => {
        if (response.data === true) {
          void new Snackbar('Библиотека БИК обновлена', 'success');
          this.refresh_button?.classList.remove('spin');
        } else if (typeof response.data === 'number') {
          //Create a date from timestamp
          const timestamp: Date = new Date(response.data * 1000);
          //Get time block
          const datetime = document.querySelector<HTMLTimeElement>('.bic_date');
          if (datetime) {
            //Update its value
            datetime.setAttribute('datetime', timestamp.toISOString());
            const day = `0${String(timestamp.getUTCDate())}`;
            const month = `0${String(timestamp.getMonth() + 1)}`;
            datetime.textContent = `${day.slice(-2)}.${month.slice(-2)}.${String(timestamp.getUTCFullYear())}`;
            void new Snackbar(`Применено обновление за ${datetime.textContent}`, 'success');
            this.refresh_button?.classList.remove('spin');
            await this.refresh(event);
          }
        }
      },
    });
    this.refresh_button.classList.remove('spin');
  }
}
