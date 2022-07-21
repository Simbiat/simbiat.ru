class PasswordStrength extends HTMLElement
{
    private passwordInput: HTMLInputElement;
    private strengthSpan: HTMLSpanElement;

    constructor() {
        super();
        this.classList.add('hidden');
        this.innerHTML = 'New password strength: <span class="password_strength">weak</span>';
        this.passwordInput = (this.parentElement as HTMLDivElement).getElementsByTagName('input').item(0) as HTMLInputElement;
        this.strengthSpan = this.getElementsByTagName('span')[0] as HTMLSpanElement;
        this.passwordInput.addEventListener('focus', this.show.bind(this));
        this.passwordInput.addEventListener('focusout', this.hide.bind(this));
        ['focus', 'change', 'input',].forEach((eventType: string) => {
            this.passwordInput.addEventListener(eventType, this.calculate.bind(this));
        });
    }

    private calculate(): string
    {
        let password = this.passwordInput.value;
        //Assigning points for the password
        let points = 0;
        //Check that it's long enough
        if (/.{8,}/u.test(password)) {
            points++;
        }
        //Add one more point, if it's twice as long as minimum requirement
        if (/.{16,}/u.test(password)) {
            points++;
        }
        //Add one more point, if it's 3 times as long as minimum requirement
        if (/.{32,}/u.test(password)) {
            points++;
        }
        //Add one more point, if it's 64 characters or more
        if (/.{64,}/u.test(password)) {
            points++;
        }
        //Check for lower case letters
        if (/\p{Ll}/u.test(password)) {
            points++;
        }
        //Check for upper case letters
        if (/\p{Lu}/u.test(password)) {
            points++;
        }
        //Check for letters without case (glyphs)
        if (/\p{Lo}/u.test(password)) {
            points++;
        }
        //Check for numbers
        if (/\p{N}/u.test(password)) {
            points++;
        }
        //Check for punctuation
        if (/[\p{P}\p{S}]/u.test(password)) {
            points++;
        }
        //Reduce point for repeating characters
        if (/(.)\1{2,}/u.test(password)) {
            points--;
        }
        //Set strength
        let strength = 'weak';
        //Return value based on points. Note, that order is important.
        if (points <= 2) {
            strength = 'weak';
        } else if (2 < points && points < 5) {
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
            this.strengthSpan.classList.add('password_'+strength);
        }
        return strength;
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
