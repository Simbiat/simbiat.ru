class OGImage extends HTMLElement
{
    private readonly og_image: HTMLImageElement | null = null;
    private readonly hideBanner: HTMLDivElement | null = null;
    
    public constructor()
    {
        super();
        this.og_image = document.querySelector('#og_image');
        this.hideBanner = document.querySelector('hide-banner');
        //Listener to hide og_image
        if (this.hideBanner) {
            this.hideBanner.addEventListener('click', () => {
                this.toggleBanner();
            });
        }
    }
    
    private toggleBanner(): void
    {
        if (this.og_image && this.hideBanner) {
            if (this.og_image.classList.contains('hidden')) {
                this.og_image.classList.remove('hidden');
                this.hideBanner.textContent = 'Hide banner';
            } else {
                this.og_image.classList.add('hidden');
                this.hideBanner.textContent = 'Show banner';
            }
        }
    }
}
