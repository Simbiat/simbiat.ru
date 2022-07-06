function webShareInit(): void
{
    let shareButton = document.getElementById('shareButton');
    if (shareButton) {
        //Register WebShare if supported
        if (navigator.share !== undefined) {
            shareButton.classList.remove('hidden');
            shareButton.addEventListener('click', webShare);
        } else {
            shareButton.classList.add('hidden');
        }
    }
}

//WebShare API call
function webShare(): Promise<void> {
    return navigator.share({
        title: document.title,
        text: getMeta('og:description') ?? getMeta('description') ?? '',
        url: document.location.href,
    });
}
