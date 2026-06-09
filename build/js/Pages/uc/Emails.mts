/**
 * @file Handle scripts on the email management page.
 */
import type { HTMLTableBodyElement } from 'Common/Aliases.mts';
import { empty, deleteRow } from 'Common/Helpers.mts';
import { Ajax } from 'Common/Ajax.mts';
import { Snackbar } from 'Common/Snackbar.mts';
import { Form } from 'NativeElements/Form.mts';

/**
 * Handle scripts on the email management page.
 */
export class Emails {
  private readonly add_mail_form = document.querySelector<HTMLFormElement>('#add_mail_form');
  private readonly submit: HTMLButtonElement | null = null;
  private readonly template = document.querySelector<HTMLTemplateElement>('#email_row');
  private readonly tbody = document.querySelector<HTMLTableBodyElement>('#emails_list tbody');

  public constructor() {
    if (this.add_mail_form) {
      this.submit = this.add_mail_form.querySelector<HTMLButtonElement>('#add_mail_submit');
      Form.submitIntercept(this.add_mail_form, () => {
        void this.add();
      });
      //Listener for mail activation buttons
      for (const item of document.querySelectorAll<HTMLInputElement>('.mail_activation')) {
        item.addEventListener('click', (event) => {
          void Emails.activate(event.target as HTMLInputElement);
        });
      }
      //Listener for mail subscription checkbox
      for (const item of document.querySelectorAll<HTMLInputElement>('[id^=subscription_checkbox_]')) {
        //Tracking click to be able to roll back change easily
        item.addEventListener('click', (event) => {
          void Emails.subscribe(event);
        });
      }
      //Listener for mail deletion buttons
      for (const item of document.querySelectorAll<HTMLInputElement>('.mail_deletion')) {
        item.addEventListener('click', (event) => {
          void Emails.delete(event.currentTarget as HTMLInputElement);
        });
      }
    }
  }

