class A
{
    private static _instance: null | A = null;

    constructor()
    {
        if (A._instance) {
            return A._instance;
        }
        document.querySelectorAll('a[target="_blank"]').forEach(anchor => {
            if (!anchor.innerHTML.includes('img/newtab.svg') && !anchor.classList.contains('galleryZoom')) {
                anchor.innerHTML += '<img class="newTabIcon" src="/img/newtab.svg" alt="Opens in new tab">';
            }
        });
        A._instance = this;
    }
}
