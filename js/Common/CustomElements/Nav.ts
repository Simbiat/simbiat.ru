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
    private readonly sidebar: HTMLElement | null = null;
    
    public constructor() {
        super();
        this.sidebar = document.querySelector('#sidebar');
        this.addEventListener('click', () => {
            this.sidebar?.classList.add('shown');
        });
    }
}

class SideHide extends HTMLElement
{
    private readonly sidebar: HTMLElement | null = null;
    
    public constructor() {
        super();
        this.sidebar = document.querySelector('#sidebar');
        this.addEventListener('click', () => {
            this.sidebar?.classList.remove('shown');
        });
    }
}