  /**
   * Add email.
   */
  private async add(): Promise<void> {
    if (this.add_mail_form && this.submit) {
      //Get form data
      const form_data = new FormData(this.add_mail_form);
      if (empty(form_data.get('email'))) {
        void new Snackbar('Please, enter a valid email address', 'failure');
        return;
      }
      const email = form_data.get('email') as string;
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/uc/emails/add`,
        form_data,
        method: 'POST',
        button: this.submit,
        /**
         * Further processing on success.
         */
        onSuccess: () => {
          //Add the row to the table
          this.addRow(email);
          //Refresh delete buttons' status
          Emails.blockDelete();
          this.add_mail_form?.reset();
          void new Snackbar(`${email} added`, 'success');
        },
      });
    }
  }

  /**
   * Add email to the table.
   * @param email - Email to add.
   */
  private addRow(email: string): void {
    if (this.tbody && this.template) {
      const clone = this.template.content.cloneNode(true) as HTMLElement;
      const cells = clone.querySelectorAll<HTMLTableCellElement>('td');
      //Set email as the value of the first cell
      if (cells[0]) {
        cells[0].textContent = email;
      }
      if (cells[1]) {
        //Update attributes of the second cell's input
        const input_element_activation = cells[1].querySelector<HTMLInputElement>('input');
        if (input_element_activation) {
          input_element_activation.setAttribute('data-email', email);
          //Attach listener
          input_element_activation.addEventListener('click', (event: MouseEvent) => {
            void Emails.activate(event.target as HTMLInputElement);
          });
        }
        //Update attributes of the second cell's spinner
        const spinner_activation = cells[1].querySelector<HTMLImageElement>('img');
        if (spinner_activation) {
          spinner_activation.setAttribute('data-tooltip', String(spinner_activation.getAttribute('data-tooltip'))
            .replace('email', email));
          spinner_activation.setAttribute('alt', String(spinner_activation.getAttribute('alt'))
            .replace('email', email));
        }
      }
      if (cells[3]) {
        //Update attributes of the 4th cell's input
        const input_element_deletion = cells[3].querySelector<HTMLInputElement>('input');
        if (input_element_deletion) {
          input_element_deletion.setAttribute('data-email', email);
          input_element_deletion.setAttribute('data-tooltip', String(input_element_deletion.getAttribute('data-tooltip'))
            .replace('email', email));
          input_element_deletion.setAttribute('alt', String(input_element_deletion.getAttribute('alt'))
            .replace('email', email));
          //Attach listener
          input_element_deletion.addEventListener('click', (event: MouseEvent) => {
            void Emails.delete(event.currentTarget as HTMLInputElement);
          });
        }
        //Update attributes of the 4th cell's spinner
        const spinner_deletion = cells[3].querySelector<HTMLImageElement>('img');
        if (spinner_deletion) {
          spinner_deletion.setAttribute('data-tooltip', String(spinner_deletion.getAttribute('data-tooltip'))
            .replace('email', email));
          spinner_deletion.setAttribute('alt', String(spinner_deletion.getAttribute('alt'))
            .replace('email', email));
        }
      }
      //Attach the row to the table's body
      this.tbody.appendChild(clone);
    }
  }

  /**
   * Delete email.
   * @param button - Button that was clicked to remove the email.
   */
  private static async delete(button: HTMLInputElement): Promise<void> {
    //Generate form data
    const form_data = new FormData();
    const email = button.getAttribute('data-email') ?? '';
    form_data.set('email', email);
    await Ajax.request({
      url: `${location.protocol}//${location.host}/api/uc/emails/delete`,
      form_data,
      method: 'DELETE',
      button,
      /**
       * Further processing on success.
       */
      onSuccess: () => {
        deleteRow(button);
        Emails.blockDelete();
        void new Snackbar(`${email} removed`, 'success');
      },
    });
  }

  /**
   * Function to block the button for mail removal if we have less than 2 confirmed mails.
   */
  private static blockDelete(): void {
    const confirmed_mail = document.querySelectorAll<HTMLSpanElement>('.mail_confirmed').length;
    for (const input of document.querySelectorAll<HTMLInputElement>('.mail_deletion')) {
      const cell = input.parentElement;
      if (cell) {
        const row = cell.parentElement;
        if (row) {
          //Check if the row is for confirmed mail
          if (row.querySelectorAll<HTMLSpanElement>('.mail_confirmed').length > 0) {
            input.disabled = confirmed_mail < 2;
          } else {
            input.disabled = false;
            //Update tooltips
            if (input.hasAttribute('data-tooltip') && input.getAttribute('data-tooltip') === 'Can\'t delete') {
              const email = String(row.querySelector<HTMLTableCellElement>('td')?.textContent);
              input.setAttribute('data-tooltip', `Delete ${email}`);
              const spinner = cell.querySelector<HTMLImageElement>('.spinner');
              spinner?.setAttribute('data-tooltip', `Removing ${email}...`);
              spinner?.setAttribute('alt', `Removing ${email}...`);
            }
          }
        }
      }
    }
  }

  /**
   * Subscribe email to notifications.
   * @param event - Checkbox click event.
   */
  private static async subscribe(event: Event): Promise<void> {
    event.preventDefault();
    event.stopPropagation();
    const checkbox = event.target as HTMLInputElement;
    if (!checkbox.hasAttribute('data-email')) {
      return;
    }
    //Generate form data
    const email = String(checkbox.getAttribute('data-email'));
    const form_data = new FormData();
    form_data.set('email', email);
    form_data.set('verb', 'subscribe');
    await Ajax.request({
      url: `${location.protocol}//${location.host}/api/uc/emails/subscribe`,
      form_data,
      method: 'PATCH',
      button: checkbox,
      /**
       * Further processing on success.
       */
      onSuccess: () => {
        checkbox.checked = true;
        checkbox.disabled = true;
        if (checkbox.labels) {
          checkbox.labels[0]?.setAttribute('data-tooltip', 'Select another email to unsubscribe this one');
        }
        for (const item of document.querySelectorAll<HTMLInputElement>('[id^=subscription_checkbox_]')) {
          if (item.id !== checkbox.id) {
            item.checked = false;
            item.disabled = false;
            if (item.labels) {
              item.labels[0]?.removeAttribute('data-tooltip');
            }
          }
        }
        for (const item of document.querySelectorAll<HTMLInputElement>('.mail_deletion')) {
          item.disabled = item.hasAttribute('data-email') && item.getAttribute('data-email') === email;
        }
        void new Snackbar(`${email} subscribed`, 'success');
      },
    });
  }

  /**
   * Send email activation.
   * @param button - Button that was clicked to activate email.
   */
  private static async activate(button: HTMLInputElement): Promise<void> {
    //Generate form data
    const email = button.getAttribute('data-email') ?? '';
    const form_data = new FormData();
    form_data.set('verb', 'activate');
    form_data.set('email', email);
    await Ajax.request({
      url: `${location.protocol}//${location.host}/api/uc/emails/activate`,
      form_data,
      method: 'PATCH',
      button,
      /**
       * Further processing on success.
       */
      onSuccess: () => {
        void new Snackbar(`Activation email sent to ${email}`, 'success');
      },
    });
  }
}
