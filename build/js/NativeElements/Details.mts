/**
 * @file Customization of the native details elements.
 */
import type { HTMLSummaryElement } from '../Common/Aliases.mts';
import { empty } from '../Common/Helpers.mts';

/**
 * Customization of the native details elements.
 */
export class Details {
  private static readonly detailsOutsideClickHandlers = new Map<HTMLDetailsElement, (event: MouseEvent) => void>();

  /**
   * Get all details tags.
   * Tags with the "persistent" class are excluded since they are meant to be open indefinitely, unless the user clicks to close.
   * Tags with "spoiler" and "adult" classes are excluded, since once you click to reveal them, the summary tag is hidden to prevent closure.
   */
  private static getAllDetailsTags(): NodeListOf<HTMLDetailsElement> {
    return document.querySelectorAll<HTMLDetailsElement>('details:not(.persistent):not(.spoiler):not(.adult)');
  }

  /**
   * Function to handle closure of all details tags except the one that we click to open.
   * @param target - Element to keep open.
   */
  private static closeAllDetailsTags(target: HTMLDetailsElement): void {
    const details = target.parentElement;
    if (details && (details as HTMLDetailsElement).open) {
      for (const tag of this.getAllDetailsTags()) {
        if (tag !== details) {
          tag.open = false;
        }
      }
    }
  }

  /**
   * Close details tag when clicking outside it.
   * @param initial_event - Initial event that was triggered.
   * @param details - Details element.
   */
  private static readonly clickOutsideDetailsTags = (
    initial_event: MouseEvent,
    details: HTMLDetailsElement,
  ): void => {
    if (details !== initial_event.target && !details.contains(initial_event.target as HTMLElement)) {
      details.open = false;
      const handler = this.detailsOutsideClickHandlers.get(details);
      if (handler) {
        document.removeEventListener('click', handler);
        this.detailsOutsideClickHandlers.delete(details);
      }
    }
  };

  /**
   * Close all details elements.
   * @param target - Original element that was clicked.
   */
  private static resetDetailsTags(target: HTMLDetailsElement): void {
    const clicked_details = target.parentElement;
    for (const details of this.getAllDetailsTags()) {
      if (details.open && details !== clicked_details && !details.contains(clicked_details)) {
        details.open = false;
        // Avoid registering a duplicate listener if one is already tracked.
      } else if (details.classList.contains('popup') && !this.detailsOutsideClickHandlers.has(details)) {
        /**
         * Create handler.
         * @param event - Pass click event.
         */
        const handler = (event: MouseEvent): void => {
          this.clickOutsideDetailsTags(event, details);
        };
        this.detailsOutsideClickHandlers.set(details, handler);
        document.addEventListener('click', handler);
      }
    }
  }

  /**
   * Function to toggle open status for `details` tags that use a separate button instead of `summary`.
   * @param button - Button that was clicked.
   */
  public static toggleDetailsButton(button: HTMLButtonElement): void {
    const details_id = button.getAttribute('data-details-id');
    if (details_id !== null && !empty(details_id)) {
      const details = document.querySelector<HTMLDetailsElement>(`#${details_id}`);
      if (details) {
        if (details.open) {
          details.open = false;
          button.focus();
        } else {
          details.open = true;
          details.focus();
        }
      }
    }
  }

  /**
   * Apply customizations.
   * @param details - Details element to customize.
   */
  public static init(details: HTMLDetailsElement): void {
    if (!details.classList.contains('persistent') && !details.classList.contains('spoiler') && !details.classList.contains('adult')) {
      // Attach listener for clicks. Technically, we can (and probably should) use 'toggle', but I was not able to achieve consistent behavior with it.
      const summary = details.querySelector<HTMLSummaryElement>('summary');
      if (summary) {
        summary.addEventListener('click', (event) => {
          this.closeAllDetailsTags(event.target as HTMLDetailsElement);
          this.resetDetailsTags(event.target as HTMLDetailsElement);
        });
      }
    }
  }
}
