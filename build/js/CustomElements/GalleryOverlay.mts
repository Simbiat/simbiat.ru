/**
 * @file Custom logic for `gallery-overlay` element.
 */
import type { HTMLFigcaptionElement } from '../Common/Aliases.mts';
import { formatFileSize, updateHistory } from '../Common/Helpers.mts';

/**
 * Custom logic for `gallery-overlay` element.
 */
export class GalleryOverlay extends HTMLElement {
  /**
   * Value of `tabindex` attribute for gallery.
   */
  private readonly gallery_tab_index = 99;
  /**
   * ID of currently opened image.
   */
  private current_image_id = 0;
  /**
   * List of the images, which are included in the gallery.
   */
  public images: NodeListOf<HTMLAnchorElement | HTMLImageElement> = document.querySelectorAll<HTMLAnchorElement | HTMLImageElement>('.gallery_zoom');
  private original_trigger: HTMLAnchorElement | HTMLImageElement | null = null;
  /**
   * Flag indicating that the gallery is open.
   */
  public is_opened = false;
  private gallery_name: HTMLDivElement | null = null;
  private gallery_close: HTMLButtonElement | null = null;
  private gallery_previous: HTMLDivElement | null = null;
  private gallery_next: HTMLDivElement | null = null;
  private gallery_name_link: HTMLAnchorElement | null = null;
  private gallery_name_down: HTMLAnchorElement | null = null;
  private gallery_image: HTMLDivElement | null = null;
  private gallery_loaded_image: HTMLImageElement | null = null;
  private gallery_total: HTMLSpanElement | null = null;
  private gallery_current: HTMLSpanElement | null = null;
  private gallery_dimensions: HTMLSpanElement | null = null;
  private gallery_size: HTMLSpanElement | null = null;

  /**
   * Get ID of the current image.
   */
  public get current(): number {
    return this.current_image_id;
  }

  /**
   * Set ID of the current image.
   * @param value - ID to set.
   */
  public set current(value: number) {
    if (value < 0) {
      //Scroll to last
      this.current_image_id = this.images.length - 1;
    } else if (value > this.images.length - 1) {
      //Scroll to first
      this.current_image_id = 0;
    } else {
      this.current_image_id = value;
    }
    if (this.images.length > 1 || !(this.parentElement as HTMLDialogElement).open) {
      this.open();
    }
  }

  public constructor() {
    super();
    //Extra processing only if there are actual images
    if (this.images.length > 0) {
      //Attach trigger for opening overlay
      for (const [index, item] of this.images.entries()) {
        item.addEventListener('click', (event) => {
          event.preventDefault();
          event.stopPropagation();
          this.original_trigger = event.target as HTMLAnchorElement | HTMLImageElement;
          this.current = index;
          return false;
        });
      }
      //Attach triggers for navigation
      this.addEventListener('keydown', this.keyNav.bind(this));
    }
  }

  /**
   * Initial processing when the element is added to DOM.
   */
  public connectedCallback(): void {
    this.gallery_name = this.querySelector<HTMLDivElement>('#gallery_name');
    this.gallery_name_link = this.querySelector<HTMLAnchorElement>('#gallery_name_link');
    this.gallery_name_down = this.querySelector<HTMLAnchorElement>('#gallery_name_down');
    this.gallery_image = this.querySelector<HTMLDivElement>('#gallery_image');
    this.gallery_loaded_image = this.querySelector<HTMLImageElement>('#gallery_loaded_image');
    this.gallery_total = this.querySelector<HTMLSpanElement>('#gallery_total');
    this.gallery_current = this.querySelector<HTMLSpanElement>('#gallery_current');
    this.gallery_close = this.querySelector<HTMLButtonElement>('#gallery_close');
    this.gallery_previous = this.querySelector<HTMLDivElement>('#gallery_prev');
    this.gallery_next = this.querySelector<HTMLDivElement>('#gallery_next');
    this.gallery_dimensions = this.querySelector<HTMLDivElement>('#gallery_dimensions');
    this.gallery_size = this.querySelector<HTMLDivElement>('#gallery_size');
    this.gallery_close
        ?.addEventListener('click', () => {
          this.close();
        });
    this.gallery_loaded_image
        ?.addEventListener('load', () => {
          this.checkZoom();
          this.metaData();
        });
    this.gallery_loaded_image?.addEventListener('click', (event) => {
      this.zoom(event);
    });
    if (this.images.length > 1) {
      this.gallery_previous?.addEventListener('click', () => {
        this.previous();
      });
      this.gallery_next?.addEventListener('click', () => {
        this.next();
      });
    } else {
      this.gallery_previous?.classList.add('disabled');
      this.gallery_next?.classList.add('disabled');
    }
  }

