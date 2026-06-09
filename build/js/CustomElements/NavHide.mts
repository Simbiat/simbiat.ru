/**
 * @file Custom logic for `nav-hide` element.
 */
import type { HTMLNavElement } from '../Common/Aliases.mts';

/**
 * Custom logic for `nav-hide` element.
 */
export class NavHide extends HTMLElement {
  public constructor() {
    super();
    this.addEventListener('click', () => {
      document.querySelector<HTMLNavElement>('#navigation')
              ?.classList
              .remove('flex');
    });
  }
}
