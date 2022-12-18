class BackToTop extends HTMLElement
{
    private static content: null | HTMLElement;
    private static BTTs: null | Array<any>;

    constructor() {
        super();
        if (!BackToTop.content) {
            BackToTop.content = (document.getElementById('content') as HTMLElement);
            BackToTop.BTTs = Array.from(document.getElementsByTagName('back-to-top'));
            BackToTop.content.addEventListener('scroll', this.toggleButtons.bind(this));
        }
        this.addEventListener('click', () => {(BackToTop.content as HTMLElement).scrollTop = 0;});
    }

    private toggleButtons(): void
    {
        if (BackToTop.BTTs) {
            if ((BackToTop.content as HTMLElement).scrollTop === 0) {
                BackToTop.BTTs.forEach((item) => {
                    item.classList.add('hidden');
                });
            } else {
                BackToTop.BTTs.forEach((item) => {
                    item.classList.remove('hidden');
                });
            }
        }
    }
}