  /**
   * Process image's meta data.
   */
  private metaData(): void {
    if (this.gallery_loaded_image) {
      if (this.gallery_dimensions) {
        this.gallery_dimensions.textContent = `${this.gallery_loaded_image.naturalWidth}x${this.gallery_loaded_image.naturalHeight}px`;
      }
      if (this.gallery_size) {
        const performance_entry = performance.getEntriesByName(this.gallery_loaded_image.src)[0];
        let image_size = null;
        if (performance_entry) {
          image_size = (performance_entry as PerformanceResourceTiming).decodedBodySize;
        }
        if (image_size === null) {
          this.gallery_size.textContent = '';
        } else {
          this.gallery_size.textContent = formatFileSize(image_size);
        }
      }
    }
  }

  /**
   * Open the gallery overlay.
   */
  private open(): void {
    this.tabIndex = this.gallery_tab_index;
    if (this.gallery_dimensions) {
      this.gallery_dimensions.textContent = '';
    }
    if (this.gallery_size) {
      this.gallery_size.textContent = '';
    }
    //Get element from the node list
    const link = this.images[this.current];
    if (link instanceof HTMLAnchorElement) {
      //Get image
      const image = link.querySelector<HTMLImageElement>('img');
      if (image instanceof HTMLImageElement) {
        image.classList.remove('zoomed_in');
        //Get figcaption
        const caption = link.parentElement?.querySelector<HTMLFigcaptionElement>('figcaption');
        //Get name
        const name = link.getAttribute('data-tooltip') ?? link.getAttribute('title') ?? image.getAttribute('alt') ?? link.href.replace(/^.*[\/\\]/v, '');
        //Update elements
        if (this.gallery_name) {
          if (caption) {
            this.gallery_name.replaceChildren(...Array.from(caption.childNodes, (n) => {
              return n.cloneNode(true);
            }));
          } else {
            this.gallery_name.textContent = name;
          }
        }
        if (this.gallery_name_link) {
          this.gallery_name_link.href = link.href;
        }
        if (this.gallery_name_down) {
          this.gallery_name_down.href = link.href;
        }
        if (this.gallery_loaded_image) {
          this.gallery_loaded_image.src = link.href;
          this.gallery_loaded_image.alt = image.getAttribute('alt') ?? '';
          this.gallery_loaded_image.setAttribute('data-tooltip', image.getAttribute('alt') ?? '');
        }
        if (this.gallery_total) {
          this.gallery_total.textContent = this.images.length.toString();
        }
        if (this.gallery_current) {
          this.gallery_current.textContent = (this.current + 1).toString();
        }
        //Show overlay
        if (!(this.parentElement as HTMLDialogElement).open) {
          (this.parentElement as HTMLDialogElement).showModal();
        }
        //Update URL
        this.history();
        image.focus();
        this.is_opened = true;
      }
    }
  }

  /**
   * Close the gallery overlay.
   */
  public close(): void {
    if (!this.is_opened) {
      return;
    }
    this.tabIndex = -1;
    //Hide overlay
    (this.parentElement as HTMLDialogElement).close();
    //Update URL
    this.history();
    //Focus on the 1st focusable element to help with keyboard navigation. If not done, focus may stay on the close button.
    (document.querySelectorAll<HTMLElement>('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])')[0])?.focus();
    this.is_opened = false;
    this.original_trigger?.focus();
  }

  /**
   * Show the previous image.
   */
  public previous(): void {
    this.current -= 1;
  }

  /**
   * Show the next image.
   */
  public next(): void {
    this.current += 1;
  }

  /**
   * Handle navigation with the keyboard.
   * @param event - Event to process.
   */
  private keyNav(event: KeyboardEvent): boolean {
    event.stopPropagation();
    if (['ArrowDown', 'ArrowRight', 'PageDown'].includes(event.code)) {
      this.next();
      return false;
    } else if (['ArrowUp', 'ArrowLeft', 'PageUp'].includes(event.code)) {
      this.previous();
      return false;
    } else if (event.code === 'End') {
      this.current = this.images.length - 1;
      return false;
    } else if (event.code === 'Home') {
      this.current = 0;
      return false;
    } else if (['Escape', 'Backspace'].includes(event.code)) {
      this.close();
      return false;
    } else if (event.code === 'Space') {
      this.zoom(event);
      return false;
    }
    return true;
  }

