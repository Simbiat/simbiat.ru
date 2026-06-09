/**
 * @file Customization of the native input elements.
 */
import { empty, getMeta, urlCleanString } from '../Common/Helpers.mts';
import { Snackbar } from '../Common/Snackbar.mts';
import { Input } from './Input.mts';

/**
 * Customization of the native input elements.
 */
export class Form {
  /**
   * Intercept submit event and presses on Enter keys.
   * @param event - Event to process.
   */
  private static readonly submitDefaultIntercept = (event: KeyboardEvent | SubmitEvent): boolean => {
    if (event.type === 'submit' || (event.type === 'keydown' && ((event as KeyboardEvent).code === 'Enter' || (event as KeyboardEvent).code === 'NumpadEnter'))) {
      event.preventDefault();
      event.stopPropagation();
      void new Snackbar('Default form events are blocked', 'warning');
      return false;
    }
    return true;
  };

  /**
   * Split pasted text among input fields in a form.
   * @param event - Clipboard event to process.
   */
  private static pasteSplit(event: ClipboardEvent): void {
    const original_string = event.clipboardData?.getData('text/plain');
    event.preventDefault();
    event.stopImmediatePropagation();
    if (typeof original_string === 'undefined') {
      return;
    }
    let buffer = original_string;
    //Get initial element
    let current = event.target;
    if (current === null) {
      //If somehow we got here, exit early
      return;
    }
    //If we are pasting into the URL field, clean it
    if ((current as HTMLInputElement).getAttribute('type') === 'url') {
      buffer = urlCleanString(buffer);
    }
    //Exit if the current field has a value already
    if ((current as HTMLInputElement).value && ((current as HTMLInputElement).selectionStart !== 0 || (current as HTMLInputElement).selectionEnd !== (current as HTMLInputElement).value.length)) {
      Input.pasteAndMove((current as HTMLInputElement), buffer);
      return;
    }
    //Get the initial length attribute
    let max_length = parseInt((current as HTMLInputElement).getAttribute('maxlength') ?? '0', 10);
    //Loop while the buffer is too large
    while (current !== null && max_length && buffer.length > max_length) {
      //Ensure the input value is updated
      Input.pasteAndMove((current as HTMLInputElement), buffer.substring(0, max_length));
      //Trigger the input event to bubble any bound events
      current.dispatchEvent(new Event('input', {
        bubbles: true,
        cancelable: true,
      }));
      //Do not spill over if a field is invalid
      if (!(current as HTMLInputElement).validity.valid) {
        return;
      }
      //Update buffer value (not the buffer itself)
      buffer = buffer.substring(max_length);
      //Stop spilling if data-no-spill is set
      if ((current as HTMLInputElement).getAttribute('data-no-spill') !== null) {
        return;
      }
      //Get the next node
      current = Input.nextInput((current as HTMLInputElement), false);
      if (current) {
        //Exit if the next field has something in it already
        if ((current as HTMLInputElement).value) {
          return;
        }
        //Focus to provide visual identification of a switch
        (current as HTMLInputElement).focus();
        //Update max_length
        max_length = parseInt((current as HTMLInputElement).getAttribute('maxlength') ?? '0', 10);
      }
    }
    //Check if we still have a valid node
    if (current) {
      //Dump everything we can from leftovers
      Input.pasteAndMove((current as HTMLInputElement), buffer);
      //Trigger the input event to bubble any bound events
      current.dispatchEvent(new Event('input', {
        bubbles: true,
        cancelable: true,
      }));
    }
  }

  /**
   * Track backspace and focus the previous input field if input is empty, when it's pressed.
   * @param event - Event to react to.
   */
  private static inputBackSpace(event: Event): void {
    const current = event.target as HTMLInputElement;
    if ((event as KeyboardEvent).code === 'Backspace' && !current.value) {
      const move_to = Input.nextInput(current, true);
      if (move_to) {
        move_to.focus();
        // Ensure, that cursor ends up at the end of the previous field
        move_to.selectionEnd = move_to.value.length;
        move_to.selectionStart = move_to.value.length;
      }
    }
  }

