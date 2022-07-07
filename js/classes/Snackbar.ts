class Snackbar
{
    private readonly snacks: HTMLDivElement;
    private notificationIndex: number = 0;

    constructor()
    {
        this.snacks = document.getElementById('snacksContainer') as HTMLDivElement;
    }

    add(text: string, color: string = '', milliseconds = 3000): void
    {
        //Generate element
        let snack = document.createElement('dialog');
        //Set ID for notification
        let id = this.notificationIndex++;
        snack.setAttribute('id', 'snackbar' + id);
        snack.setAttribute('role', 'alert');
        //Add snackbar class
        snack.classList.add('snackbar');
        //Add text
        snack.innerHTML = '<span class="snack_text">' + text + '</span><input id="closeSnack' + id + '" class="navIcon snack_close" alt="Close notification" type="image" src="/img/close.svg" aria-invalid="false" placeholder="image">';
        //Add class for color
        if (color) {
            snack.classList.add(color);
        }
        //Add element to parent
        this.snacks.appendChild(snack);
        //Add animation class
        snack.classList.add('fadeIn');
        //Add event listener to close button
        snack.addEventListener('click', () => {this.delete(snack);});
        //Set time to remove the child
        if (milliseconds > 0) {
            setTimeout(() => {
                this.delete(snack);
            }, milliseconds);
        }
    }

    delete(snack: HTMLDialogElement): void
    {
        //Animate removal
        snack.classList.remove('fadeIn');
        snack.classList.add('fadeOut');
        //Actual removal
        snack.addEventListener('animationend', () => {this.snacks.removeChild(snack);});
    }
}
