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
        this.addEventListener('click', this.share.bind(this));
    }
    
    private share(): void
    {
        if (navigator.share) {
            navigator.share(this.shareData)
                     .catch(() => {
                         this.toClipboard();
                     });
        } else {
            this.toClipboard();
        }
        this.blur();
    }
    
    private toClipboard(): void {
        navigator.clipboard.writeText(window.location.href)
                 .then(() => {
                     addSnackbar(`Page link copied to clipboard`, 'success');
                 }, () => {
                     addSnackbar(`Failed to copy page link to clipboard`, 'failure');
                 });
    }
}
