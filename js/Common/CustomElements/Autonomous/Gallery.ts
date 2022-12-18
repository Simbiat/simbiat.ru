class Gallery extends HTMLElement
{
    //ID of currently opened image
    private _current: number = 0;
    //Array of the images
    public images: Array<HTMLElement> = [];

    public get current(): number
    {
        return this._current;
    }

    public set current(value: number)
    {
        if (value < 0) {
            //Scroll to last
            this._current = this.images.length -1;
        } else if (value > this.images.length - 1) {
            //Scroll to first
            this._current = 0;
        } else {
            this._current = value;
        }
        if (this.images.length > 1 || this.classList.contains('hidden')) {
            this.open();
        }
    }

    constructor() {
        super();
        //Get list of images
        this.images = Array.from(document.querySelectorAll('.galleryZoom'));
        //Extra processing only if there are actual images
        if (this.images.length > 0) {
            //Define buttons
            customElements.define('gallery-close', GalleryClose);
            customElements.define('gallery-prev', GalleryPrev);
            customElements.define('gallery-next', GalleryNext);
            customElements.define('gallery-image', GalleryImage);
            //Attach trigger for opening overlay
            this.images.forEach((item, index: number) => {
                item.addEventListener('click', (event: Event) => {
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
    
    private open(): void
    {
        this.tabIndex = 99;
        //Get element from array
        let link = this.images[this.current] as HTMLAnchorElement;
        //Get image
        let image = link.querySelector('img') as HTMLImageElement;
        image.classList.remove('zoomedIn');
        //Get figcaption
        let caption = (link.parentElement as HTMLElement).querySelector('figcaption');
        //Get name
        let name = link.getAttribute('data-tooltip') ?? link.getAttribute('title') ?? image.getAttribute('alt') ?? link.href.replace(/^.*[\\\/]/u, '');
        //Update elements
        (document.getElementById('galleryName') as HTMLDivElement).innerHTML = caption ? caption.innerHTML : name;
        (document.getElementById('galleryNameLink') as HTMLAnchorElement).href = (document.getElementById('galleryLoadedImage') as HTMLImageElement).src = link.href;
        (document.getElementById('galleryTotal') as HTMLDivElement).innerText = this.images.length.toString();
        (document.getElementById('galleryCurrent') as HTMLDivElement).innerText = (this.current + 1).toString();
        //Show overlay
        this.classList.remove('hidden');
        //Update URL
        this.history();
        this.focus();
    }

    public close(): void
    {
        this.tabIndex = -1;
        //Hide overlay
        this.classList.add('hidden');
        //Update URL
        this.history();
        //Focus on 1st focusable element to help with keyboard navigation. If not done, focus may stay on close button.
        (document.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])')[0] as HTMLElement).focus();
    }

    public previous(): void
    {
        this.current--;
    }

    public next(): void
    {
        this.current++;
    }

    //Navigation with keyboard
    private keyNav(event: KeyboardEvent): boolean
    {
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
        } else {
            return true;
        }
    }

    private history(): void
    {
        const url = new URL(document.location.href);
        const newIndex = (this.current + 1).toString();
        const regexTitle = new RegExp('(.+'+pageTitle+')(, Image \\d+)?', 'ui');
        let newUrl: string;
        let newTitle: string;
        if (this.classList.contains('hidden')) {
            newTitle = document.title.replace(/(.*)(, Image )(\d+)/ui, '$1');
            newUrl = document.location.href.replace(url.hash, '');
        } else {
            newTitle = document.title.replace(regexTitle, '$1, Image ' + newIndex);
            newUrl = document.location.href.replace(/([^#]+)((#gallery=\d+)|$)/ui, '$1#gallery=' + newIndex);
        }
        //Update only if there is URL change
        if (url !== new URL(newUrl)) {
            updateHistory(newUrl, newTitle);
        }
    }
}

class GalleryImage extends HTMLElement
{
    private image: HTMLImageElement;
    private readonly zoomListener;

    constructor() {
        super();
        this.image = document.getElementById('galleryLoadedImage') as HTMLImageElement;
        this.zoomListener = this.zoom.bind(this);
        this.image.addEventListener('load', this.checkZoom.bind(this));
    }

    private checkZoom(): void
    {
        this.image.classList.remove('zoomedIn');
        if (this.image.naturalHeight <= this.image.height) {
            this.image.removeEventListener('click', this.zoomListener);
            this.image.classList.add('noZoom');
        } else {
            this.image.classList.remove('noZoom');
            this.image.addEventListener('click', this.zoomListener);
        }
    }
    
    private zoom(): void
    {
        if (this.image.classList.contains('zoomedIn')) {
            this.image.classList.remove('zoomedIn');
        } else {
            this.image.classList.add('zoomedIn');
        }
    }
}

class GalleryPrev extends HTMLElement
{
    private overlay: Gallery;

    constructor() {
        super();
        this.overlay = (document.querySelector('gallery-overlay') as Gallery);
        if (this.overlay.images.length > 1) {
            this.addEventListener('click', () => {
                this.overlay.previous();
            });
        } else {
            this.classList.add('disabled');
        }
    }
}

class GalleryNext extends HTMLElement
{
    private overlay: Gallery;

    constructor() {
        super();
        this.overlay = (document.querySelector('gallery-overlay') as Gallery);
        if (this.overlay.images.length > 1) {
            this.addEventListener('click', () => {
                this.overlay.next();
            });
        } else {
            this.classList.add('disabled');
        }
    }
}

class GalleryClose extends HTMLElement
{
    constructor()
    {
        super();
        this.addEventListener('click', () => {
            (document.querySelector('gallery-overlay') as Gallery).close();
        });
    }
}

class CarouselList extends HTMLElement
{
    private readonly list: HTMLUListElement;
    private readonly next: HTMLDivElement;
    private readonly previous: HTMLDivElement;
    private readonly maxScroll: number = 0;

    constructor()
    {
        super();
        this.list = this.querySelector('.imageCarouselList') as HTMLUListElement;
        this.next = this.querySelector('.imageCarouselNext') as HTMLDivElement;
        this.previous = this.querySelector('.imageCarouselPrev') as HTMLDivElement;
        if (this.list && this.next && this.previous) {
            //Get maximum scrollLeft value
            this.maxScroll = this.list.scrollWidth - this.list.offsetWidth;
            //Attache logic to disable scroll buttons conditionally
            this.list.addEventListener('scroll', () => {
                this.disableScroll();
            });
            //Attach scroll triggers to carousel buttons
            [this.next, this.previous].forEach(item => {
                item.addEventListener('click', (event: Event) => {
                    this.toScroll(event as Event)
                });
            });
            //Disabled scrolling buttons for carousels, that require this
            this.disableScroll();
        }
    }
    
    private toScroll(event: Event): void
    {
        let scrollButton = event.target as HTMLElement;
        //Get width to scroll based on width of one of the images
        let img = this.list.querySelector('img') as HTMLImageElement;
        let width = img.width;
        if (scrollButton.classList.contains('imageCarouselPrev')) {
            this.list.scrollLeft -= width;
        } else {
            this.list.scrollLeft += width;
        }
        this.disableScroll();
    }
    
    private disableScroll(): void
    {
        if (this.list.scrollLeft === 0) {
            this.previous.classList.add('disabled');
        } else {
            this.previous.classList.remove('disabled');
        }
        if (this.list.scrollLeft >= this.maxScroll) {
            this.next.classList.add('disabled');
        } else {
            this.next.classList.remove('disabled');
        }
    }
}
