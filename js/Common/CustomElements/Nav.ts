class NavShow extends HTMLElement
{
    private readonly navDiv: HTMLElement | null = null;

    public constructor() {
        super();
        this.navDiv = document.querySelector('#navigation');
        this.addEventListener('click', () => {
            this.navDiv?.classList.add('flex');
        });
    }
}

class NavHide extends HTMLElement
{
    private readonly navDiv: HTMLElement | null = null;

    public constructor() {
        super();
        this.navDiv = document.querySelector('#navigation');
        this.addEventListener('click', () => {
            this.navDiv?.classList.remove('flex');
        });
    }
}

class SideShow extends HTMLElement
{
    private readonly sideHide: HTMLElement | null = null;
    private readonly sidebar: HTMLDialogElement | null = null;
    private readonly button: HTMLElement | null = null;
    
    public constructor() {
        super();
        this.button = this.querySelector('input');
        this.sideHide = document.querySelector('side-hide');
        if (this.id === 'prodLink') {
            if (this.button) {
                this.button.addEventListener('click', () => {
                    window.open(document.location.href.replace('local.simbiat.dev', 'www.simbiat.dev'), '_blank');
                });
            }
        } else if (this.button && this.sideHide && this.hasAttribute('data-sidebar')) {
                this.sidebar = document.querySelector(`#${String(this.getAttribute('data-sidebar'))}`);
                this.button.addEventListener('click', () => {
                    this.sidebar?.showModal();
                });
            }
    }
}

class SideHide extends HTMLElement
{
    private readonly sidebar: HTMLDialogElement | null = null;
    
    public constructor() {
        super();
        if (this.parentElement) {
            this.sidebar = this.parentElement.parentElement as HTMLDialogElement;
            this.addEventListener('click', () => {
                this.sidebar?.close();
            });
        }
    }
}
