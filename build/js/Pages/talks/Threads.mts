/**
 * @file Handle scripts on the thread pages.
 */
import { TIMEZONE } from 'Common/Constants.mts';
import { pageRefresh, empty } from 'Common/Helpers.mts';
import { Ajax } from 'Common/Ajax.mts';
import { Snackbar } from 'Common/Snackbar.mts';
import { type PostForm } from 'CustomElements/PostForm.mts';
import { TinyMCE as TinyMCEClass } from 'Common/TinyMCE.mts';
import type { TinyMCE } from 'tinymce/tinymce.d.ts';
import { Form } from 'NativeElements/Form.mts';

declare const tinymce: TinyMCE;

/**
 * Handle scripts on the thread pages.
 */
export class Threads {
  private readonly add_post_form = document.querySelector<HTMLFormElement>('#post_form');
  private readonly edit_thread_form = document.querySelector<HTMLFormElement>('#thread_form');
  private readonly private_thread_form = document.querySelector<HTMLFormElement>('#thread_private_form');
  private readonly pin_thread_form = document.querySelector<HTMLFormElement>('#thread_pin_form');
  private readonly close_thread_form = document.querySelector<HTMLFormElement>('#thread_closed_form');
  private readonly move_thread_form = document.querySelector<HTMLFormElement>('#thread_move_form');
  private readonly delete_thread_form = document.querySelector<HTMLFormElement>('#thread_delete_form');
  private readonly post_form = document.querySelector<PostForm>('post-form');

  public constructor() {
    if (this.add_post_form) {
      Form.submitIntercept(this.add_post_form, () => {
        void this.addPost();
      });
    }
    if (this.edit_thread_form) {
      Form.submitIntercept(this.edit_thread_form, () => {
        void this.edit();
      });
    }
    if (this.private_thread_form) {
      Form.submitIntercept(this.private_thread_form, () => {
        void this.makePrivate();
      });
    }
    if (this.pin_thread_form) {
      Form.submitIntercept(this.pin_thread_form, () => {
        void this.pin();
      });
    }
    if (this.close_thread_form) {
      Form.submitIntercept(this.close_thread_form, () => {
        void this.close();
      });
    }
    if (this.move_thread_form) {
      Form.submitIntercept(this.move_thread_form, () => {
        void this.move();
      });
    }
    if (this.delete_thread_form) {
      Form.submitIntercept(this.delete_thread_form, () => {
        void this.delete();
      });
    }
    //Listener for `reply to` buttons
    for (const item of document.querySelectorAll<HTMLButtonElement>('.reply_to_button')) {
      //Tracking click to be able to roll back change easily
      (item as HTMLElement).addEventListener('click', (event: MouseEvent) => {
        this.replyTo(event.target as HTMLInputElement);
      });
    }
    // Move to post form on click of #post_form link
    const new_post_tab = document.querySelector<HTMLAnchorElement>('tab-menu a.tab_name[href="#post_form"]');
    const textarea = document.querySelector<HTMLTextAreaElement>('#post_text');
    if (new_post_tab && textarea) {
      new_post_tab.addEventListener('click', (event: MouseEvent) => {
        event.stopPropagation();
        event.preventDefault();
        tinymce.execCommand('mceFocus', false, textarea.id);
      });
    }
  }

  /**
   * Reply to a specific post.
   * @param button - Button that clicked.
   */
  private replyTo(button: HTMLInputElement): void {
    //Get the post's ID
    const reply_to = button.getAttribute('data-post-id') ?? '';
    if (this.post_form && reply_to) {
      this.post_form.replyTo(reply_to);
    }
  }

