/**
 * @file Handle scripts on the pages of posts.
 */
import { empty, pageRefresh } from 'Common/Helpers.mts';
import { Ajax } from 'Common/Ajax.mts';
import { Snackbar } from 'Common/Snackbar.mts';
import { TinyMCE } from 'Common/TinyMCE.mts';
import { Form } from 'NativeElements/Form.mts';

/**
 * Handle scripts on the pages of posts.
 */
export class Posts {
  private readonly post_form = document.querySelector<HTMLFormElement>('post-form form');
  private readonly delete_post_form = document.querySelector<HTMLFormElement>('#delete_post_form');
  private readonly move_post_form = document.querySelector<HTMLFormElement>('#post_move_form');

  public constructor() {
    //Listener for form
    if (this.post_form) {
      Form.submitIntercept(this.post_form, () => {
        void this.edit();
      });
    }
    //Listener for deletion
    if (this.delete_post_form) {
      Form.submitIntercept(this.delete_post_form, () => {
        void this.delete();
      });
    }
    //Listener for moving the post
    if (this.move_post_form) {
      Form.submitIntercept(this.move_post_form, () => {
        void this.move();
      });
    }
  }

  /**
   * Move a post to another thread.
   */
  private async move(): Promise<void> {
    if (this.move_post_form) {
      //Get the `submit` button
      const button = this.move_post_form.querySelector<HTMLButtonElement>('button[type=submit]');
      //Get form data
      const form_data = new FormData(this.move_post_form);
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/talks/posts/${(form_data.get('post_data[post_id]') ?? '0') as string}`,
        form_data,
        method: 'PATCH',
        button,
        /**
         * Further processing on success.
         */
        onSuccess: () => {
          void new Snackbar('Post moved. Reloading...', 'success');
          pageRefresh();
        },
        keep_disabled: true,
      });
    }
  }

  /**
   * Edit post.
   */
  private async edit(): Promise<void> {
    if (this.post_form) {
      //Get the `submit` button
      const button = this.post_form.querySelector<HTMLButtonElement>('button[type=submit]');
      //Get form data
      const form_data = new FormData(this.post_form);
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/talks/posts/${(form_data.get('post_data[post_id]') ?? '0') as string}`,
        form_data,
        method: 'PATCH',
        button,
        /**
         * Further processing on success.
         * @param response - Response from API endpoint.
         */
        onSuccess: async (response) => {
          if (response.data === true) {
            //Notify TinyMCE that data was saved
            const textarea = this.post_form?.querySelector<HTMLTextAreaElement>('textarea');
            if (textarea && !empty(textarea.id)) {
              await TinyMCE.save(textarea.id);
            }
            void new Snackbar('Post updated. Reloading...', 'success');
            pageRefresh(response.location);
          } else if (response.location) {
            void new Snackbar(`${response.reason} View the post <a href="${response.location}" target="_blank">here</a>.`, 'failure', 0);
          }
        },
        keep_disabled: true,
        require_data_true: true,
      });
    }
  }

  /**
   * Delete post.
   */
  private async delete(): Promise<void> {
    if (this.delete_post_form && confirm('This is the last chance to back out.\nIf you press \'OK\' this post will be permanently deleted.\nPress \'Cancel\' to cancel the action.')) {
      //Get the `submit` button
      const button = this.delete_post_form.querySelector<HTMLButtonElement>('button[type=submit]');
      //Get form data
      const form_data = new FormData(this.delete_post_form);
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/talks/posts/${(form_data.get('post_data[post_id]') ?? '0') as string}`,
        form_data,
        method: 'PATCH',
        button,
        /**
         * Further processing on success.
         * @param response - Response from API endpoint.
         */
        onSuccess: (response) => {
          void new Snackbar('Post removed. Redirecting to thread...', 'success');
          pageRefresh(response.location);
        },
        keep_disabled: true,
      });
    }
  }
}
