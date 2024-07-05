class PasswordShow extends HTMLElement
{
    private readonly passwordInput: HTMLInputElement | null = null;

    public constructor() {
        super();
        if (this.parentElement) {
            this.passwordInput = this.parentElement.querySelector('input');
            if (this.passwordInput) {
                this.addEventListener('click', this.toggle.bind(this));
            }
        }
    }

    //Show or hide password by changing password field's type
    private toggle(event: Event): void
    {
        //Prevent focus stealing
        event.preventDefault();
        if (this.passwordInput) {
            if (this.passwordInput.type === 'password') {
                this.passwordInput.type = 'text';
                this.title = 'Hide password';
            } else {
                this.passwordInput.type = 'password';
                this.title = 'Show password';
            }
        }
    }
}

class PasswordRequirements extends HTMLElement
{
    private readonly passwordInput: HTMLInputElement | null = null;

    public constructor() {
        super();
        this.classList.add('hidden');
        this.innerHTML = 'Only password requirement: at least 8 symbols';
        if (this.parentElement) {
            this.passwordInput = this.parentElement.querySelector('input');
            if (this.passwordInput) {
                this.passwordInput.addEventListener('focus', this.show.bind(this));
                this.passwordInput.addEventListener('focusout', this.hide.bind(this));
                ['focus', 'change', 'input',].forEach((eventType: string) => {
                    if (this.passwordInput) {
                        this.passwordInput.addEventListener(eventType, this.validate.bind(this));
                    }
                });
            }
        }
    }

    private validate(): void
    {
        if (this.passwordInput) {
            if (this.passwordInput.validity.valid) {
                this.classList.remove('error');
                this.classList.add('success');
            } else {
                this.classList.add('error');
                this.classList.remove('success');
            }
        }
    }

    private show(): void
    {
        if (this.passwordInput) {
            const autocomplete = this.passwordInput.getAttribute('autocomplete') ?? null;
            if (autocomplete === 'new-password') {
                this.classList.remove('hidden');
            } else {
                this.classList.add('hidden');
            }
        }
    }

    private hide(): void
    {
        this.classList.add('hidden');
    }
}

class PasswordStrength extends HTMLElement
{
    private readonly passwordInput: HTMLInputElement | null = null;
    private readonly strengthSpan: HTMLSpanElement | null = null;

    public constructor() {
        super();
        this.classList.add('hidden');
        this.innerHTML = 'New password strength: <span class="password_strength">weak</span>';
        if (this.parentElement) {
            this.passwordInput = this.parentElement.querySelector('input');
            this.strengthSpan = this.querySelector('span');
            if (this.passwordInput && this.strengthSpan) {
                this.passwordInput.addEventListener('focus', this.show.bind(this));
                this.passwordInput.addEventListener('focusout', this.hide.bind(this));
                ['focus', 'change', 'input',].forEach((eventType: string) => {
                    if (this.passwordInput) {
                        this.passwordInput.addEventListener(eventType, this.calculate.bind(this));
                    }
                });
            }
        }
    }

    private calculate(): string
    {
        if (this.passwordInput && this.strengthSpan) {
            const password = this.passwordInput.value;
            //Assigning points for the password
            let points = 0;
            //Check that it's long enough
            if ((/.{8,}/u).test(password)) {
                points += 1;
            }
            //Add one more point, if it's twice as long as minimum requirement
            if ((/.{16,}/u).test(password)) {
                points += 1;
            }
            //Add one more point, if it's 3 times as long as minimum requirement
            if ((/.{32,}/u).test(password)) {
                points += 1;
            }
            //Add one more point, if it's 64 characters or more
            if ((/.{64,}/u).test(password)) {
                points += 1;
            }
            //Check for lower case letters
            if ((/\p{Ll}/u).test(password)) {
                points += 1;
            }
            //Check for upper case letters
            if ((/\p{Lu}/u).test(password)) {
                points += 1;
            }
            //Check for letters without case (glyphs)
            if ((/\p{Lo}/u).test(password)) {
                points += 1;
            }
            //Check for numbers
            if ((/\p{N}/u).test(password)) {
                points += 1;
            }
            //Check for punctuation
            if ((/[\p{P}\p{S}]/u).test(password)) {
                points += 1;
            }
            //Reduce point for repeating characters
            if ((/(?<character>.)\1{2,}/u).test(password)) {
                points -= 1;
            }
            //Set strength
            let strength;
            //Return value based on points. Note, that order is important.
            if (points <= 2) {
                strength = 'weak';
            } else if (points > 2 && points < 5) {
                strength = 'medium';
            } else if (points === 5) {
                strength = 'strong';
            } else {
                strength = 'very strong';
            }
            //Set text
            this.strengthSpan.innerHTML = strength;
            //Remove classes
            this.strengthSpan.classList.remove('password_weak', 'password_medium', 'password_strong', 'password_very_strong');
            //Add class
            if (strength === 'very strong') {
                this.strengthSpan.classList.add('password_very_strong');
            } else {
                this.strengthSpan.classList.add(`password_${strength}`);
            }
            return strength;
        }
        return '';
    }

    private show(): void
    {
        if (this.passwordInput) {
            const autocomplete = this.passwordInput.getAttribute('autocomplete') ?? null;
            if (autocomplete === 'new-password') {
                this.classList.remove('hidden');
            } else {
                this.classList.add('hidden');
            }
        }
    }

    private hide(): void
    {
        this.classList.add('hidden');
    }
}
