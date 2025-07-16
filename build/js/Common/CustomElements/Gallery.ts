class Gallery extends HTMLElement {
  //ID of currently opened image
  private _current = 0;
  //Array of the images
  public images: HTMLElement[] = [];
  //Flag indicating that gallery is open
  public isOpened = false;
  //Basic sub-elements
  private readonly gallery_name: HTMLDivElement | null = null;
  private readonly gallery_name_link: HTMLAnchorElement | null = null;
  private readonly gallery_loaded_image: HTMLImageElement | null = null;
  private readonly gallery_total: HTMLDivElement | null = null;
  private readonly gallery_current: HTMLDivElement | null = null;

  public get current(): number {
    return this._current;
  }

  public set current(value: number) {
    if (value < 0) {
      //Scroll to last
      this._current = this.images.length - 1;
    } else if (value > this.images.length - 1) {
      //Scroll to first
      this._current = 0;
    } else {
      this._current = value;
    }
    if (this.images.length > 1 || !(this.parentElement as HTMLDialogElement).open) {
      this.open();
    }
  }

  public constructor() {
    super();
    //Get list of images
    this.images = Array.from(document.querySelectorAll('.gallery_zoom'));
    this.gallery_name = document.querySelector('#gallery_name');
    this.gallery_name_link = document.querySelector('#gallery_name_link');
    this.gallery_loaded_image = document.querySelector('#gallery_loaded_image');
    this.gallery_total = document.querySelector('#gallery_total');
    this.gallery_current = document.querySelector('#gallery_current');
    //Extra processing only if there are actual images
    if (this.images.length > 0) {
      //Attach trigger for opening overlay
      this.images.forEach((item, index: number) => {
        item.addEventListener('click', (event: MouseEvent) => {
          event.preventDefault();
          event.stopPropagation();
          this.current = index;
          return false;
        });
      });
      //Attach triggers for navigation
      this.addEventListener('keydown', this.keyNav.bind(this));
    }
  }

  private open(): void {
    this.tabIndex = 99;
    //Get element from array
    const link = this.images[this.current];
    if (link instanceof HTMLAnchorElement) {
      //Get image
      const image = link.querySelector('img');
      if (image instanceof HTMLImageElement) {
        image.classList.remove('zoomed_in');
        //Get figcaption
        const caption = link.parentElement?.querySelector('figcaption');
        //Get name
        const name = link.getAttribute('data-tooltip') ?? link.getAttribute('title') ?? image.getAttribute('alt') ?? link.href.replace(/^.*[\\/]/u, '');
        //Update elements
        if (this.gallery_name) {
          this.gallery_name.innerHTML = caption ? caption.innerHTML : name;
        }
        if (this.gallery_name_link) {
          this.gallery_name_link.href = link.href;
        }
        if (this.gallery_loaded_image) {
          this.gallery_loaded_image.src = link.href;
        }
        if (this.gallery_total) {
          this.gallery_total.innerText = this.images.length.toString();
        }
        if (this.gallery_current) {
          this.gallery_current.innerText = (this.current + 1).toString();
        }
        //Show overlay
        if (!(this.parentElement as HTMLDialogElement).open) {
          (this.parentElement as HTMLDialogElement).showModal();
        }
        //Update URL
        this.history();
        this.focus();
        this.isOpened = true;
      }
    }
  }

  public close(): void {
    if (!this.isOpened) {
      return;
    }
    this.tabIndex = -1;
    //Hide overlay
    (this.parentElement as HTMLDialogElement).close();
    //Update URL
    this.history();
    //Focus on 1st focusable element to help with keyboard navigation. If not done, focus may stay on close button.
    (document.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])')[0] as HTMLElement).focus();
    this.isOpened = false;
  }

  public previous(): void {
    this.current -= 1;
  }

  public next(): void {
    this.current += 1;
  }

  // Navigation with keyboard
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
    }
    return true;
  }

  private history(): void {
    const url = new URL(document.location.href, window.location.origin);
    const new_index = (this.current + 1).toString();
    const new_url = new URL(document.location.href, window.location.origin);
    let new_title: string;
    if ((this.parentElement as HTMLDialogElement).open) {
      new_title = `${document.title.replace(/(?<pageTitle>.*)(?<imagePrefix>, Image )(?<imageNumber>\d+)/ui, '$<pageTitle>')}, Image ${new_index}`;
      new_url.hash = `gallery=${new_index}`;
    } else {
      new_title = document.title.replace(/(?<pageTitle>.*)(?<imagePrefix>, Image )(?<imageNumber>\d+)/ui, '$<pageTitle>');
      new_url.hash = '';
    }
    // Update only if there is URL change
    if (url !== new URL(new_url, window.location.origin)) {
      updateHistory(new_url.href, new_title);
    }
  }
}

