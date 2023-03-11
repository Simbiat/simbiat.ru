class BackToTop extends HTMLElement
{
    private readonly content: HTMLElement | null;
    private readonly BTTs: NodeListOf<HTMLElement>;

    public constructor() {
        super();
        this.content = document.querySelector('#content');
        this.BTTs = document.querySelectorAll('back-to-top');
        if (this.content) {
            window.addEventListener('scroll', this.toggleButtons.bind(this), false);
            this.addEventListener('click', () => {
                    window.scrollTo({
                        'behavior': 'smooth',
                        'left': 0,
                        'top': 0,
                    });
            });
        }
    }

    private toggleButtons(): void
    {
        if (this.content && !empty(this.BTTs)) {
            if (window.scrollY <= window.innerHeight/100) {
                this.BTTs.forEach((item) => {
                    item.classList.add('hidden');
                });
            } else {
                this.BTTs.forEach((item) => {
                    item.classList.remove('hidden');
                });
            }
        }
    }
}
