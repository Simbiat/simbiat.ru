/**
 * @file Handle scripts on the avatar management page.
 */
import { basename } from 'Common/Helpers.mts';
import { Ajax } from 'Common/Ajax.mts';
import { Snackbar, SNACKBAR_FAIL_TIMEOUT } from 'Common/Snackbar.mts';
import { Form } from 'NativeElements/Form.mts';

/**
 * Handle scripts on the avatar management page.
 */
export class EditAvatars {
  private readonly form = document.querySelector<HTMLFormElement>('#profile_avatar');
  private readonly current_avatar = document.querySelector<HTMLImageElement>('#current_avatar');
  private readonly sidebar_avatar = document.querySelector<HTMLImageElement>('#sidebar_avatar');
  private readonly avatar_file = document.querySelector<HTMLInputElement>('#profile_avatar_file');
  private readonly avatars_list = document.querySelector<HTMLUListElement>('#avatars_list');
  private readonly template = document.querySelector<HTMLTemplateElement>('#avatar_item');

  public constructor() {
    //Attach form listener
    if (this.form) {
      Form.submitIntercept(this.form, () => {
        void this.upload();
      });
    }
    this.listen();
  }

  /**
   * Attach listeners.
   */
  private listen(): void {
    //Listen to avatar change
    for (const item of document.querySelectorAll<HTMLInputElement>('input[id^="avatar_"]')) {
      item.addEventListener('change', (event: Event) => {
        void this.setActive(event.target as HTMLInputElement);
      });
    }
    //Listen to avatar deletion
    for (const item of document.querySelectorAll<HTMLButtonElement>('button[id^="del_"]')) {
      item.addEventListener('click', (event: Event) => {
        void this.delete(event.target as HTMLInputElement);
      });
    }
  }

  /**
   * Upload a new avatar.
   */
  private async upload(): Promise<void> {
    if (this.form) {
      if (this.avatar_file?.files?.length === 0) {
        void new Snackbar('No file selected', 'failure', SNACKBAR_FAIL_TIMEOUT);
        return;
      }
      if (this.avatar_file?.files?.[0]?.size === 0) {
        void new Snackbar('Selected file is empty', 'failure', SNACKBAR_FAIL_TIMEOUT);
        return;
      }
      //Get form data
      const form_data = new FormData(this.form);
      const button = this.form.querySelector<HTMLButtonElement>('#avatar_submit');
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/uc/avatars/add`,
        form_data,
        method: 'POST',
        button,
        /**
         * Further processing on success.
         * @param response - Response from API endpoint.
         */
        onSuccess: (response) => {
          this.addToList(response.location);
        },
      });
      //Remove file and preview
      if (this.avatar_file) {
        this.avatar_file.value = '';
        this.avatar_file.dispatchEvent(new Event('change'));
      }
    }
  }

  /**
   * Change current avatar.
   * @param avatar - Avatar to set as current one.
   */
  private async setActive(avatar: HTMLInputElement): Promise<void> {
    //Get li element
    const li = avatar.parentElement?.closest<HTMLLIElement>('li');
    if (li) {
      const form_data = new FormData();
      form_data.append('avatar', (li as HTMLElement).id);
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/uc/avatars/setactive`,
        form_data,
        method: 'PATCH',
        /**
         * Further processing on success.
         * @param response - Response from API endpoint.
         */
        onSuccess: (response) => {
          //Update avatar on page
          this.refresh(response.location);
        },
        /**
         * Further processing on error.
         */
        onError: () => {
          if (avatar.checked) {
            avatar.checked = false;
          }
        },
      });
    }
  }

  /**
   * Function to refresh the current avatar on the page without reloading.
   * @param avatar - Path to avatar.
   */
  private refresh(avatar: string): void {
    const file_hash = basename(avatar);
    if (this.avatars_list) {
      for (const item of this.avatars_list.querySelectorAll<HTMLLIElement>('li')) {
        const radio = item.querySelector<HTMLInputElement>('input[id^=avatar_]');
        const close = item.querySelector<HTMLButtonElement>('button[id^=del_]');
        //Deselect all nodes, that are not current one
        if (radio && close) {
          if (item.id === file_hash) {
            radio.checked = true;
            //Remove "delete" button
            close.classList.add('hidden');
            close.disabled = true;
          } else {
            radio.checked = false;
            //Show "delete" button
            close.classList.remove('hidden');
            close.disabled = false;
          }
        }
      }
      if (this.current_avatar) {
        this.current_avatar.src = avatar;
      }
      if (this.sidebar_avatar) {
        this.sidebar_avatar.src = avatar;
      }
    }
  }

  /**
   * Function to add avatar to the list.
   * @param avatar - Path to avatar.
   */
  private addToList(avatar: string): void {
    const hash = basename(avatar);
    if (this.template) {
      //Create a list item
      const clone = this.template.content.cloneNode(true) as HTMLElement;
      //Set ID for the item
      const li = clone.querySelector<HTMLLIElement>('li');
      if (li) {
        li.id = hash;
      }
      //Set attributes for inputs and attach listeners
      const inputs = clone.querySelectorAll<HTMLInputElement>('input');
      if (inputs[0]) {
        inputs[0].id = inputs[0].id.replace('hash', hash);
        inputs[0].addEventListener('change', (event: Event) => {
          void this.setActive(event.target as HTMLInputElement);
        });
      }
      if (inputs[1]) {
        inputs[1].id = inputs[1].id.replace('hash', hash);
        inputs[1].addEventListener('click', (event: MouseEvent) => {
          void this.delete(event.target as HTMLInputElement);
        });
      }
      //Update label
      const label = clone.querySelector<HTMLLabelElement>('label');
      if (label) {
        label.setAttribute('for', String(label.getAttribute('for'))
          .replace('hash', hash));
      }
      //Update image source
      const img = clone.querySelector<HTMLImageElement>('img');
      if (img) {
        img.src = avatar;
      }
      //Attach new item to the list
      if (this.avatars_list) {
        this.avatars_list.appendChild(clone);
      }
      //Update avatar on the page
      this.refresh(avatar);
    }
  }

  /**
   * Function to delete avatar.
   * @param avatar - Avatar element to remove.
   */
  private async delete(avatar: HTMLInputElement): Promise<void> {
    //Get li element
    const li = avatar.parentElement?.closest<HTMLLIElement>('li');
    if (li) {
      const form_data = new FormData();
      form_data.append('avatar', li.id);
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/uc/avatars/delete`,
        form_data,
        method: 'DELETE',
        /**
         * Further processing on success.
         * @param response - Response from API endpoint.
         */
        onSuccess: (response) => {
          //Delete the avatar from the list
          li.remove();
          //Update avatar on page
          this.refresh(response.location);
        },
      });
    }
  }
}
