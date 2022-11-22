class Snackbar
{
    private readonly snacks: HTMLDivElement;
    private static notificationIndex: number = 0;

    constructor(text: string, color: string = '', milliseconds = 3000)
    {
        this.snacks = document.querySelector('snack-bar') as HTMLDivElement;
        if (this.snacks) {
            //Generate element
            let snack = document.createElement('dialog');
            //Set ID for notification
            let id = Snackbar.notificationIndex++;
            snack.setAttribute('id', 'snackbar' + id);
            snack.setAttribute('role', 'alert');
            //Add snackbar class
            snack.classList.add('snackbar');
            //Add text
            snack.innerHTML = '<span class="snack_text">' + text + '</span><snack-close data-close-in="' + milliseconds + '"><input class="navIcon snack_close" alt="Close notification" type="image" src="/img/close.svg" aria-invalid="false" placeholder="image"></snack-close>';
            snack.querySelectorAll('a[target="_blank"]').forEach(anchor => {
                new A().newTabStyle(anchor as HTMLAnchorElement);
            });
            //Add class for color
            if (color) {
                snack.classList.add(color);
            }
            //Add element to parent
            this.snacks.appendChild(snack);
            //Add animation class
            snack.classList.add('fadeIn');
        }
    }
}

class SnackbarClose extends HTMLElement
{
    private readonly snackbar: HTMLDivElement;
    private readonly snack: HTMLDialogElement;

    constructor()
    {
        super();
        this.snack = this.parentElement as HTMLDialogElement;
        this.snackbar = document.querySelector('snack-bar') as HTMLDivElement;
        this.addEventListener('click', this.close);
        let closeIn = parseInt(this.getAttribute('data-close-in') ?? '0')
        if (closeIn > 0) {
            setTimeout(() => {
                this.close();
            }, closeIn);
        }
    }

    public close(): void
    {
        //Animate removal
        this.snack.classList.remove('fadeIn');
        this.snack.classList.add('fadeOut');
        //Actual removal
        this.snack.addEventListener('animationend', () => {
            if (this.snack) {
                this.snackbar.removeChild(this.snack);
            }
        });
    }
}
