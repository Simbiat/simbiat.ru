class Snackbar
{
    private readonly snacks: HTMLDivElement;
    private static notificationIndex: number = 0;

    constructor(text: string, color: string = '', milliseconds = 3000)
    {
        this.snacks = document.querySelector('snack-bar') as HTMLDivElement;
        if (this.snacks) {
            //Generate element
            let template = (document.querySelector('#snackbar_template') as HTMLTemplateElement).content.cloneNode(true) as DocumentFragment;
            //Set ID for notification
            let id = Snackbar.notificationIndex++;
            let snack = template.querySelector('dialog') as HTMLDialogElement;
            snack.setAttribute('id', 'snackbar' + id);
            //Add text
            (snack.querySelector('.snack_text') as HTMLSpanElement).innerHTML = text;
            //Update milliseconds for auto-closure
            (snack.querySelector('snack-close') as HTMLElement).setAttribute('data-close-in', String(milliseconds));
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
