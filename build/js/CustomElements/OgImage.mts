/**
 * @file Custom logic for `og-image` element.
 */
export class OgImage extends HTMLElement {
  private readonly og_image = document.querySelector<HTMLImageElement>('#og_image');
  private readonly hide_banner = document.querySelector<HTMLDivElement>('div.hide_banner');

  public constructor() {
    super();
    //Listener to hide og_image
    if (this.hide_banner) {
      this.hide_banner.addEventListener('click', () => {
        this.toggleBanner();
      });
    }
  }

  /**
   * Toggle banner element.
   */
  private toggleBanner(): void {
    if (this.og_image && this.hide_banner) {
      if (this.og_image.classList.contains('hidden')) {
        this.og_image.classList.remove('hidden');
        this.hide_banner.textContent = 'Hide banner';
      } else {
        this.og_image.classList.add('hidden');
        this.hide_banner.textContent = 'Show banner';
      }
    }
  }
}
