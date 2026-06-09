/**
 * @file Custom logic for the `tool-tip` element.
 */
import { empty } from '../Common/Helpers.mts';
import { Sanitize } from '../Common/Sanitize.mts';

/**
 * Custom logic for the `tool-tip` element.
 */
export class ToolTip extends HTMLElement {
  private x = 0;
  private y = 0;
  private width = 0;
  private height = 0;
  private readonly one_and_half_offset = 1.5;
  private readonly two_offset = 2;
  private long_press_timer: ReturnType<typeof setTimeout> | null = null;
  private readonly long_press_delay = 500;

  /**
   * Initial processing when the element is added to DOM.
   */
  public connectedCallback(): void {
    // Handle tooltip positioning for mouse hover
    document.addEventListener('pointermove', this.on_pointer_move);
    // Handle focus
    document.addEventListener('focus', this.on_focus);
    // Handle touch events
    document.addEventListener('touchstart', this.on_touch_start, {
      passive: true,
    });
    document.addEventListener('touchend', this.cancel_long_press, {
      passive: true,
    });
    document.addEventListener('touchmove', this.cancel_long_press, {
      passive: true,
    });
  }

  // noinspection JSUnusedGlobalSymbols https://youtrack.jetbrains.com/issue/WEB-55981
  /**
   * De-initialize the element on removal from DOM.
   */
  public disconnectedCallback(): void {
    document.removeEventListener('pointermove', this.on_pointer_move);
    document.removeEventListener('focus', this.on_focus);
    document.removeEventListener('touchstart', this.on_touch_start);
    document.removeEventListener('touchend', this.cancel_long_press);
    document.removeEventListener('touchmove', this.cancel_long_press);
  }

  /**
   * Handle pointer events.
   * @param event - Event to process.
   */
  private readonly on_pointer_move = (event: PointerEvent): void => {
    this.update(event.target as HTMLElement);
    this.width = Math.max(event.width, 10);
    this.height = Math.max(event.height, 10);
    this.x = event.clientX + this.width;
    this.y = event.clientY - this.height;
    //Get block dimensions
    this.tooltipCursor();
  };

  /**
   * Handle focus events.
   * @param event - Event to process.
   */
  private readonly on_focus = (event: Event): void => {
    this.update(event.target as HTMLElement);
    const coordinates = (event.target as HTMLElement).getBoundingClientRect();
    this.x = coordinates.x + this.width;
    this.y = coordinates.y - (this.offsetHeight * this.one_and_half_offset);
    this.tooltipCursor();
  };

  /**
   * Handle touch start events.
   * @param event - Event to process.
   */
  private readonly on_touch_start = (event: TouchEvent): void => {
    const touch = event.touches[0];
    const target = event.target as HTMLElement;
    if (touch) {
      this.x = touch.clientX;
      this.y = touch.clientY - (this.offsetHeight * this.one_and_half_offset);
      this.long_press_timer = setTimeout(() => {
        this.update(target, true);
        this.tooltipCursor();
        document.addEventListener('touchstart', this.onOutsideTap.bind(this), {
          passive: true,
          once: true,
        });
      }, this.long_press_delay);
    }
  };

  /**
   * Cancel long press.
   */
  private readonly cancel_long_press = (): void => {
    if (this.long_press_timer !== null) {
      clearTimeout(this.long_press_timer);
      this.long_press_timer = null;
    }
  };

  /**
   * Handle touch outside the tooltip.
   * @param event - Event to process.
   */
  private onOutsideTap(event: TouchEvent): void {
    // If the tap landed on a tooltip-bearing element, let its own touchstart handle it
    if (!(event.target as HTMLElement).closest<HTMLElement>('[data-tooltip]')) {
      this.removeAttribute('data-tooltip');
      this.textContent = '';
      this.hidePopover();
    }
  }

  /**
   * Update "cursor" position data in document style.
   */
  private tooltipCursor(): void {
    if (this.y + this.offsetHeight > window.innerHeight) {
      this.y = window.innerHeight - (this.offsetHeight * this.two_offset);
    }
    if (this.x + this.offsetWidth > window.innerWidth) {
      this.x = window.innerWidth - (this.offsetWidth * this.one_and_half_offset);
    }
    if (this.x - this.width < 0) {
      this.x = this.width;
    }
    if (this.y - this.height < 0) {
      this.y = this.height;
    }
    document.documentElement.style.setProperty('--cursor_x', `${this.x}px`);
    document.documentElement.style.setProperty('--cursor_y', `${this.y}px`);
  }

  /**
   * Update tooltip text.
   * @param element - Element we are hovering at.
   * @param coarse - Flag indicating whether this is from touch event or pointer.
   */
  private update(element: HTMLElement, coarse = false): void {
    const tooltip = element.getAttribute('data-tooltip')
      ?? element.parentElement?.getAttribute('data-tooltip')
      ?? element.getAttribute('alt')
      ?? element.getAttribute('title')
      ?? '';
    if (!empty(tooltip) && element !== this && (coarse || matchMedia('(pointer:fine)').matches)) {
      this.setAttribute('data-tooltip', 'true');
      //this.textContent = tooltip;
      this.replaceChildren(Sanitize.html(tooltip));
      this.showPopover();
    } else {
      this.removeAttribute('data-tooltip');
      this.textContent = '';
      this.hidePopover();
    }
  }
}
