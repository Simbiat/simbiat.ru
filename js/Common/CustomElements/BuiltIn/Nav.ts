class Nav
{
    private static _instance: null | Nav = null;
    private navDiv: HTMLElement | null = null;

    constructor()
    {
        if (Nav._instance) {
            return Nav._instance;
        }
        this.navDiv = (document.getElementById('navigation') as HTMLElement);
        //Listeners for click event to show or hide navigation
        (document.getElementById('showNav') as HTMLDivElement).addEventListener('click', () => {(this.navDiv as HTMLElement).classList.add('shown')});
        (document.getElementById('hideNav') as HTMLDivElement).addEventListener('click', () => {(this.navDiv as HTMLElement).classList.remove('shown')});
        Nav._instance = this;
    }
}
