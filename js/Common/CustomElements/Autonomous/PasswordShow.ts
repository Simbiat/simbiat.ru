class PasswordShow extends HTMLElement
{
    private passwordInput: HTMLInputElement;

    constructor() {
        super();
        this.passwordInput = (this.parentElement as HTMLDivElement).getElementsByTagName('input').item(0) as HTMLInputElement;
        this.addEventListener('click', this.toggle)
    }

    //Show or hide password by changing password field's type
    private toggle(event: Event): void
    {
        //Prevent focus stealing
        event.preventDefault();
        if (this.passwordInput.type === 'password') {
            this.passwordInput.type = 'text';
            this.title = 'Hide password';
        } else {
            this.passwordInput.type = 'password';
            this.title = 'Show password';
        }
    }
}