  /**
   * Add a new post.
   */
  private async addPost(): Promise<void> {
    if (this.add_post_form) {
      const textarea = this.add_post_form.querySelector<HTMLTextAreaElement>('textarea');
      //Ensure we have the latest version of the text from TinyMCE instance
      if (textarea && !empty(textarea.id)) {
        await TinyMCEClass.save(textarea.id, true);
      }
      //Get the `submit` button
      const button = this.add_post_form.querySelector<HTMLButtonElement>('button[type=submit]');
      //Get form data
      const form_data = new FormData(this.add_post_form);
      //Add time zone
      form_data.append('post_data[timezone]', TIMEZONE);
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/talks/posts`,
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
            if (textarea && !empty(textarea.id)) {
              await TinyMCEClass.save(textarea.id);
            }
            void new Snackbar('Post created. Reloading...', 'success');
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
   * Move the thread to a different section.
   */
  private async move(): Promise<void> {
    if (this.move_thread_form) {
      //Get the `submit` button
      const button = this.move_thread_form.querySelector<HTMLButtonElement>('button[type=submit]');
      //Get form data
      const form_data = new FormData(this.move_thread_form);
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/talks/threads/${(form_data.get('thread_data[thread_id]') ?? '0') as string}`,
        form_data,
        method: 'PATCH',
        button,
        /**
         * Further processing on success.
         */
        onSuccess: () => {
          void new Snackbar('Thread moved. Reloading...', 'success');
          pageRefresh();
        },
        keep_disabled: true,
      });
    }
  }

  /**
   * Delete the thread.
   */
  private async delete(): Promise<void> {
    if (this.delete_thread_form && confirm('This is the last chance to back out.\nIf you press \'OK\' this thread will be permanently deleted.\nPress \'Cancel\' to cancel the action.')) {
      //Get the `submit` button
      const button = this.delete_thread_form.querySelector<HTMLButtonElement>('button[type=submit]');
      //Get form data
      const form_data = new FormData(this.delete_thread_form);
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/talks/threads/${(form_data.get('thread_data[thread_id]') ?? '0') as string}`,
        form_data,
        method: 'DELETE',
        button,
        /**
         * Further processing on success.
         * @param response - Response from API endpoint.
         */
        onSuccess: (response) => {
          void new Snackbar('Thread removed. Redirecting to parent...', 'success');
          pageRefresh(response.location);
        },
        keep_disabled: true,
      });
    }
  }

  /**
   * Close or open the thread.
   */
  private async close(): Promise<void> {
    if (this.close_thread_form) {
      //Get the `submit` button
      const button = this.close_thread_form.querySelector<HTMLButtonElement>('button[type=submit]');
      //Get form data
      const form_data = new FormData(this.close_thread_form);
      //Get verb
      const verb = form_data.get('verb') ?? 'close';
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/talks/threads/${(form_data.get('thread_data[thread_id]') ?? '0') as string}`,
        form_data,
        method: 'PATCH',
        button,
        /**
         * Further processing on success.
         */
        onSuccess: () => {
          if (verb === 'open') {
            void new Snackbar('Thread marked as open. Reloading...', 'success');
          } else {
            void new Snackbar('Thread marked as closed. Reloading...', 'success');
          }
          pageRefresh();
        },
        keep_disabled: true,
      });
    }
  }

  /**
   * Update thread.
   */
  private async edit(): Promise<void> {
    if (this.edit_thread_form) {
      //Get the `submit` button
      const button = this.edit_thread_form.querySelector<HTMLButtonElement>('button[type=submit]');
      //Get form data
      const form_data = new FormData(this.edit_thread_form);
      //Check if a custom icon is being attached
      const og_image = this.edit_thread_form.querySelector<HTMLInputElement>('input[type=file]');
      if (og_image?.files?.[0]) {
        form_data.append('thread_data[og_image]', 'true');
      } else {
        form_data.append('thread_data[og_image]', 'false');
      }
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/talks/threads/${(form_data.get('thread_data[thread_id]') ?? '0') as string}`,
        form_data,
        method: 'POST',
        button,
        /**
         * Further processing on success.
         * @param response - Response from API endpoint.
         */
        onSuccess: (response) => {
          if (response.data === true) {
            void new Snackbar('Thread updated. Reloading...', 'success');
            pageRefresh();
          } else if (response.location) {
            void new Snackbar(`${response.reason} View the thread <a href="${response.location}" target="_blank">here</a>.`, 'failure', 0);
          }
        },
        keep_disabled: true,
        require_data_true: true,
      });
    }
  }

  /**
   * Mark thread private or public.
   */
  private async makePrivate(): Promise<void> {
    if (this.private_thread_form) {
      //Get the `submit` button
      const button = this.private_thread_form.querySelector<HTMLButtonElement>('button[type=submit]');
      //Get form data
      const form_data = new FormData(this.private_thread_form);
      //Get verb
      const verb = form_data.get('verb') ?? 'mark_private';
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/talks/threads/${(form_data.get('thread_data[thread_id]') ?? '0') as string}`,
        form_data,
        method: 'PATCH',
        button,
        /**
         * Further processing on success.
         */
        onSuccess: () => {
          if (verb === 'mark_public') {
            void new Snackbar('Thread marked as public. Reloading...', 'success');
          } else {
            void new Snackbar('Thread marked as private. Reloading...', 'success');
          }
          pageRefresh();
        },
        keep_disabled: true,
      });
    }
  }

  /**
   * Pin or unpin thread.
   */
  private async pin(): Promise<void> {
    if (this.pin_thread_form) {
      //Get the `submit` button
      const button = this.pin_thread_form.querySelector<HTMLButtonElement>('button[type=submit]');
      //Get form data
      const form_data = new FormData(this.pin_thread_form);
      //Get verb
      const verb = form_data.get('verb') ?? 'unpin';
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/talks/threads/${(form_data.get('thread_data[thread_id]') ?? '0') as string}`,
        form_data,
        method: 'PATCH',
        button,
        /**
         * Further processing on success.
         */
        onSuccess: () => {
          if (verb === 'pin') {
            void new Snackbar('Thread pinned. Reloading...', 'success');
          } else {
            void new Snackbar('Thread unpinned. Reloading...', 'success');
          }
          pageRefresh();
        },
        keep_disabled: true,
      });
    }
  }
}
