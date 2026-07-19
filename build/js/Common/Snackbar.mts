/**
 * @file Snackbars are small pop-up messages at the bottom of the screen.
 */
import { Sanitize } from 'Common/Sanitize.mts';
import DOMPurify from 'dompurify';
import { TIME_MANAGER } from './Constants.mts';

export const SNACKBAR_FAIL_TIMEOUT = 10000;

/**
 * Class to handle snackbar messages.
 */
export class Snackbar {
  private readonly container = document.querySelector<HTMLDivElement>('#snackbar_container');
  private readonly snack: HTMLDialogElement | null = null;
  private readonly closure_timeout: number | null = null;
  private readonly default_timeout = 3000;

  /**
   * Create a snackbar message.
   * @param text - Text of the message.
   * @param color - Optional color class to apply. Defaults to `success`.
   * @param milliseconds - For how long the message should stay on screen. `0` means it will persist indefinitely.
   */
  public constructor(text: string, color = 'success', milliseconds = this.default_timeout) {
    const template = document.querySelector<HTMLTemplateElement>('#snackbar_template');
    if (this.container && template) {
      this.container.setAttribute('popover', 'manual');
      this.container.showPopover();
      //Generate element
      const new_snack = template.content.cloneNode(true) as DocumentFragment;
      this.snack = new_snack.querySelector<HTMLDialogElement>('dialog');
      if (this.snack !== null) {
        //Add text
        const text_block = this.snack.querySelector<HTMLSpanElement>('.snack_text');
        if (text_block !== null) {
          // noinspection InnerHTMLJS
          text_block.innerHTML = DOMPurify.sanitize(text, Sanitize.PURIFY_CONFIG);
        }
        // Get close button
        const close_button = this.snack.querySelector<HTMLButtonElement>('.snack_close');
        if (close_button) {
          close_button.addEventListener('click', this.close.bind(this));
          // Update milliseconds for auto-closure
          close_button.setAttribute('data-close-in', String(milliseconds));
          // Attach listener for auto-closure
          if (milliseconds > 0) {
            this.closure_timeout = TIME_MANAGER.setTimeout(() => {
              this.startClosure();
            }, milliseconds, {
              mode: 'active',
              executionPolicy: 'focused',
            });
          }
        }
        //Add class for color
        if (color) {
          this.snack.classList.add(color);
        }
        //Add the element to the parent
        this.container.appendChild(this.snack);
        this.snack.show();
      }
    } else {
      // This is a failback, which normally should not happen
      // eslint-disable-next-line no-console
      console.log(text);
      window.alert(text);
    }
  }

  /**
   * Initiate closure of the snackbar, triggering animation.
   */
  private startClosure(): void {
    //Animate removal
    if (this.snack) {
      this.snack.classList.remove('fade_in');
      this.snack.classList.add('fade_out');
      //Actual removal
      this.snack.addEventListener('animationend', () => {
        this.close();
      }, {
        once: true,
      });
    }
  }

  /**
   * Actually close the snackbar.
   */
  private close(): void {
    if (this.snack) {
      this.snack.close();
      if (this.closure_timeout !== null) {
        TIME_MANAGER.clearTimeout(this.closure_timeout);
      }
      if ((this.container?.contains(this.snack)) === true) {
        this.container.removeChild(this.snack);
        // Remove container from top layer if there are no more snacks in it
        if (this.container.querySelectorAll<HTMLDialogElement>('.snackbar').length === 0) {
          this.container.hidePopover();
          this.container.removeAttribute('popover');
        }
      }
    }
  }
}
