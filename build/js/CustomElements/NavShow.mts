/**
 * @file Custom logic for `nav-show` element.
 */
import type { HTMLNavElement } from '../Common/Aliases.mts';

/**
 * Custom logic for `nav-show` element.
 */
export class NavShow extends HTMLElement {
  public constructor() {
    super();
    this.addEventListener('click', () => {
      document.querySelector<HTMLNavElement>('#navigation')
              ?.classList
              .add('flex');
    });
  }
}
