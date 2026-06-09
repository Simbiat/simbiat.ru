/**
 * @file Customization of the native textarea elements.
 */
import { loadTinyMCE } from '../Common/TinyMCE.mts';

/**
 * Customization of the native textarea elements.
 */
export class Textarea {
  /**
   * Percent at which textarea is considered to be "close to the limit".
   */
  private static readonly TEXTAREA_CLOSE_TO_LIMIT = 75;

  /**
   * Apply customizations.
   * @param textarea - Textarea element to customize.
   */
  public static init(textarea: HTMLTextAreaElement): void {
    //Give elements a default placeholder if there is none
    if (!textarea.hasAttribute('placeholder')) {
      textarea.setAttribute('placeholder', textarea.value || textarea.type || 'placeholder');
    }
    if (textarea.maxLength > 0) {
      //Attach listener
      for (const event_type of ['change', 'keydown', 'keyup', 'input']) {
        textarea.addEventListener(event_type, (event) => {
          this.countInTextarea(event.target as HTMLTextAreaElement);
        });
      }
      //Call to set the initial value
      this.countInTextarea(textarea);
    }
    if (textarea.classList.contains('tinymce') && textarea.id) {
      void loadTinyMCE(textarea.id);
    }
  }

  /**
   * Function to count characters inside textarea elements and update their respective labels.
   * @param textarea - Textarea to count in.
   */
  private static countInTextarea(textarea: HTMLTextAreaElement): void {
    if (textarea.labels[0] && textarea.maxLength) {
      const label = textarea.labels[0];
      label.setAttribute('data-curlength', `(${textarea.value.length}/${textarea.maxLength}ch)`);
      label.classList.remove('at_the_limit', 'close_to_limit');
      if (textarea.value.length >= textarea.maxLength) {
        label.classList.add('at_the_limit');
      } else if (((100 * textarea.value.length) / textarea.maxLength) >= this.TEXTAREA_CLOSE_TO_LIMIT) {
        label.classList.add('close_to_limit');
      }
    }
  }
}
