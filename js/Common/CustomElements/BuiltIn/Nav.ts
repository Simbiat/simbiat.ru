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
        let showNav = document.getElementById('showNav');
        let hideNav = document.getElementById('hideNav');
        if (showNav) {
            showNav.addEventListener('click', () => {
                (this.navDiv as HTMLElement).classList.add('shown')
            });
        }
        if (hideNav) {
            hideNav.addEventListener('click', () => {
                (this.navDiv as HTMLElement).classList.remove('shown')
            });
        }
        Nav._instance = this;
    }
}
