/**
 * @file Handle scripts on the section pages.
 */
import { TIMEZONE } from 'Common/Constants.mts';
import { pageRefresh, empty } from 'Common/Helpers.mts';
import { Ajax } from 'Common/Ajax.mts';
import { Snackbar } from 'Common/Snackbar.mts';
import { saveTinyMCE } from 'Common/TinyMCE.mts';
import { Form } from 'NativeElements/Form.mts';

/**
 * Handle scripts on the section pages.
 */
export class Sections {
  private readonly add_section_form = document.querySelector<HTMLFormElement>('#add_section_form');
  private readonly add_thread_form = document.querySelector<HTMLFormElement>('#thread_form');
  private readonly edit_section_form = document.querySelector<HTMLFormElement>('#edit_section_form');
  private readonly private_section_form = document.querySelector<HTMLFormElement>('#section_private_form');
  private readonly close_section_form = document.querySelector<HTMLFormElement>('#section_closed_form');
  private readonly move_section_form = document.querySelector<HTMLFormElement>('#section_move_form');
  private readonly delete_section_form = document.querySelector<HTMLFormElement>('#section_delete_form');

  public constructor() {
    if (this.add_section_form) {
      Form.submitIntercept(this.add_section_form, () => {
        void this.addSection();
      });
    }
    if (this.add_thread_form) {
      Form.submitIntercept(this.add_thread_form, () => {
        void this.addThread();
      });
    }
    if (this.edit_section_form) {
      Form.submitIntercept(this.edit_section_form, () => {
        void this.edit();
      });
    }
    if (this.private_section_form) {
      Form.submitIntercept(this.private_section_form, () => {
        void this.makePrivate();
      });
    }
    if (this.close_section_form) {
      Form.submitIntercept(this.close_section_form, () => {
        void this.close();
      });
    }
    if (this.move_section_form) {
      Form.submitIntercept(this.move_section_form, () => {
        void this.move();
      });
    }
    if (this.delete_section_form) {
      Form.submitIntercept(this.delete_section_form, () => {
        void this.delete();
      });
    }
  }

