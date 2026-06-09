/**
 * @file Custom logic for the `time-r` element.
 */
import { TIME_MANAGER } from '../Common/Constants.mts';
import { pageRefresh } from '../Common/Helpers.mts';

/**
 * Custom logic for the `time-r` element.
 */
export class TimeR extends HTMLElement {
  private readonly interval: number | null = null;

  public constructor() {
    super();
    this.interval = TIME_MANAGER.setInterval(() => {
      const data_increase = Boolean(this.getAttribute('data-increase') ?? false);
      if (parseInt(this.textContent, 10) > 0 || this.hasAttribute('data-negative')) {
        if (data_increase) {
          this.textContent = String(parseInt(this.textContent, 10) + 1);
        } else {
          this.textContent = String(parseInt(this.textContent, 10) - 1);
        }
      } else {
        TIME_MANAGER.clearInterval(Number(this.interval));
        if (this.id === 'refresh_timer') {
          pageRefresh();
        }
      }
    }, 1000, {
      mode: 'active',
      executionPolicy: 'visible',
    });
  }

  /**
   * Initial processing when the element is added to DOM.
   */
  public connectedCallback(): void {
    this.role = 'timer';
  }

  // noinspection JSUnusedGlobalSymbols https://youtrack.jetbrains.com/issue/WEB-55981
  /**
   * De-initialize the element on removal from DOM.
   */
  public disconnectedCallback(): void {
    TIME_MANAGER.clearInterval(Number(this.interval));
  }
}
