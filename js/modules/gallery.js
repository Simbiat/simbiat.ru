/*exported galleryInit*/
let galleryCurrent = 1;
let galleryList = [];

function galleryInit()
{
    //Attach trigger for opening overlay
    document.querySelectorAll('.galleryZoom').forEach(item => {
        item.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            galleryOpen(event.target);
            return false;
        });
    });
    //Attach trigger for closing the overlay
    document.getElementById('galleryClose').addEventListener('click', galleryClose);
    //Attach triggers for navigation
    document.getElementById('galleryPrevious').addEventListener('click', galleryPrevious);
    document.getElementById('galleryNext').addEventListener('click', galleryNext);
    //Attach scroll triggers to carousel scrolling
    document.querySelectorAll('.imageCarouselPrev, .imageCarouselNext').forEach(item => {
        item.addEventListener('click', scrollCarousel);
    });
    //Disabled scrolling buttons for carousels, that require this. Doing in separate cycle to avoid triggering it twice
    document.querySelectorAll('.imageCarousel').forEach(item => {
        carouselDisable(item);
    });
}

function scrollCarousel(event)
{
    let scrollButton = event.target;
    let ul = scrollButton.parentElement.getElementsByTagName('ul')[0];
    //Get width to scroll based on width of one of the images
    let img = ul.getElementsByTagName('img')[0];
    let width = img.width;
    if (scrollButton.classList.contains('imageCarouselPrev')) {
        ul.scrollLeft -= width;
    } else {
        ul.scrollLeft += width;
    }
    carouselDisable(scrollButton.parentElement);
}

function carouselDisable(carousel)
{
    //Get previous and next buttons
    let prev = carousel.getElementsByClassName('imageCarouselPrev')[0];
    let next = carousel.getElementsByClassName('imageCarouselNext')[0];
    //Get UL to get scroll details
    let ul = carousel.getElementsByTagName('ul')[0];
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

function galleryOpen(image)
{
    //Get list of images
    galleryCount();
    //Get current image
    let link;
    if (image.tagName.toLowerCase() === 'a') {
        link = image;
    } else {
        link = image.closest('a');
    }
    //Get current index
    galleryCurrent = galleryGetIndex(link);
    //Load image
    galleryLoadImage();
    //Show overlay
    document.getElementById('galleryOverlay').classList.remove('hidden');
}

function galleryLoadImage()
{
    //Get element from array
    let link = galleryList[galleryCurrent - 1];
    //Get image
    let image = link.getElementsByTagName('img')[0];// jshint ignore:line
    //Get figcaption
    let caption = link.parentElement.getElementsByTagName('figcaption')[0];
    //Get name
    let name = link.getAttribute('data-tooltip') ?? link.getAttribute('title') ?? image.getAttribute('alt') ?? link.href.replace(/^.*[\\\/]/u, '');// jshint ignore:line
    //Update elements
    document.getElementById('galleryName').innerHTML = caption ? caption.innerHTML : name;
    document.getElementById('galleryNameLink').innerHTML = '<a href="'+link.href+'" target="_blank"><img loading="lazy" decoding="async" class="linkIcon" alt="Open in new tab" src="/img/newtab.svg"></a>';
    document.getElementById('galleryTotal').innerText = galleryList.length.toString();
    document.getElementById('galleryCurrent').innerText = galleryCurrent.toString();
    document.getElementById('galleryImage').innerHTML = '<img id="galleryLoadedImage" loading="lazy" decoding="async" alt="'+name+'" src="'+link.href+'">';
    document.getElementById('galleryLoadedImage').addEventListener('load', checkZoom);
}

function galleryClose()
{
    document.getElementById('galleryOverlay').classList.add('hidden');
}

function galleryCount()
{
    //Reset array
    galleryList = [];
    //Populate array
    galleryList = document.querySelectorAll('.galleryZoom');
}

function galleryGetIndex(link)
{
    return Array.from(galleryList).indexOf(link) + 1;
}

function galleryPrevious()
{
    galleryCurrent = galleryCurrent - 1;
    //Scroll over
    if (galleryCurrent < 1) {
        galleryCurrent = galleryList.length;
    }
    //Load image
    galleryLoadImage(galleryCurrent);
}

function galleryNext()
{
    galleryCurrent = galleryCurrent + 1;
    //Scroll over
    if (galleryCurrent > galleryList.length) {
        galleryCurrent = 1;
    }
    //Load image
    galleryLoadImage(galleryCurrent);
}

function checkZoom()
{
    let image = document.getElementById('galleryLoadedImage');
    if (image.naturalHeight <= image.height) {
        image.classList.add('noZoom');
        image.removeEventListener('click', galleryZoom);
    } else {
        image.classList.remove('noZoom');
        image.addEventListener('click', galleryZoom);
    }
}

function galleryZoom()
{
    let image = document.getElementById('galleryLoadedImage');
    if (image.classList.contains('zoomedIn')) {
        image.classList.remove('zoomedIn');
    } else {
        image.classList.add('zoomedIn');
    }
}
