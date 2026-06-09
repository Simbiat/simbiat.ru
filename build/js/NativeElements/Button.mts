/**
 * @file Customization of the native button elements.
 */
import { empty } from '../Common/Helpers.mts';
import { Details } from './Details.mts';

/**
 * Customization of the native button elements.
 */
export class Button {
  /**
   * Apply customizations.
   * @param button - Button element to customize.
   */
  public static init(button: HTMLButtonElement): void {
    // Default value if type is missing or invalid is `submit`, but we want to avoid that, since `submit` implies form submission, which we may not want.
    const type = button.getAttribute('type');
    if (empty(type) || !['button', 'reset', 'submit'].includes(type ?? '')) {
      button.type = 'button';
    }
    if (button.classList.contains('toggle_details')) {
      button.addEventListener('click', () => {
        Details.toggleDetailsButton(button);
      });
    }
    const image = button.querySelector<HTMLImageElement>('img:only-child');
    /* Prevent selection of the image from keyboard */
    if (image) {
      image.tabIndex = -1;
    }
  }
}
