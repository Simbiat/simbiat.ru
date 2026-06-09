/**
 * @file Handle scripts on the BIC keying page.
 */
import { Ajax } from 'Common/Ajax.mts';
import { updateHistory } from 'Common/Helpers.mts';

/**
 * Handle scripts on the BIC keying page.
 */
export class Keying {
  private readonly form = document.querySelector<HTMLFormElement>('#bic_keying');
  private readonly result = document.querySelector<HTMLSpanElement>('#acc_check_result');
  private readonly bic_key_sample = document.querySelector<HTMLSpanElement>('#bic_key_sample');
  private readonly acc_key_sample = document.querySelector<HTMLSpanElement>('#account_key_sample');
  private readonly spinner = document.querySelector<HTMLImageElement>('#bic_spinner');

  public constructor() {
    for (const element of document.querySelectorAll<HTMLInputElement>('#bic_key, #account_key')) {
      for (const event_type of ['change', 'input', 'paste']) {
        element.addEventListener(event_type, () => {
          void this.calc();
        });
      }
    }
  }

  /**
   * Calculate the key.
   */
  private async calc(): Promise<boolean> {
    if (this.form && this.result && this.bic_key_sample && this.acc_key_sample) {
      //Get form data
      const form_data = new FormData(this.form);
      const bic_key = String(form_data.get('bic_key') as string ?? '');
      const acc_key = String(form_data.get('account_key') as string ?? '');
      this.result.classList.remove(...this.result.classList);
      if ((/^\d{9}$/v).exec(bic_key) === null) {
        this.result.classList.add('failure');
        this.result.textContent = 'Неверный формат БИКа';
        Keying.styleBic(this.bic_key_sample, 'warning', 'БИК');
        return false;
      }
      Keying.styleBic(this.bic_key_sample, 'success', bic_key);
      if ((/^\d{5}[\dАВЕКМНРСТХавекмнрстх]\d{14}$/v).exec(acc_key) === null) {
        this.result.classList.add('failure');
        this.result.textContent = 'Неверный формат счёта';
        Keying.styleBic(this.acc_key_sample, 'warning', 'СЧЁТ');
        return false;
      }
      Keying.styleBic(this.acc_key_sample, 'success', acc_key);
      //Initiate request
      this.result.classList.add('warning');
      this.result.textContent = 'Проверяем...';
      if (this.spinner) {
        this.spinner.classList.remove('hidden');
      }
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/bictracker/keying`,
        form_data,
        method: 'POST',
        /**
         * Further processing on success.
         * @param response - Response from API endpoint.
         */
        onSuccess: (response) => {
          updateHistory(`${location.protocol}//${location.host}/bictracker/keying/${bic_key}/${acc_key}/`, `Ключевание счёта ${acc_key}`);
          if (this.result) {
            this.result.classList.remove(...this.result.classList);
            if (response.data === true) {
              this.result.classList.add('success');
              this.result.textContent = 'Правильное ключевание';
              return;
            }
            this.result.classList.add('failure');
            if (response.data === false) {
              this.result.textContent = 'Непредвиденная ошибка';
            } else {
              const inner_span = document.createElement('span');
              inner_span.classList.add('success');
              inner_span.textContent = String(response.data);
              this.result.textContent = `Неверное ключевание. Ожидаемый ключ: ${response.data} (${acc_key.replace(/(?<beforeKey>^\d{5}[\dАВЕКМНРСТХавекмнрстх]\d{2}).*$/v, '$<beforeKey>')}`;
              this.result.appendChild(inner_span);
              this.result.appendChild(document.createTextNode(`${acc_key.replace(/^\d{5}[\dАВЕКМНРСТХавекмнрстх]\d{3}(?=\d{11}$)/v, '')})`));
            }
          }
        },
      });
      if (this.spinner) {
        this.spinner.classList.add('hidden');
      }
    }
    return false;
  }

  /**
   * Helper function for styling.
   * @param element - Element to style.
   * @param new_class - New class to add.
   * @param text - Text to set.
   */
  private static styleBic(element: HTMLSpanElement, new_class: string, text = ''): void {
    element.classList.remove(...element.classList);
    element.classList.add(new_class);
    element.textContent = text;
  }
}
