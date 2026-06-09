/**
 * @file Custom logic for `image-carousel` element.
 */
export class CarouselList extends HTMLElement {
  private list: HTMLUListElement | null = null;
  private next: HTMLDivElement | null = null;
  private previous: HTMLDivElement | null = null;

  /**
   * Initial processing when the element is added to DOM.
   */
  public connectedCallback(): void {
    this.list = this.querySelector<HTMLUListElement>('.image_carousel_list');
    this.next = this.querySelector<HTMLDivElement>('.image_carousel_next');
    this.previous = this.querySelector<HTMLDivElement>('.image_carousel_prev');
    if (this.list && this.next && this.previous) {
      //Attach scroll triggers to carousel buttons
      for (const item of [this.next, this.previous]) {
        item.addEventListener('click', (event: MouseEvent) => {
          this.toScroll(event);
        });
      }
      // Disabled scrolling buttons for carousels that require this
      //this.disableScroll();
      this.initScrollObserver();
    }
  }

  /**
   * Initialize scroll observer.
   */
  private initScrollObserver(): void {
    if (!this.list || !this.next || !this.previous) {
      return;
    }
    const items = this.list.querySelectorAll<HTMLLIElement>('li');
    if (items.length === 0) {
      return;
    }
    const first_item = items[0];
    const last_item = items[items.length - 1];
    if (!first_item || !last_item) {
      return;
    }
    const observer = new IntersectionObserver(
      (entries) => {
        for (const entry of entries) {
          if (entry.target === first_item) {
            this.previous?.classList.toggle('disabled', entry.isIntersecting);
          } else if (entry.target === last_item) {
            this.next?.classList.toggle('disabled', entry.isIntersecting);
          }
        }
      },
      {
        root: this.list,
        threshold: 0.95,
      },
    );
    observer.observe(first_item);
    observer.observe(last_item);
  }

  /**
   * Scroll the carousel.
   * @param event - Event to react to.
   */
  private toScroll(event: Event): void {
    if (this.list) {
      const scroll_button = event.target as HTMLElement;
      //Get width to scroll based on the width of one of the images
      const img = this.list.querySelector<HTMLImageElement>('img');
      if (img) {
        if (scroll_button.classList.contains('image_carousel_prev')) {
          this.list.scrollLeft -= img.width;
        } else {
          this.list.scrollLeft += img.width;
        }
      }
    }
  }
}
