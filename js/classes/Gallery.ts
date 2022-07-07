class Gallery
{
    private current: number = 1;
    public static images: Array<HTMLElement> = [];

    constructor()
    {
        //Attach trigger for opening overlay
        document.querySelectorAll('.galleryZoom').forEach(item => {
            item.addEventListener('click', (event: Event) => {
                event.preventDefault();
                event.stopPropagation();
                this.open(event.target as HTMLElement, true);
                return false;
            });
        });
        //Attach trigger for closing the overlay
        (document.getElementById('galleryClose') as HTMLDivElement).addEventListener('click', this.close.bind(this));
        //Attach triggers for navigation
        (document.getElementById('galleryPrevious') as HTMLDivElement).addEventListener('click', this.previous.bind(this));
        (document.getElementById('galleryNext') as HTMLDivElement).addEventListener('click', this.next.bind(this));
        //Attach scroll triggers to carousel scrolling
        document.querySelectorAll('.imageCarouselPrev, .imageCarouselNext').forEach(item => {
            item.addEventListener('click', (event: Event) => { this.scroll(event as Event) });
        });
        //Get list of images
        this.count();
        //Disabled scrolling buttons for carousels, that require this. Doing in separate cycle to avoid triggering it twice
        document.querySelectorAll('.imageCarousel').forEach(item => {
            this.disable(item as HTMLElement);
        });
    }

    scroll(event: Event): void
    {
        let scrollButton = event.target as HTMLElement;
        let ul = (scrollButton.parentElement as HTMLElement).getElementsByTagName('ul')[0] as HTMLUListElement;
        //Get width to scroll based on width of one of the images
        let img = ul.getElementsByTagName('img')[0] as HTMLImageElement;
        let width = img.width;
        if (scrollButton.classList.contains('imageCarouselPrev')) {
            ul.scrollLeft -= width;
        } else {
            ul.scrollLeft += width;
        }
        this.disable(scrollButton.parentElement as HTMLElement);
    }

    disable(carousel: HTMLElement): void
    {
        //Get previous and next buttons
        let prev = carousel.getElementsByClassName('imageCarouselPrev')[0] as HTMLDivElement;
        let next = carousel.getElementsByClassName('imageCarouselNext')[0] as HTMLDivElement;
        //Get UL to get scroll details
        let ul = carousel.getElementsByTagName('ul')[0] as HTMLUListElement;
        //Get maximum scrollLeft value
        let max = ul.scrollWidth - ul.offsetWidth;
        if (ul.scrollLeft === 0) {
            prev.classList.add('disabled');
        } else {
            prev.classList.remove('disabled');
        }
        if (ul.scrollLeft >= max) {
            next.classList.add('disabled');
        } else {
            next.classList.remove('disabled');
        }
    }

    open(image: HTMLElement, hashUpdate: boolean): void
    {
        //Get current image
        let link;
        if (image.tagName.toLowerCase() === 'a') {
            link = image;
        } else {
            link = image.closest('a');
        }
        //Get current index
        this.current = this.getIndex(link as HTMLAnchorElement);
        //Load image
        this.loadImage(hashUpdate);
        //Show overlay
        (document.getElementById('galleryOverlay') as HTMLDivElement).classList.remove('hidden');
    }

    loadImage(hashUpdate: boolean): void
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
        (document.getElementById('galleryNameLink') as HTMLDivElement).innerHTML = '<a href="'+link.href+'" target="_blank"><img loading="lazy" decoding="async" class="linkIcon" alt="Open in new tab" src="/img/newtab.svg"></a>';
        (document.getElementById('galleryTotal') as HTMLDivElement).innerText = Gallery.images.length.toString();
        (document.getElementById('galleryCurrent') as HTMLDivElement).innerText = this.current.toString();
        (document.getElementById('galleryImage') as HTMLDivElement).innerHTML = '<img id="galleryLoadedImage" loading="lazy" decoding="async" alt="'+name+'" src="'+link.href+'">';
        (document.getElementById('galleryLoadedImage') as HTMLDivElement).addEventListener('load', this.checkZoom.bind(this));
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

    close(): void
    {
        (document.getElementById('galleryOverlay') as HTMLDivElement).classList.add('hidden');
    }

    count(): void
    {
        //Reset array
        Gallery.images = [];
        //Populate array
        Gallery.images = Array.from(document.querySelectorAll('.galleryZoom'));
    }

    getIndex(link: HTMLAnchorElement): number
    {
        return Gallery.images.indexOf(link) + 1;
    }

    previous(): void
    {
        this.current = this.current - 1;
        //Scroll over
        if (this.current < 1) {
            this.current = Gallery.images.length;
        }
        //Load image
        this.loadImage(true);
    }

    next(): void
    {
        this.current = this.current + 1;
        //Scroll over
        if (this.current > Gallery.images.length) {
            this.current = 1;
        }
        //Load image
        this.loadImage(true);
    }

    checkZoom(): void
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

    zoom(): void
    {
        let image = document.getElementById('galleryLoadedImage') as HTMLImageElement;
        if (image.classList.contains('zoomedIn')) {
            image.classList.remove('zoomedIn');
        } else {
            image.classList.add('zoomedIn');
        }
    }
}
