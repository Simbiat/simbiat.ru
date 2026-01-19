export class Posts {
  private readonly post_form: HTMLFormElement | null = null;
  private readonly delete_post_form: HTMLFormElement | null = null;

  public constructor() {
    this.post_form = document.querySelector('post-form form');
    this.delete_post_form = document.querySelector('#delete_post_form');
    //Listener for form
    if (this.post_form) {
      submitIntercept(this.post_form, this.edit.bind(this));
    }
    //Listener for deletion
    if (this.delete_post_form) {
      submitIntercept(this.delete_post_form, this.delete.bind(this));
    }
  }

  private edit(): void {
    if (this.post_form) {
      //Get submit button
      const button = this.post_form.querySelector('input[type=submit]');
      //Get form data
      const form_data = new FormData(this.post_form);
      buttonToggle(button as HTMLInputElement);
      ajax(`${location.protocol}//${location.host}/api/talks/posts/${String(form_data.get('post_data[post_id]') ?? '0')}/edit`, form_data, 'json', 'PATCH', AJAX_TIMEOUT, true)
        .then((response) => {
          const data = response as ajaxJSONResponse;
          if (data.data === true) {
            //Notify TinyMCE, that data was saved
            if (this.post_form) {
              const textarea = this.post_form.querySelector('textarea');
              if (textarea && !empty(textarea.id)) {
                saveTinyMCE(textarea.id);
              }
            }
            addSnackbar('Post updated. Reloading...', 'success');
            pageRefresh(data.location);
          } else {
            if (data.location) {
              addSnackbar(data.reason + ` View the post <a href="${data.location}" target="_blank" rel="noopener noreferrer">here</a>.`, 'failure', 0);
            } else {
              addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
            }
            buttonToggle(button as HTMLInputElement);
          }
        });
    }
  }

  private delete(): void {
    if (this.delete_post_form) {
      if (confirm('This is the last chance to back out.\nIf you press \'OK\' this post will be permanently deleted.\nPress \'Cancel\' to cancel the action.')) {
        //Get submit button
        const button = this.delete_post_form.querySelector('input[type=submit]');
        //Get form data
        const form_data = new FormData(this.delete_post_form);
        buttonToggle(button as HTMLInputElement);
        ajax(`${location.protocol}//${location.host}/api/talks/posts/${String(form_data.get('post_data[post_id]') ?? '0')}`, form_data, 'json', 'DELETE', AJAX_TIMEOUT, true)
          .then((response) => {
            const data = response as ajaxJSONResponse;
            if (data.data === true) {
              addSnackbar('Post removed. Redirecting to thread...', 'success');
              pageRefresh(data.location);
            } else {
              addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
            }
            buttonToggle(button as HTMLInputElement);
          });
      }
    }
  }
}
