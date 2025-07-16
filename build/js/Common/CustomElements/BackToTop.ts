class BackToTop extends HTMLElement
{
    private readonly content: HTMLElement | null;
    private readonly BTTs: NodeListOf<HTMLElement>;

    public constructor()
    {
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
        //Update URL hash based on position and available headings
        if (!window.location.hash.toLowerCase()
                   .startsWith('#gallery=')) {
            const headings = document.querySelectorAll('h1:not(#h1_title), h2, h3, h4, h5, h6');
            for (let i = 0; i <= headings.length - 1; i++) {
                const heading = headings[i] as HTMLHeadingElement;
                const bottom = heading.getBoundingClientRect().bottom;
                const top = heading.getBoundingClientRect().top;
                const height = heading.getBoundingClientRect().height;
                if (top >= -height * 2 && bottom <= height * 2 && heading.checkVisibility()) {
                    history.replaceState(document.title, document.title, `#${heading.id}`);
                    return;
                }
            }
        }
    }
}
