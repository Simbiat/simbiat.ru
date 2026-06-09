/**
 * @file Custom logic for `image-upload` element.
 */
import { empty } from '../Common/Helpers.mts';

/**
 * Class for input type "file" with image preview.
 */
export class ImageUpload extends HTMLElement {
  private preview: HTMLImageElement | null = null;
  private file: HTMLInputElement | null = null;
  private label: HTMLLabelElement | null = null;

  /**
   * Initial processing when the element is added to DOM.
   */
  public connectedCallback(): void {
    this.preview = this.querySelector<HTMLImageElement>('img');
    this.file = this.querySelector<HTMLInputElement>('input[type=file]');
    this.label = this.querySelector<HTMLLabelElement>('label');
    if (this.file) {
      //Enforcing certain values of the items
      if (empty(this.file.accept)) {
        this.file.accept = 'image/avif,image/bmp,image/gif,image/jpeg,image/png,image/webp,image/svg+xml';
      }
      this.file.placeholder = 'Image file';
      //Attach listener to the file upload field
      this.file.addEventListener('change', () => {
        this.update();
      });
    }
    if (this.preview && this.label) {
      this.preview.alt = `Preview of ${this.label.textContent.charAt(0)
                                           .toLowerCase()}${this.label.textContent.slice(1)}`;
      this.preview.setAttribute('data-tooltip', this.preview.alt);
      //In case we have a data-current, that is not empty - attempt to show it
      const current = this.preview.getAttribute('data-current') ?? '';
      if (!(/^\s*$/v).test(current)) {
        this.preview.src = current;
        this.preview.classList.remove('hidden');
      }
    }
  }

  /**
   * Function to update the preview of the avatar.
   */
  private update(): void {
    if (this.preview && this.file) {
      if (this.file.files?.[0]) {
        this.preview.src = URL.createObjectURL(this.file.files[0]);
        this.preview.classList.remove('hidden');
      } else {
        this.preview.classList.add('hidden');
      }
    }
  }
}
