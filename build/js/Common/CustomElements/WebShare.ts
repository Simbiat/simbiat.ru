class WebShare extends HTMLElement
{
    private readonly shareData;
    
    public constructor() {
        super();
        //Register WebShare if supported
        this.shareData = {
            'text': getMeta('og:description') ?? getMeta('description') ?? '',
            'title': document.title,
            'url': document.location.href,
        };
        if (navigator.canShare(this.shareData)) {
            this.classList.remove('hidden');
            this.addEventListener('click', this.share.bind(this));
        } else {
            this.classList.add('hidden');
        }
    }
    
    private share(): void
    {
        navigator.share(this.shareData).catch(() => {
            addSnackbar('Failed to share link, possibly unsupported feature.', 'failure', 10000);
            this.classList.add('hidden');
        });
    }
}
