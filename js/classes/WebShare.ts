class WebShare
{
    private readonly shareButton: HTMLImageElement;

    constructor() {
        this.shareButton = document.getElementById('shareButton') as HTMLImageElement;
        if (this.shareButton) {
            //Register WebShare if supported
            if (navigator.share !== undefined) {
                this.shareButton.classList.remove('hidden');
                this.shareButton.addEventListener('click', this.share);
            } else {
                this.shareButton.classList.add('hidden');
            }
        }
    }

    share(): Promise<void>
    {
        return navigator.share({
            title: document.title,
            text: getMeta('og:description') ?? getMeta('description') ?? '',
            url: document.location.href,
        });
    }
}
