/**
 * @file Customization of the native dialog elements.
 */
export class Dialog {
  /**
   * Apply customizations.
   * @param dialog - Dialog element to customize.
   */
  public static init(dialog: HTMLDialogElement): void {
    if (dialog.classList.contains('modal')) {
      dialog.addEventListener('click', (event) => {
        const target = event.target;
        if (target && target === dialog) {
          dialog.close();
        }
      });
    }
  }
}
