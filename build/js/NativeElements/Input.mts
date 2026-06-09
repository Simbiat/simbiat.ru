/**
 * @file Customization of the native input elements.
 */
import { empty, urlClean } from '../Common/Helpers.mts';
import { Snackbar, SNACKBAR_FAIL_TIMEOUT } from '../Common/Snackbar.mts';

/**
 * Customization of the native input elements.
 */
export class Input {
  /**
   * Get and show color in the attribute. For some reason, CSS's attr(value) does not show the value if I do not do this.
   * @param input_element - Element to process.
   */
  public static colorValue(input_element: HTMLInputElement): void {
    if (input_element.type === 'color') {
      input_element.setAttribute('value', input_element.value);
    }
  }

  /**
   * Find the next or previous input element.
   * @param initial - Element to start from.
   * @param reverse - Flag indicating that we need to go in reverse.
   */
  public static nextInput(initial: HTMLInputElement, reverse = false): HTMLInputElement | null {
    //Get form
    const form = initial.form;
    //Iterate textual inputs inside the form. Not using previousElementSibling, because next/previous input may not be a sibling on the same level
    if (form) {
      let previous;
      for (const move_to of form.querySelectorAll<HTMLInputElement>('input[type="email"], input[type="password"], input[type="search"], input[type="tel"], input[type="text"], input[type="url"]')) {
        if (reverse) {
          //Check if the current element in the loop is the initial one, meaning
          if (move_to === initial) {
            //If previous is not empty - share it. Otherwise - false, since initial input is first in the form
            if (previous) {
              return previous;
            }
            return null;
          }
          //If we are moving forward and the initial node is the previous one
        } else if (previous === initial) {
          return move_to;
        }
        //Update previous input
        previous = move_to;
      }
    }
    return null;
  }

  /**
   * Paste and move the cursor to the end of the field value. Essentially, this is meant to simulate the default paste event.
   * @param input - Element we initially paste into.
   * @param text - Text that we are pasting.
   */
  public static pasteAndMove(input: HTMLInputElement, text: string): void {
    if (input.selectionStart === null || input.selectionEnd === null) {
      return;
    }
    const start = input.selectionStart;
    const end = input.selectionEnd;
    const selected_length = end - start;
    const length_after_paste = input.value.length + text.length - selected_length;
    let new_text;
    //Ensure that we paste only up to the maximum length of the field
    if (input.maxLength && length_after_paste > input.maxLength) {
      new_text = text.substring(0, input.maxLength - input.value.length + selected_length);
    } else {
      new_text = text;
    }
    const new_cursor_position = start + new_text.length;
    //Insert text at the cursor position
    input.value = input.value.substring(0, start) + new_text + input.value.substring(end);
    //Move the cursor to the end of the inserted text
    input.setSelectionRange(new_cursor_position, new_cursor_position);
    //Scroll to cursor position
    input.scrollLeft = (input.scrollWidth / input.value.length) * new_cursor_position;
  }

  /**
   * Validate file names for inputs of type "file", to throw an error before sending them to server, where they will be blocked by SecRule 920120.
   * @param file_input - File input element.
   */
  private static inputFileValidate(file_input: HTMLInputElement): void {
    const files = file_input.files;
    const invalid_chars = /["';=]/gv;
    if (files !== null) {
      for (const file of files) {
        if (invalid_chars.test(file.name)) {
          file_input.value = '';
          void new Snackbar('File name contains one or more of the prohibited characters `\'`, `"`, `;`, or `=`. Please rename the file and try again.', 'failure', SNACKBAR_FAIL_TIMEOUT);
          return;
        }
      }
    }
  }

  /**
   * Apply customizations.
   * @param input - Input element to customize.
   */
  public static init(input: HTMLInputElement): void {
    //Add a placeholder if not present. Required more as a precaution for text-like inputs with no placeholder
    if (!input.hasAttribute('placeholder')) {
      input.setAttribute('placeholder', input.value || input.type || 'placeholder');
    }
    //Add missing type attribute to be explicit
    if (empty(input.getAttribute('type'))) {
      input.setAttribute('type', 'text');
    }
    if (empty(input.type)) {
      input.type = 'text';
    }
    if (input.getAttribute('type') === 'color') {
      for (const event_type of ['focus', 'change', 'input']) {
        input.addEventListener(event_type, () => {
          this.colorValue(input);
        });
      }
      this.colorValue(input);
    }
    if (input.getAttribute('type') === 'url' && !input.form) {
      input.addEventListener('paste', (event) => {
        urlClean(event);
      });
    }
    if (input.getAttribute('type') === 'file') {
      for (const event_type of ['change', 'input']) {
        input.addEventListener(event_type, () => {
          this.inputFileValidate(input);
        });
      }
    }
  }
}