  /**
   * Focus the next field if the current is filled to the brim and valid.
   * @param event - Event to react to.
   */
  private static autoNext(event: Event): void {
    const current = event.target as HTMLInputElement;
    // Get length attribute
    const max_length = parseInt(current.getAttribute('maxlength') ?? '0', 10);
    // Check it against value length
    if (max_length && current.value.length === max_length && current.validity.valid) {
      const move_to = Input.nextInput(current, false);
      if (move_to) {
        move_to.focus();
      }
    }
  }

  /**
   * Apply customizations.
   * @param form - Form element to customize.
   */
  public static init(form: HTMLFormElement): void {
    if (!form.hasAttribute('data-intercepted') || form.getAttribute('data-intercepted') !== 'true') {
      form.addEventListener('submit', this.submitDefaultIntercept);
      form.addEventListener('keydown', this.submitDefaultIntercept);
    }
    //For all elements that can be used inside a form add name if it's missing. Make it equal to ID.
    for (const item of form.querySelectorAll<HTMLElement>('button, datalist, fieldset, input, meter, progress, select, textarea')) {
      if (!item.hasAttribute('data-noname')
        && (
          !item.hasAttribute('name') || empty(item.getAttribute('name'))
        )
        && !empty(item.id)
      ) {
        item.setAttribute('name', item.id);
      }
    }
    //List of input types that are "textual" by default, thus can be tracked through keydown and paste events. In essence, these are types that support the maxlength attribute.
    // Below is a list of input types that make little sense to be tracked through keydown or paste events, in case it will be required at some time:
    // 1. checkbox, color, file, number, radio are not textual.
    // 2. date, datetime-local, time, month, week may fall back to textual fields, but you can't predict this by checking the browser version.
    // 3. hidden since it's hidden.
    // 4. image since its purpose is unclear by default.
    // 5. range since unclear how to track to actually determine that user stopped interaction,
    // 6. reset and submit are excluded due to their purpose.
    for (const item of form.querySelectorAll<HTMLInputElement>('input[type="email" i], input[type="password" i], input[type="search" i], input[type="tel" i], input[type="text" i], input[type="url" i]')) {
      item.addEventListener('keydown', (event) => {
        this.inputBackSpace(event);
      });
      if (!empty(item.getAttribute('maxlength'))) {
        for (const event_type of ['change', 'input']) {
          item.addEventListener(event_type, (event) => {
            this.autoNext(event);
          });
        }
        item.addEventListener('paste', (event) => {
          this.pasteSplit(event);
        });
      }
    }
  }

  /**
   * Register a search form that will be treated by default browser events processing logic.
   */
  public static searchForm(): void {
    const form = document.querySelector<HTMLFormElement>('form.search_form');
    if (form) {
      form.removeEventListener('submit', this.submitDefaultIntercept);
      form.removeEventListener('keydown', this.submitDefaultIntercept);
      form.setAttribute('data-intercepted', 'true');
    }
  }

  /**
   * Prevent double form submission and submission by determined bots.
   * @param event - Event to react to.
   * @param form - Form element.
   * @param callable - Function to execute.
   */
  private static async runGuarded(event: KeyboardEvent | SubmitEvent, form: HTMLFormElement, callable: () => Promise<void> | void): Promise<void> {
    event.preventDefault();
    event.stopPropagation();
    if (form.hasAttribute('data-submitting')) {
      return;
    }
    const is_bot = !empty(getMeta('is_bot'));
    if (is_bot) {
      void new Snackbar('No form submissions are allowed for bots', 'error');
    } else {
      form.setAttribute('data-submitting', 'true');
      try {
        await callable();
      } finally {
        form.removeAttribute('data-submitting');
      }
    }
  }

  /**
   * Intercept submit events (pure `submit` and Enter/NumpadEnter keydown events) to use custom callable, which is called through `runGuarded` wrapper.
   * @param form - Form element.
   * @param callable - Function to attach.
   */
  public static submitIntercept(form: HTMLFormElement, callable: () => Promise<void> | void): void {
    form.removeEventListener('submit', this.submitDefaultIntercept);
    form.removeEventListener('keydown', this.submitDefaultIntercept);
    form.setAttribute('data-intercepted', 'true');

    form.addEventListener('submit', (event: SubmitEvent) => {
      void this.runGuarded(event, form, callable);
    });

    form.addEventListener('keydown', (event: KeyboardEvent) => {
      if (event.code === 'Enter' || event.code === 'NumpadEnter') {
        void this.runGuarded(event, form, callable);
      }
    });
  }
}
