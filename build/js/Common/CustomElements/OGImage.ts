class OGImage extends HTMLElement
{
    private readonly ogimage: HTMLImageElement | null = null;
    private readonly hideBanner: HTMLDivElement | null = null;
    
    public constructor() {
        super();
        this.ogimage = document.querySelector('#ogimage');
        this.hideBanner = document.querySelector('hide-banner');
        //Listener to hide ogimage
        if (this.hideBanner) {
            this.hideBanner.addEventListener('click', () => {
                this.toggleBanner();
            });
        }
    }
    
    private toggleBanner(): void
    {
        if (this.ogimage && this.hideBanner) {
            if (this.ogimage.classList.contains('hidden')) {
                this.ogimage.classList.remove('hidden');
                this.hideBanner.textContent = 'Hide banner';
            } else {
                this.ogimage.classList.add('hidden');
                this.hideBanner.textContent = 'Show banner';
            }
        }
    }
}
