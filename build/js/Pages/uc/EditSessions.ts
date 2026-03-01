import { addSnackbar, deleteRow } from 'Common/Helpers.ts';
import { ajax, type AjaxJSONResponse } from 'Common/Ajax.ts';
import { AJAX_TIMEOUT, SNACKBAR_FAIL_LIFE } from 'Common/Constants.ts';
import { buttonToggle } from 'Common/Inputs.ts';

export class EditSessions {
  private readonly cookieButtons: NodeListOf<HTMLInputElement>;
  private readonly sessionButtons: NodeListOf<HTMLInputElement>;

  public constructor() {
    this.cookieButtons = document.querySelectorAll('.cookie_deletion:not([disabled])');
    this.sessionButtons = document.querySelectorAll('.session_deletion:not([disabled])');
    //Listener for deletion buttons
    document.querySelectorAll('.cookie_deletion, .session_deletion').forEach((item) => {
              item.addEventListener('click', (event) => {
                EditSessions.delete(event.target as HTMLInputElement);
              });
            });
    //Listener for "Delete all" buttons
    document.querySelectorAll('#delete_cookies, #delete_sessions').forEach((item) => {
              item.addEventListener('click', (event) => {
                this.deleteAll(event.target as HTMLInputElement);
              });
            });
  }

  private deleteAll(button: HTMLInputElement): void {
    let buttons: NodeListOf<HTMLInputElement>;
    let type: string;
    if (button.id === 'delete_cookies') {
      type = 'cookies';
      buttons = this.cookieButtons;
    } else if (button.id === 'delete_sessions') {
      type = 'sessions';
      buttons = this.sessionButtons;
    } else {
      addSnackbar('Unknown button type', 'failure', SNACKBAR_FAIL_LIFE);
      return;
    }
    //Traverse in reverse, because of numeric row IDs used for rows removal
    const ArrayOfButtons = Array.from(buttons).reverse();
    ArrayOfButtons.forEach((item) => {
      EditSessions.delete(item, false);
    });
    addSnackbar(`All ${type} except current were removed`, 'success');
  }

  private static delete(button: HTMLInputElement, singular = true): void {
    //Generate form data
    const form_data = new FormData();
    let type: string;
    let typeSingular: string;
    if (button.classList.contains('cookie_deletion')) {
      type = 'cookies';
      typeSingular = 'Cookie';
      form_data.set('cookie', String(button.getAttribute('data-cookie')));
    } else if (button.classList.contains('session_deletion')) {
      type = 'sessions';
      typeSingular = 'Session';
      form_data.set('session', String(button.getAttribute('data-session')));
    } else {
      addSnackbar('Unknown button type', 'failure', SNACKBAR_FAIL_LIFE);
      return;
    }
    buttonToggle(button);
    void ajax(`${location.protocol}//${location.host}/api/uc/${type}/delete`, form_data, 'json', 'DELETE', AJAX_TIMEOUT, true).then((response) => {
        const data = response as AjaxJSONResponse;
        if (data.data === true) {
          deleteRow(button);
          if (singular) {
            addSnackbar(`${typeSingular} removed`, 'success');
          }
        } else {
          buttonToggle(button);
          addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
        }
      });
  }
}
