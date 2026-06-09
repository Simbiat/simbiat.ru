/**
 * @file Handle scripts on the profile page.
 */
import { Ajax } from 'Common/Ajax.mts';
import { TIME_MANAGER } from 'Common/Constants.mts';
import { empty } from 'Common/Helpers.mts';
import { Snackbar } from 'Common/Snackbar.mts';
import { saveTinyMCE } from 'Common/TinyMCE.mts';
import { Form } from 'NativeElements/Form.mts';

/**
 * Handle scripts on the profile page.
 */
export class EditProfile {
  private readonly profile_auto_save = 10000;
  private readonly username_form = document.querySelector<HTMLFormElement>('#profile_username');
  private readonly username_submit = document.querySelector<HTMLButtonElement>('#username_submit');
  private readonly username_field = document.querySelector<HTMLInputElement>('#username_value');
  private readonly profile_form = document.querySelector<HTMLFormElement>('#profile_details');
  private readonly profile_submit = document.querySelector<HTMLButtonElement>('#details_submit');
  private readonly about_value = document.querySelector<HTMLTextAreaElement>('#about_value');
  private readonly auto_time = document.querySelector<HTMLParagraphElement>('#last_auto_save');
  private readonly time_tag: HTMLTimeElement | null = null;
  private profile_form_data = '';
  private timeout: number | null = null;

  public constructor() {
    if (this.auto_time) {
      this.time_tag = this.auto_time.querySelector<HTMLTimeElement>('time');
    }
    if (this.username_form) {
      for (const event_type of ['focus', 'change', 'input']) {
        if (this.username_field) {
          this.username_field.addEventListener(event_type, this.usernameOnChange.bind(this));
        }
      }
      this.usernameOnChange();
      Form.submitIntercept(this.username_form, () => {
        void this.username();
      });
    }
    if (this.profile_form) {
      //Save initial values
      this.profile_form_data = JSON.stringify([...new FormData(this.profile_form).entries()]);
      this.profileOnChange();
      //Monitor changes in all fields of the form
      for (const element_type of ['select', 'textarea', 'input']) {
        for (const element of this.profile_form.querySelectorAll<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>(element_type)) {
          for (const event_type of ['focus', 'change', 'input']) {
            element.addEventListener(event_type, this.profileOnChange.bind(this));
          }
        }
      }
      Form.submitIntercept(this.profile_form, () => {
        void this.profile();
      });
    }
  }

  /**
   * Submit profile update.
   * @param auto - Flag indicating auto save.
   */
  private async profile(auto = false): Promise<void> {
    if (this.profile_form) {
      //Get form data
      const form_data = new FormData(this.profile_form);
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/uc/profile`,
        form_data,
        method: 'PATCH',
        /**
         * Further processing on success.
         */
        onSuccess: async () => {
          this.profile_form_data = JSON.stringify([...form_data.entries()]);
          this.profileOnChange();
          void new Snackbar('Profile updated', 'success');
          //If auto-save, update the time
          if (auto) {
            this.auto_time?.classList.remove('hidden');
            if (this.time_tag) {
              const time = new Date();
              this.time_tag.setAttribute('datetime', time.toISOString());
              this.time_tag.textContent = time.toLocaleTimeString();
            }
          }
          //Notify TinyMCE that data was saved
          if (this.about_value && !empty(this.about_value.id)) {
            await saveTinyMCE(this.about_value.id);
          }
        },
      });
    }
  }

  /**
   * Disable or enable the button depending on whether there were changes in the form.
   */
  private profileOnChange(): void {
    if (this.profile_form && this.profile_submit) {
      if (this.timeout !== null) {
        TIME_MANAGER.clearTimeout(this.timeout);
      }
      const form_data = new FormData(this.profile_form);
      //Comparing stringify versions of data, because FormData === FormData always returns false
      this.profile_submit.disabled = this.profile_form_data === JSON.stringify([...form_data.entries()]);
      if (!this.profile_submit.disabled) {
        //Schedule auto save
        this.timeout = TIME_MANAGER.setTimeout(() => {
          void this.profile(true);
        }, this.profile_auto_save, {
          mode: 'realtime',
          executionPolicy: 'visible',
        });
      }
    }
  }

  /**
   * Disable or enable the button for username change, depending on its value.
   */
  private usernameOnChange(): void {
    if (this.username_field && this.username_submit) {
      this.username_submit.disabled = this.username_field.getAttribute('data-original') === this.username_field.value;
    }
  }

  /**
   * Change username.
   */
  private async username(): Promise<void> {
    if (this.username_form && this.username_submit) {
      //Get form data
      const form_data = new FormData(this.username_form);
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/uc/username`,
        form_data,
        method: 'PATCH',
        button: this.username_submit,
        /**
         * Further processing on success.
         */
        onSuccess: () => {
          if (this.username_field) {
            this.username_field.setAttribute('data-original', this.username_field.value);
          }
          this.usernameOnChange();
          void new Snackbar('Username changed', 'success');
        },
      });
    }
  }
}
