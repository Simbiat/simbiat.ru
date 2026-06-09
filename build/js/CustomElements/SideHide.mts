/**
 * @file Custom logic for `side-hide` element.
 */
export class SideHide extends HTMLElement {
  public constructor() {
    super();
    this.addEventListener('click', () => {
      this.closest<HTMLDialogElement>('dialog')
          ?.close();
    });
  }
}
