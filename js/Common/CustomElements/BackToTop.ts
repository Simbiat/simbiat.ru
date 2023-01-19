class BackToTop extends HTMLElement
{
    private readonly content: HTMLElement | null;
    private readonly BTTs: NodeListOf<HTMLElement>;

    public constructor() {
        super();
            this.content = document.querySelector('#content');
            this.BTTs = document.querySelectorAll('back-to-top');
        if (this.content) {
            this.content.addEventListener('scroll', this.toggleButtons.bind(this));
            this.addEventListener('click', () => {
                if (this.content) {
                    this.content.scrollTop = 0;
                }
            });
        }
    }

    private toggleButtons(): void
    {
        if (this.content && !empty(this.BTTs)) {
            if (this.content.scrollTop === 0) {
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
