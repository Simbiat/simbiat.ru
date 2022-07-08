class Gallery
{
    private current: number = 1;
    public static images: Array<HTMLElement> = [];

    constructor()
    {
        //Get list of images
        Gallery.images = Array.from(document.querySelectorAll('.galleryZoom'));
        if (Gallery.images.length > 0) {
            //Attach trigger for opening overlay
            Gallery.images.forEach(item => {
                item.addEventListener('click', (event: Event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    this.open(event.target as HTMLElement, true);
                    return false;
                });
            });
            //Attach trigger for closing the overlay
            customElements.define('gallery-close', GalleryClose);
            //Define image carousels
            customElements.define('image-carousel', CarouselList);
            //Attach triggers for navigation
            if (Gallery.images.length > 1) {
                (document.getElementById('galleryPrevious') as HTMLDivElement).addEventListener('click', this.previous.bind(this));
                (document.getElementById('galleryNext') as HTMLDivElement).addEventListener('click', this.next.bind(this));
            } else {
                (document.getElementById('galleryPrevious') as HTMLDivElement).classList.add('disabled');
                (document.getElementById('galleryNext') as HTMLDivElement).classList.add('disabled');
            }
            (document.getElementById('galleryLoadedImage') as HTMLDivElement).addEventListener('load', this.checkZoom.bind(this));
        }
    }

    public open(image: HTMLElement, hashUpdate: boolean): void
    {
        //Get current image
        let link;
        if (image.tagName.toLowerCase() === 'a') {
            link = image;
        } else {
            link = image.closest('a');
        }
        //Get current index
        this.current = Gallery.images.indexOf(link as HTMLAnchorElement) + 1;
        //Load image
        this.loadImage(hashUpdate);
        //Show overlay
        (document.getElementById('galleryOverlay') as HTMLDivElement).classList.remove('hidden');
        if (Gallery.images.length > 1) {
            document.addEventListener('keydown', this.keyNav.bind(this));
        }
    }

    private loadImage(hashUpdate: boolean): void
    {
        //Get element from array
        let link = Gallery.images[this.current - 1] as HTMLAnchorElement;
        //Get image
        let image = link.getElementsByTagName('img')[0] as HTMLImageElement;
        //Get figcaption
        let caption = (link.parentElement as HTMLElement).getElementsByTagName('figcaption')[0];
        //Get name
        let name = link.getAttribute('data-tooltip') ?? link.getAttribute('title') ?? image.getAttribute('alt') ?? link.href.replace(/^.*[\\\/]/u, '');
        //Update elements
        (document.getElementById('galleryName') as HTMLDivElement).innerHTML = caption ? caption.innerHTML : name;
        (document.getElementById('galleryNameLink') as HTMLAnchorElement).href = (document.getElementById('galleryLoadedImage') as HTMLImageElement).src = link.href;
        (document.getElementById('galleryTotal') as HTMLDivElement).innerText = Gallery.images.length.toString();
        (document.getElementById('galleryCurrent') as HTMLDivElement).innerText = this.current.toString();
        //Update URL
        if (hashUpdate) {
            let url = new URL(document.location.href);
            let hash = url.hash;
            if (hash) {
                window.history.pushState('Image ' + this.current.toString(), document.title, document.location.href.replace(hash, '#gallery=' + this.current.toString()));
            } else {
                window.history.pushState('Image ' + this.current.toString(), document.title, document.location.href + '#gallery=' + this.current.toString());
            }
        }
    }

    public previous(): void
    {
        this.current = this.current - 1;
        //Scroll over
        if (this.current < 1) {
            this.current = Gallery.images.length;
        }
        //Load image
        this.loadImage(true);
    }

    public next(): void
    {
        this.current = this.current + 1;
        //Scroll over
        if (this.current > Gallery.images.length) {
            this.current = 1;
        }
        //Load image
        this.loadImage(true);
    }

    //Navigation with keyboard
    public keyNav(event: KeyboardEvent): boolean
    {
        //Need to find way to remove it on closure
        //Close on Escape and Backspace
        //Navigation on hashchange got broken
        event.preventDefault();
        event.stopPropagation();
        if (['ArrowUp', 'ArrowRight', 'PageDown'].includes(event.code)) {
            this.next();
            return false;
        } else if (['ArrowDown', 'ArrowLeft', 'PageUp'].includes(event.code)) {
            this.previous();
            return false;
        } else if (event.code === 'End') {
            this.current = Gallery.images.length;
            this.loadImage(true);
            return false;
        } else if (event.code === 'Home') {
            this.current = 1;
            this.loadImage(true);
            return false;
        } else {
            return true;
        }
    }

    private checkZoom(): void
    {
        let image = document.getElementById('galleryLoadedImage') as HTMLImageElement;
        if (image.naturalHeight <= image.height) {
            image.classList.add('noZoom');
            image.removeEventListener('click', this.zoom.bind(this));
        } else {
            image.classList.remove('noZoom');
            image.addEventListener('click', this.zoom.bind(this));
        }
    }

    public zoom(): void
    {
        let image = document.getElementById('galleryLoadedImage') as HTMLImageElement;
        if (image.classList.contains('zoomedIn')) {
            image.classList.remove('zoomedIn');
        } else {
            image.classList.add('zoomedIn');
        }
    }
}

class GalleryClose extends HTMLElement
{
    constructor()
    {
        super();
        this.addEventListener('click', this.close);
    }

    public close(): void
    {
        //Update URL
        let url = new URL(document.location.href);
        let hash = url.hash;
        if (hash) {
            window.history.pushState(document.title, document.title, document.location.href.replace(hash, ''));
        }
        //Hide the gallery
        (document.getElementById('galleryOverlay') as HTMLDivElement).classList.add('hidden');
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
        this.list = this.getElementsByClassName('imageCarouselList')[0] as HTMLUListElement;
        this.next = this.getElementsByClassName('imageCarouselNext')[0] as HTMLDivElement;
        this.previous = this.getElementsByClassName('imageCarouselPrev')[0] as HTMLDivElement;
        if (this.list && this.next && this.previous) {
            //Get maximum scrollLeft value
            this.maxScroll = this.list.scrollWidth - this.list.offsetWidth;
            //Attache logic to disable scroll buttons conditionally
            this.list.addEventListener('scroll', () => {
                this.disableScroll.bind(this);
            });
            //Attach scroll triggers to carousel buttons
            [this.next, this.previous].forEach(item => {
                item.addEventListener('click', (event: Event) => {
                    this.toScroll(event as Event)
                });
            });
            //Disabled scrolling buttons for carousels, that require this. Doing in separate cycle to avoid triggering it twice
            this.disableScroll();
        }
    }

    public toScroll(event: Event): void
    {
        let scrollButton = event.target as HTMLElement;
        //Get width to scroll based on width of one of the images
        let img = this.list.getElementsByTagName('img')[0] as HTMLImageElement;
        let width = img.width;
        if (scrollButton.classList.contains('imageCarouselPrev')) {
            this.list.scrollLeft -= width;
        } else {
            this.list.scrollLeft += width;
        }
        this.disableScroll();
    }

    public disableScroll(): void
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
