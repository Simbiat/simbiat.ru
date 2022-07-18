class Aside
{
    private static _instance: null | Aside = null;
    private sidebarDiv: HTMLElement | null = null;

    constructor()
    {
        if (Aside._instance) {
            return Aside._instance;
        }
        this.sidebarDiv = (document.getElementById('sidebar') as HTMLElement);
        //Listeners for click event to show or hide sidebar
        (document.getElementById('showSidebar') as HTMLDivElement).addEventListener('click', () => {(this.sidebarDiv as HTMLElement).classList.add('shown')});
        (document.getElementById('hideSidebar') as HTMLDivElement).addEventListener('click', () => {(this.sidebarDiv as HTMLElement).classList.remove('shown')});
        Aside._instance = this;
    }
}