  /**
   * Update browser's history.
   */
  private history(): void {
    const url = new URL(document.location.href, window.location.origin);
    const new_index = (this.current + 1).toString();
    const new_url = new URL(document.location.href, window.location.origin);
    let new_title: string;
    if ((this.parentElement as HTMLDialogElement).open) {
      new_title = `${document.title.replace(/(?<pageTitle>^.*), image \d+/iv, '$<pageTitle>')}, Image ${new_index}`;
      new_url.hash = `gallery=${new_index}`;
    } else {
      new_title = document.title.replace(/(?<pageTitle>^.*), image \d+/iv, '$<pageTitle>');
      new_url.hash = '';
    }
    // Update only if there is URL change
    if (url !== new URL(new_url, window.location.origin)) {
      updateHistory(new_url.href, new_title);
    }
  }

  /**
   * Check if an image can be zoomed.
   */
  private checkZoom(): void {
    if (this.gallery_loaded_image) {
      this.gallery_loaded_image.classList.remove('zoomed_in');
      this.gallery_loaded_image.style.transformOrigin = '';
      if (this.gallery_image) {
        this.gallery_image.scrollLeft = 0;
        this.gallery_image.scrollTop = 0;
      }
      if (this.gallery_loaded_image.naturalHeight <= this.gallery_loaded_image.height) {
        this.gallery_loaded_image.classList.add('zoom_fits_container');
      } else {
        this.gallery_loaded_image.classList.remove('zoom_fits_container');
      }
    }
  }

  /**
   * Zoom the image.
   * @param event - Event to react to.
   */
  private zoom(event: KeyboardEvent | MouseEvent): void {
    // Flag for whether a click called this
    const is_click = event.type === 'click';
    // Using variables since these will be used in `requestAnimationFrame` later
    const img = this.gallery_loaded_image;
    const container = this.gallery_image;
    if (!img || !container) {
      return;
    }
    // Focus on the image. This will not happen "naturally", if zoomed with the keyboard
    img.focus();
    // Zoom out
    if (img.classList.contains('zoomed_in')) {
      // Reset attributes
      img.style.transformOrigin = '';
      img.classList.remove('zoomed_in');
      requestAnimationFrame(() => {
        container.scrollLeft = 0;
        container.scrollTop = 0;
      });
      return;
    }
    let point = null;
    // Need to calculate point to which we are zooming in based on where we clicked
    if (is_click) {
      const img_rect = img.getBoundingClientRect();
      const container_rect = container.getBoundingClientRect();
      // Check if the image will overflow if zoomed
      const overflows = (img.naturalWidth * 2 > container_rect.width || img.naturalHeight * 2 > container_rect.height);
      // Adjust positioning so that the image does not move around the container when zoomed in, if it fits the container
      if (overflows) {
        const origin_x = (((event as MouseEvent).clientX - img_rect.left) / img_rect.width) * 100;
        const origin_y = (((event as MouseEvent).clientY - img_rect.top) / img_rect.height) * 100;
        img.style.transformOrigin = `${origin_x.toFixed(2)}% ${origin_y.toFixed(2)}%`;
      } else {
        img.style.transformOrigin = 'center';
      }
      // Get the point to zoom in
      point = {
        scrollLeft: ((event as MouseEvent).clientX - img_rect.left) * (img.naturalWidth / img_rect.width) - (event as MouseEvent).clientX - container_rect.left,
        scrollTop: ((event as MouseEvent).clientY - img_rect.top) * (img.naturalHeight / img_rect.height) - (event as MouseEvent).clientY - container_rect.top,
      };
    } else {
      img.style.transformOrigin = 'center';
    }
    img.classList.add('zoomed_in');
    // Scroll if the image does not fit
    if (!img.classList.contains('zoom_fits_container')) {
      // This prevents the browser from scrolling
      container.style.overflow = 'hidden';
      requestAnimationFrame(() => {
        if (is_click && point) {
          // Put the clicked point under the cursor (or as close as we can)
          container.scrollLeft = point.scrollLeft;
          container.scrollTop = point.scrollTop;
        } else {
          // If `Space` was used to zoom in, or we did not get the point for some other reason - scroll to center of the image
          container.scrollLeft = img.naturalWidth / 2 - container.clientWidth / 2;
          container.scrollTop = img.naturalHeight / 2 - container.clientHeight / 2;
        }
        container.style.overflow = '';
      });
    }
  }
}
