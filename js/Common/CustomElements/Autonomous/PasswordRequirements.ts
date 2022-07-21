class PasswordRequirements extends HTMLElement
{
    private passwordInput: HTMLInputElement;

    constructor() {
        super();
        this.classList.add('hidden');
        this.innerHTML = 'Only password requirement: at least 8 symbols';
        this.passwordInput = (this.parentElement as HTMLDivElement).getElementsByTagName('input').item(0) as HTMLInputElement;
        this.passwordInput.addEventListener('focus', this.show.bind(this));
        this.passwordInput.addEventListener('focusout', this.hide.bind(this));
        ['focus', 'change', 'input',].forEach((eventType: string) => {
            this.passwordInput.addEventListener(eventType, this.validate.bind(this));
        });
    }

    private validate(): void
    {
        if (this.passwordInput.validity.valid) {
            this.classList.remove('error');
            this.classList.add('success');
        } else {
            this.classList.add('error');
            this.classList.remove('success');
        }
    }

    private show(): void
    {
        let autocomplete = this.passwordInput.getAttribute('autocomplete') ?? null;
        if (autocomplete === 'new-password') {
            this.classList.remove('hidden');
        } else {
            this.classList.add('hidden');
        }
    }

    private hide(): void
    {
        this.classList.add('hidden');
    }
}
