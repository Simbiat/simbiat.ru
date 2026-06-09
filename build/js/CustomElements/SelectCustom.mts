/**
 * Class for select with description and image for each option.
 * @file Custom logic for the `post-form` element.
 */
export class SelectCustom extends HTMLElement {
  private icon: HTMLImageElement | null = null;
  private select: HTMLSelectElement | null = null;
  private label: HTMLLabelElement | null = null;
  private description: HTMLDivElement | null = null;

  /**
   * Initial processing when the element is added to DOM.
   */
  public connectedCallback(): void {
    this.icon = this.querySelector<HTMLImageElement>('.select_icon');
    this.select = this.querySelector<HTMLSelectElement>('select');
    this.label = this.querySelector<HTMLLabelElement>('label');
    this.description = this.querySelector<HTMLDivElement>('.select_description');
    //Enforcing certain values of the items
    if (this.icon && this.label) {
      this.icon.alt = `Icon for ${this.label.textContent.charAt(0)
                                      .toLowerCase()}${this.label.textContent.slice(1)}`;
      this.icon.setAttribute('data-tooltip', this.icon.alt);
    }
    //Attach listener to the file upload field
    if (this.select) {
      this.select.addEventListener('change', () => {
        this.update();
      });
    }
    //Run initial update
    this.update();
  }

  /**
   * Function to update the preview of the image.
   */
  private update(): void {
    if (this.select) {
      const option = this.select[this.select.selectedIndex] as HTMLOptionElement;
      const description = option.getAttribute('data-description') ?? '';
      const icon = option.getAttribute('data-icon') ?? '';
      if (this.description) {
        if ((/^\s*$/v).test(description)) {
          this.description.classList.add('hidden');
        } else {
          this.description.textContent = description;
          this.description.classList.remove('hidden');
        }
      }
      if (this.icon) {
        if ((/^\s*$/v).test(icon)) {
          this.icon.classList.add('hidden');
        } else {
          this.icon.src = icon;
          this.icon.classList.remove('hidden');
        }
      }
    }
  }
}
