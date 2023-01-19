class WebShare extends HTMLElement
{
    public constructor() {
        super();
        //Register WebShare if supported
        if (navigator.canShare()) {
            this.classList.remove('hidden');
            this.addEventListener('click', this.share.bind(this));
        } else {
            this.classList.add('hidden');
        }
    }
    
    private share(): void
    {
        navigator.share({
            'text': getMeta('og:description') ?? getMeta('description') ?? '',
            'title': document.title,
            'url': document.location.href,
        }).catch(() => {
            addSnackbar('Failed to share link, possibly unsupported feature.', 'failure', 10000);
            this.classList.add('hidden');
        });
    }
}
