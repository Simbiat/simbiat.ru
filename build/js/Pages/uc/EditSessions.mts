/**
 * @file Handle scripts on the session management page.
 */
import { deleteRow } from 'Common/Helpers.mts';
import { Ajax } from 'Common/Ajax.mts';
import { Snackbar, SNACKBAR_FAIL_TIMEOUT } from 'Common/Snackbar.mts';

/**
 * Handle scripts on the session management page.
 */
export class EditSessions {
  private readonly cookie_buttons = document.querySelectorAll<HTMLButtonElement>('.cookie_deletion:not([disabled])');
  private readonly session_buttons = document.querySelectorAll<HTMLButtonElement>('.session_deletion:not([disabled])');

  public constructor() {
    //Listener for deletion buttons
    for (const item of document.querySelectorAll<HTMLButtonElement>('.cookie_deletion, .session_deletion')) {
      item.addEventListener('click', (event) => {
        void EditSessions.delete(event.currentTarget as HTMLButtonElement);
      });
    }
    //Listener for "Delete all" buttons
    for (const item of document.querySelectorAll<HTMLButtonElement>('#delete_cookies, #delete_sessions')) {
      item.addEventListener('click', (event) => {
        this.deleteAll(event.currentTarget as HTMLButtonElement);
      });
    }
  }

  /**
   * Delete all sessions or cookies (one by one).
   * @param button - Button that was clicked.
   */
  private deleteAll(button: HTMLButtonElement): void {
    let buttons: NodeListOf<HTMLButtonElement>;
    let type: string;
    if (button.id === 'delete_cookies') {
      type = 'cookies';
      buttons = this.cookie_buttons;
    } else if (button.id === 'delete_sessions') {
      type = 'sessions';
      buttons = this.session_buttons;
    } else {
      void new Snackbar('Unknown button type', 'failure', SNACKBAR_FAIL_TIMEOUT);
      return;
    }
    //Traverse in reverse, because of numeric row IDs used for rows removal
    const array_of_buttons = Array.from(buttons)
                                  .reverse();
    for (const item of array_of_buttons) {
      void EditSessions.delete(item, false);
    }
    void new Snackbar(`All ${type} except current were removed`, 'success');
  }

  /**
   * Delete session or cookie.
   * @param button - Button that was clicked.
   * @param singular - If singular item is being removed. If `true`, the snackbar will be shown after success.
   */
  private static async delete(button: HTMLButtonElement, singular = true): Promise<void> {
    //Generate form data
    const form_data = new FormData();
    let type: string;
    let type_singular: string;
    if (button.classList.contains('cookie_deletion')) {
      type = 'cookies';
      type_singular = 'Cookie';
      form_data.set('cookie', String(button.getAttribute('data-cookie')));
    } else if (button.classList.contains('session_deletion')) {
      type = 'sessions';
      type_singular = 'Session';
      form_data.set('session', String(button.getAttribute('data-session')));
    } else {
      void new Snackbar('Unknown button type', 'failure', SNACKBAR_FAIL_TIMEOUT);
      return;
    }
    await Ajax.request({
      url: `${location.protocol}//${location.host}/api/uc/${type}/delete`,
      form_data,
      method: 'DELETE',
      button,
      /**
       * Further processing on success.
       */
      onSuccess: () => {
        deleteRow(button);
        if (singular) {
          void new Snackbar(`${type_singular} removed`, 'success');
        }
      },
    });
  }
}
