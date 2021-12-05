/*exported galleryInit*/
let galleryCurrent = 1;
let galleryList = [];

function galleryInit()
{
    //Attach trigger for opening overlay
    Array.from(document.getElementsByClassName('galleryZoom')).forEach(item => {
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
    let image = galleryList[galleryCurrent - 1];
    //Get name
    let name = image.getAttribute('data-tooltip') ?? image.getAttribute('title') ?? image.href.replace(/^.*[\\\/]/u, '');// jshint ignore:line
    //Update elements
    document.getElementById('galleryName').innerText = name;
    document.getElementById('galleryNameLink').innerHTML = '<a href="'+image.href+'" target="_blank"><img loading="lazy" decoding="async" class="linkIcon" alt="Open in new tab" src="/img/newtab.svg"></a>';
    document.getElementById('galleryTotal').innerText = galleryList.length.toString();
    document.getElementById('galleryCurrent').innerText = galleryCurrent.toString();
    document.getElementById('galleryImage').innerHTML = '<img id="galleryLoadedImage" loading="lazy" decoding="async" alt="'+name+'" src="'+image.href+'">';
    document.getElementById('galleryLoadedImage').addEventListener('click', galleryZoom);
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

function galleryZoom()
{
    let image = document.getElementById('galleryLoadedImage');
    if (image.classList.contains('zoomedIn')) {
        image.classList.remove('zoomedIn');
    } else {
        image.classList.add('zoomedIn');
    }
}