  /**
   * Mark section private or public.
   */
  private async makePrivate(): Promise<void> {
    if (this.private_section_form) {
      //Get the `submit` button
      const button = this.private_section_form.querySelector<HTMLButtonElement>('button[type=submit]');
      //Get form data
      const form_data = new FormData(this.private_section_form);
      //Get verb
      const verb = form_data.get('verb') ?? 'mark_private';
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/talks/sections/${(form_data.get('section_data[section_id]') ?? '0') as string}`,
        form_data,
        method: 'PATCH',
        button,
        /**
         * Further processing on success.
         */
        onSuccess: () => {
          if (verb === 'mark_public') {
            void new Snackbar('Section marked as public', 'success');
          } else {
            void new Snackbar('Section marked as private', 'success');
          }
          pageRefresh();
        },
        keep_disabled: true,
      });
    }
  }

  /**
   * Move section.
   */
  private async move(): Promise<void> {
    if (this.move_section_form) {
      //Get the `submit` button
      const button = this.move_section_form.querySelector<HTMLButtonElement>('button[type=submit]');
      //Get form data
      const form_data = new FormData(this.move_section_form);
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/talks/sections/${(form_data.get('section_data[section_id]') ?? '0') as string}`,
        form_data,
        method: 'PATCH',
        button,
        /**
         * Further processing on success.
         */
        onSuccess: () => {
          void new Snackbar('Section moved. Reloading...', 'success');
          pageRefresh();
        },
        keep_disabled: true,
      });
    }
  }

  /**
   * Close or open the section.
   */
  private async close(): Promise<void> {
    if (this.close_section_form) {
      //Get the `submit` button
      const button = this.close_section_form.querySelector<HTMLButtonElement>('button[type=submit]');
      //Get form data
      const form_data = new FormData(this.close_section_form);
      //Get verb
      const verb = form_data.get('verb') ?? 'close';
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/talks/sections/${(form_data.get('section_data[section_id]') ?? '0') as string}`,
        form_data,
        method: 'PATCH',
        button,
        /**
         * Further processing on success.
         */
        onSuccess: () => {
          if (verb === 'open') {
            void new Snackbar('Section marked as open', 'success');
          } else {
            void new Snackbar('Section marked as closed', 'success');
          }
          pageRefresh();
        },
        keep_disabled: true,
      });
    }
  }

  /**
   * Create a new section.
   */
  private async addSection(): Promise<void> {
    if (this.add_section_form) {
      //Get the `submit` button
      const button = this.add_section_form.querySelector<HTMLButtonElement>('button[type=submit]');
      //Get form data
      const form_data = new FormData(this.add_section_form);
      //Check if the custom icon is being attached
      const icon = this.add_section_form.querySelector<HTMLInputElement>('input[type=file]');
      if (icon?.files?.[0]) {
        form_data.append('section_data[icon]', 'true');
      } else {
        form_data.append('section_data[icon]', 'false');
      }
      //Add time zone
      form_data.append('section_data[timezone]', TIMEZONE);
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/talks/sections/${(form_data.get('section_data[section_id]') ?? '0') as string}`,
        form_data,
        method: 'PATCH',
        button,
        /**
         * Further processing on success.
         * @param response - Response from API endpoint.
         */
        onSuccess: (response) => {
          if (response.data === true) {
            void new Snackbar('Section created. Reloading...', 'success');
            pageRefresh();
          } else if (response.location) {
            void new Snackbar(`${response.reason} View the section <a href="${response.location}" target="_blank" rel="noopener noreferrer">here</a>.`, 'failure', 0);
          }
        },
        keep_disabled: true,
        require_data_true: true,
      });
    }
  }

  /**
   * Edit section.
   */
  private async edit(): Promise<void> {
    if (this.edit_section_form) {
      //Get the `submit` button
      const button = this.edit_section_form.querySelector<HTMLButtonElement>('button[type=submit]');
      //Get form data
      const form_data = new FormData(this.edit_section_form);
      //Check if the custom icon is being attached
      const icon = this.edit_section_form.querySelector<HTMLInputElement>('input[type=file]');
      if (icon?.files?.[0]) {
        form_data.append('section_data[icon]', 'true');
      } else {
        form_data.append('section_data[icon]', 'false');
      }
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/talks/sections/${(form_data.get('section_data[section_id]') ?? '0') as string}`,
        form_data,
        method: 'PATCH',
        button,
        /**
         * Further processing on success.
         * @param response - Response from API endpoint.
         */
        onSuccess: (response) => {
          if (response.data === true) {
            void new Snackbar('Section updated. Reloading...', 'success');
            pageRefresh();
          } else if (response.location) {
            void new Snackbar(`${response.reason} View the section <a href="${response.location}" target="_blank" rel="noopener noreferrer">here</a>.`, 'failure', 0);
          }
        },
        keep_disabled: true,
        require_data_true: true,
      });
    }
  }

  /**
   * Delete section.
   */
  private async delete(): Promise<void> {
    if (this.delete_section_form && confirm('This is the last chance to back out.\nIf you press \'OK\' this section will be permanently deleted.\nPress \'Cancel\' to cancel the action.')) {
      //Get the `submit` button
      const button = this.delete_section_form.querySelector<HTMLButtonElement>('button[type=submit]');
      //Get form data
      const form_data = new FormData(this.delete_section_form);
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/talks/sections/${(form_data.get('section_data[section_id]') ?? '0') as string}`,
        form_data,
        method: 'DELETE',
        button,
        /**
         * Further processing on success.
         * @param response - Response from API endpoint.
         */
        onSuccess: (response) => {
          void new Snackbar('Section removed. Redirecting to parent...', 'success');
          pageRefresh(response.location);
        },
        keep_disabled: true,
      });
    }
  }

  /**
   * Create a new thread.
   */
  private async addThread(): Promise<void> {
    if (this.add_thread_form) {
      //Get the `submit` button
      const button = this.add_thread_form.querySelector<HTMLButtonElement>('button[type=submit]');
      //Get form data
      const form_data = new FormData(this.add_thread_form);
      //Check if the custom icon is being attached
      const og_image = this.add_thread_form.querySelector<HTMLInputElement>('input[type=file]');
      if (og_image?.files?.[0]) {
        form_data.append('thread_data[og_image]', 'true');
      } else {
        form_data.append('thread_data[og_image]', 'false');
      }
      //Add time zone
      form_data.append('thread_data[timezone]', TIMEZONE);
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/talks/threads`,
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
            const textarea = this.add_thread_form?.querySelector<HTMLTextAreaElement>('textarea');
            if (textarea && !empty(textarea.id)) {
              await saveTinyMCE(textarea.id);
            }
            pageRefresh(response.location);
          } else if (response.location) {
            void new Snackbar(`${response.reason} View the thread <a href="${response.location}" target="_blank" rel="noopener noreferrer">here</a>.`, 'failure', 0);
          }
        },
        keep_disabled: true,
        require_data_true: true,
      });
    }
  }
}
