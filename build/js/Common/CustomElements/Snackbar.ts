class SnackbarClose extends HTMLElement
{
    private readonly snackbar: HTMLDivElement | null = null;
    private readonly snack: HTMLDialogElement;
    
    public constructor()
    {
        super();
        this.snack = this.parentElement as HTMLDialogElement;
        const snackbar = document.querySelector('snack-bar');
        if (snackbar !== null) {
            this.snackbar = snackbar as HTMLDivElement;
        }
        this.addEventListener('click', this.close.bind(this));
        const closeIn = parseInt(this.getAttribute('data-close-in') ?? '0', 10);
        if (closeIn > 0) {
            window.setTimeout(() => {
                this.close();
            }, closeIn);
        }
    }
    
    private close(): void
    {
        //Animate removal
        this.snack.classList.remove('fadeIn');
        this.snack.classList.add('fadeOut');
        //Actual removal
        this.snack.addEventListener('animationend', () => {
            this.snack.close();
            if ((this.snackbar?.contains(this.snack)) === true) {
                this.snackbar.removeChild(this.snack);
            }
        });
    }
}
