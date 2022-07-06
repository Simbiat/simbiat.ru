/*exported galleryInit*/
let galleryCurrent = 1;
let galleryList: Array<HTMLElement> = [];

function galleryInit(): void
{
    //Attach trigger for opening overlay
    document.querySelectorAll('.galleryZoom').forEach(item => {
        item.addEventListener('click', function(event: Event) {
            event.preventDefault();
            event.stopPropagation();
            galleryOpen(event.target as HTMLElement, true);
            return false;
        });
    });
    //Attach trigger for closing the overlay
    (document.getElementById('galleryClose') as HTMLDivElement).addEventListener('click', galleryClose);
    //Attach triggers for navigation
    (document.getElementById('galleryPrevious') as HTMLDivElement).addEventListener('click', galleryPrevious);
    (document.getElementById('galleryNext') as HTMLDivElement).addEventListener('click', galleryNext);
    //Attach scroll triggers to carousel scrolling
    document.querySelectorAll('.imageCarouselPrev, .imageCarouselNext').forEach(item => {
        item.addEventListener('click', scrollCarousel);
    });
    //Get list of images
    galleryCount();
    //Disabled scrolling buttons for carousels, that require this. Doing in separate cycle to avoid triggering it twice
    document.querySelectorAll('.imageCarousel').forEach(item => {
        carouselDisable(item as HTMLElement);
    });
}

function scrollCarousel(event: Event): void
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
    carouselDisable(scrollButton.parentElement as HTMLElement);
}

function carouselDisable(carousel: HTMLElement): void
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

function galleryOpen(image: HTMLElement, hashUpdate: boolean): void
{
    //Get current image
    let link;
    if (image.tagName.toLowerCase() === 'a') {
        link = image;
    } else {
        link = image.closest('a');
    }
    //Get current index
    galleryCurrent = galleryGetIndex(link as HTMLAnchorElement);
    //Load image
    galleryLoadImage(hashUpdate);
    //Show overlay
    (document.getElementById('galleryOverlay') as HTMLDivElement).classList.remove('hidden');
}

function galleryLoadImage(hashUpdate: boolean): void
{
    //Get element from array
    let link = galleryList[galleryCurrent - 1] as HTMLAnchorElement;
    //Get image
    let image = link.getElementsByTagName('img')[0] as HTMLImageElement;
    //Get figcaption
    let caption = (link.parentElement as HTMLElement).getElementsByTagName('figcaption')[0];
    //Get name
    let name = link.getAttribute('data-tooltip') ?? link.getAttribute('title') ?? image.getAttribute('alt') ?? link.href.replace(/^.*[\\\/]/u, '');
    //Update elements
    (document.getElementById('galleryName') as HTMLDivElement).innerHTML = caption ? caption.innerHTML : name;
    (document.getElementById('galleryNameLink') as HTMLDivElement).innerHTML = '<a href="'+link.href+'" target="_blank"><img loading="lazy" decoding="async" class="linkIcon" alt="Open in new tab" src="/img/newtab.svg"></a>';
    (document.getElementById('galleryTotal') as HTMLDivElement).innerText = galleryList.length.toString();
    (document.getElementById('galleryCurrent') as HTMLDivElement).innerText = galleryCurrent.toString();
    (document.getElementById('galleryImage') as HTMLDivElement).innerHTML = '<img id="galleryLoadedImage" loading="lazy" decoding="async" alt="'+name+'" src="'+link.href+'">';
    (document.getElementById('galleryLoadedImage') as HTMLDivElement).addEventListener('load', checkZoom);
    //Update URL
    if (hashUpdate) {
        let url = new URL(document.location.href);
        let hash = url.hash;
        if (hash) {
            window.history.pushState('Image ' + galleryCurrent.toString(), document.title, document.location.href.replace(hash, '#gallery=' + galleryCurrent.toString()));
        } else {
            window.history.pushState('Image ' + galleryCurrent.toString(), document.title, document.location.href + '#gallery=' + galleryCurrent.toString());
        }
    }
}

function galleryClose(): void
{
    (document.getElementById('galleryOverlay') as HTMLDivElement).classList.add('hidden');
}

function galleryCount(): void
{
    //Reset array
    galleryList = [];
    //Populate array
    galleryList = Array.from(document.querySelectorAll('.galleryZoom'));
}

function galleryGetIndex(link: HTMLAnchorElement): number
{
    return galleryList.indexOf(link) + 1;
}

function galleryPrevious(): void
{
    galleryCurrent = galleryCurrent - 1;
    //Scroll over
    if (galleryCurrent < 1) {
        galleryCurrent = galleryList.length;
    }
    //Load image
    galleryLoadImage(true);
}

function galleryNext(): void
{
    galleryCurrent = galleryCurrent + 1;
    //Scroll over
    if (galleryCurrent > galleryList.length) {
        galleryCurrent = 1;
    }
    //Load image
    galleryLoadImage(true);
}

function checkZoom(): void
{
    let image = document.getElementById('galleryLoadedImage') as HTMLImageElement;
    if (image.naturalHeight <= image.height) {
        image.classList.add('noZoom');
        image.removeEventListener('click', galleryZoom);
    } else {
        image.classList.remove('noZoom');
        image.addEventListener('click', galleryZoom);
    }
}

function galleryZoom(): void
{
    let image = document.getElementById('galleryLoadedImage') as HTMLImageElement;
    if (image.classList.contains('zoomedIn')) {
        image.classList.remove('zoomedIn');
    } else {
        image.classList.add('zoomedIn');
    }
}
