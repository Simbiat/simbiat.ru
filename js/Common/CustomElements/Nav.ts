class NavShow extends HTMLElement
{
    private readonly navDiv: HTMLElement | null = null;

    public constructor() {
        super();
        this.navDiv = document.querySelector('#navigation');
        this.addEventListener('click', () => {
            this.navDiv?.classList.add('shown');
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
            this.navDiv?.classList.remove('shown');
        });
    }
}

class SideShow extends HTMLElement
{
    private readonly sidebarPopUp: HTMLElement | null = null;
    private readonly sideHide: HTMLElement | null = null;
    private readonly sidebar: HTMLElement | null = null;
    private readonly button: HTMLElement | null = null;
    
    public constructor() {
        super();
        this.button = this.querySelector('input');
        this.sideHide = document.querySelector('side-hide');
        if (this.id === 'prodLink') {
            if (this.button) {
                this.button.addEventListener('click', () => {
                    window.open(document.location.href.replace('local.simbiat.ru', 'www.simbiat.dev'), '_blank');
                });
            }
        } else if (this.button && this.sideHide && this.hasAttribute('data-sidebar')) {
                this.sidebarPopUp = document.querySelector('#sidebar_pop_up');
                this.sidebar = document.querySelector(`#${String(this.getAttribute('data-sidebar'))}`);
                this.button.addEventListener('click', () => {
                    this.sidebarPopUp?.classList.remove('hidden');
                    this.sidebar?.classList.remove('hidden');
                    this.sideHide?.classList.remove('hidden');
                });
            }
    }
}

class SideHide extends HTMLElement
{
    private readonly sidebarPopUp: HTMLElement | null = null;
    private readonly sidebars: NodeListOf<HTMLElement>;
    
    public constructor() {
        super();
        this.sidebarPopUp = document.querySelector('#sidebar_pop_up');
        this.sidebars = document.querySelectorAll('.sidebar');
        this.addEventListener('click', () => {
            this.sidebarPopUp?.classList.add('hidden');
            this.sidebars.forEach((aside) => {
                aside.classList.add('hidden');
            });
            this.classList.add('hidden');
        });
    }
}
