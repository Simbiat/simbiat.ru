export class Contacts {
  private readonly add_thread_form: HTMLFormElement | null = null;

  public constructor() {
    this.add_thread_form = document.querySelector('#thread_form');
    if (this.add_thread_form) {
      submitIntercept(this.add_thread_form, this.addThread.bind(this));
    }
  }

  private addThread(): void {
    if (this.add_thread_form) {
      //Get submit button
      const button = this.add_thread_form.querySelector('input[type=submit]');
      //Get form data
      const form_data = new FormData(this.add_thread_form);
      //Add time zone
      form_data.append('thread_data[timezone]', TIMEZONE);
      buttonToggle(button as HTMLInputElement);
      ajax(`${location.protocol}//${location.host}/api/contact`, form_data, 'json', 'POST', AJAX_TIMEOUT, true)
        .then((response) => {
          const data = response as ajaxJSONResponse;
          if (data.data === true) {
            if (this.add_thread_form) {
              //Notify TinyMCE, that data was saved
              const textarea = this.add_thread_form.querySelector('textarea');
              if (textarea && !empty(textarea.id)) {
                saveTinyMCE(textarea.id);
              }
            }
            addSnackbar('Thread created. Reloading...', 'success');
            pageRefresh(data.location);
          } else {
            if (data.location) {
              addSnackbar(data.reason + ` View the thread <a href="${data.location}" target="_blank" rel="noopener noreferrer">here</a>.`, 'failure', 0);
            } else {
              addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
            }
          }
          buttonToggle(button as HTMLInputElement);
        });
    }
  }
}
