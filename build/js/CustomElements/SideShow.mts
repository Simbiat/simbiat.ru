/**
 * @file Custom logic for `side-show` element.
 */
import type { SideHide } from './SideHide.mts';

/**
 * Custom logic for `side-hide` element.
 */
export class SideShow extends HTMLElement {
  private readonly side_hide = document.querySelector<SideHide>('side-hide');
  private sidebar: HTMLDialogElement | null = null;
  private button: HTMLButtonElement | null = null;

  /**
   * Initial processing when the element is added to DOM.
   */
  public connectedCallback(): void {
    this.button = this.querySelector<HTMLButtonElement>('button');
    if (this.id === 'prod_link') {
      if (this.button) {
        this.button.addEventListener('click', () => {
          window.open(encodeURI(document.location.href.replace('localhost', 'www.simbiat.eu')), '_blank');
        });
      }
    } else if (this.button && this.side_hide && this.hasAttribute('data-sidebar')) {
      this.sidebar = document.querySelector<HTMLDialogElement>(`#${String(this.getAttribute('data-sidebar'))}`);
      this.button.addEventListener('click', () => {
        this.sidebar?.showModal();
      });
    }
  }
}