class GalleryImage extends HTMLElement {
  private readonly image: HTMLImageElement | null = null;
  private readonly zoom_listener;

  public constructor() {
    super();
    this.image = document.querySelector('#gallery_loaded_image');
    this.zoom_listener = this.zoom.bind(this);
    if (this.image) {
      this.image.addEventListener('load', this.checkZoom.bind(this));
    }
  }

  private checkZoom(): void {
    if (this.image) {
      this.image.classList.remove('zoomed_in');
      if (this.image.naturalHeight <= this.image.height) {
        this.image.removeEventListener('click', this.zoom_listener);
        this.image.classList.add('no_zoom');
      } else {
        this.image.classList.remove('no_zoom');
        this.image.addEventListener('click', this.zoom_listener);
      }
    }
  }

  private zoom(): void {
    if (this.image) {
      if (this.image.classList.contains('zoomed_in')) {
        this.image.classList.remove('zoomed_in');
      } else {
        this.image.classList.add('zoomed_in');
      }
    }
  }
}

class GalleryPrev extends HTMLElement {
  private readonly overlay: Gallery | null;

  public constructor() {
    super();
    this.overlay = document.querySelector('gallery-overlay');
    if (this.overlay !== null && this.overlay.images.length > 1) {
      this.addEventListener('click', () => {
        if (this.overlay !== null) {
          this.overlay.previous();
        }
      });
    } else {
      this.classList.add('disabled');
    }
  }
}

class GalleryNext extends HTMLElement {
  private readonly overlay: Gallery | null;

  public constructor() {
    super();
    this.overlay = document.querySelector('gallery-overlay');
    if (this.overlay !== null && this.overlay.images.length > 1) {
      this.addEventListener('click', () => {
        if (this.overlay !== null) {
          this.overlay.next();
        }
      });
    } else {
      this.classList.add('disabled');
    }
  }
}

class GalleryClose extends HTMLElement {
  public constructor() {
    super();
    this.addEventListener('click', () => {
      const overlay = document.querySelector('gallery-overlay');
      if (overlay !== null) {
        (overlay as Gallery).close();
      }
    });
  }
}

class CarouselList extends HTMLElement {
  private readonly list: HTMLUListElement | null;
  private readonly next: HTMLDivElement | null;
  private readonly previous: HTMLDivElement | null;
  private readonly maxScroll: number = 0;

  public constructor() {
    super();
    this.list = this.querySelector('.image_carousel_list');
    this.next = this.querySelector('image-carousel-next');
    this.previous = this.querySelector('image-carousel-prev');
    if (this.list && this.next && this.previous) {
      //Get maximum scrollLeft value
      this.maxScroll = this.list.scrollWidth - this.list.offsetWidth;
      //Attache logic to disable scroll buttons conditionally
      this.list.addEventListener('scroll', () => {
        this.disableScroll();
      });
      //Attach scroll triggers to carousel buttons
      [this.next, this.previous].forEach((item) => {
        item.addEventListener('click', (event: MouseEvent) => {
          this.toScroll(event);
        });
      });
      // Disabled scrolling buttons for carousels, that require this
      this.disableScroll();
    }
  }

  private toScroll(event: Event): void {
    if (this.list) {
      const scrollButton = event.target as HTMLElement;
      //Get width to scroll based on width of one of the images
      const img = this.list.querySelector('img');
      if (img) {
        if (scrollButton.nodeName === 'IMAGE-CAROUSEL-PREV') {
          this.list.scrollLeft -= img.width;
        } else {
          this.list.scrollLeft += img.width;
        }
        this.disableScroll();
      }
    }
  }

  private disableScroll(): void {
    if (this.list) {
      if (this.previous) {
        if (this.list.scrollLeft === 0) {
          this.previous.classList.add('disabled');
        } else {
          this.previous.classList.remove('disabled');
        }
      }
      if (this.next) {
        if (this.list.scrollLeft >= this.maxScroll) {
          this.next.classList.add('disabled');
        } else {
          this.next.classList.remove('disabled');
        }
      }
    }
  }
}
