class WebShare extends HTMLElement
{
    constructor() {
        super();
        if (this) {
            //Register WebShare if supported
            if (navigator.share !== undefined) {
                this.classList.remove('hidden');
                this.addEventListener('click', this.share);
            } else {
                this.classList.add('hidden');
            }
        }
    }

    public share(): Promise<void>
    {
        return navigator.share({
            title: document.title,
            text: getMeta('og:description') ?? getMeta('description') ?? '',
            url: document.location.href,
        });
    }
}
