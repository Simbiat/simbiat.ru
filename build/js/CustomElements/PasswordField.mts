/**
 * @file Custom logic for `password-field` element.
 */
export class PasswordField extends HTMLElement {
  private password_input: HTMLInputElement | null = null;
  private password_show: HTMLDivElement | null = null;
  private password_requirements: HTMLDivElement | null = null;
  private password_strength: HTMLDivElement | null = null;
  private strength_span: HTMLSpanElement | null = null;

  /**
   * Initial processing when the element is added to DOM.
   */
  public connectedCallback(): void {
    this.password_input = this.querySelector<HTMLInputElement>('input');
    this.password_show = this.querySelector<HTMLDivElement>('.password_show');
    this.password_requirements = this.querySelector<HTMLDivElement>('.password_requirements');
    this.password_strength = this.querySelector<HTMLDivElement>('.password_strength');
    if (this.password_input) {
      if (this.password_strength) {
        this.strength_span = this.password_strength.querySelector<HTMLSpanElement>('span');
      }
      if (this.password_show) {
        this.password_show.addEventListener('click', this.toggleType.bind(this));
      }
      if (this.password_requirements) {
        this.password_requirements.classList.add('hidden');
        this.password_input.addEventListener('focus', () => {
          if (this.password_requirements) {
            this.show(this.password_requirements);
          }
        });
        this.password_input.addEventListener('focusout', () => {
          if (this.password_requirements) {
            this.hide(this.password_requirements);
          }
        });
        for (const event_type of ['focus', 'change', 'input']) {
          this.password_input.addEventListener(event_type, this.validate.bind(this));
        }
      }
      if (this.password_strength && this.strength_span) {
        this.password_strength.classList.add('hidden');
        this.password_input.addEventListener('focus', () => {
          if (this.password_strength) {
            this.show(this.password_strength);
          }
        });
        this.password_input.addEventListener('focusout', () => {
          if (this.password_strength) {
            this.hide(this.password_strength);
          }
        });
        for (const event_type of ['focus', 'change', 'input']) {
          this.password_input.addEventListener(event_type, this.calculate.bind(this));
        }
      }
    }
  }

  /**
   * Show or hide password by changing the password field's type.
   * @param event - Event to process.
   */
  private toggleType(event: Event): void {
    //Prevent focus stealing
    event.preventDefault();
    if (this.password_input && this.password_show) {
      if (this.password_input.type === 'password') {
        this.password_input.type = 'text';
        this.password_show.title = 'Hide password';
      } else {
        this.password_input.type = 'password';
        this.password_show.title = 'Show password';
      }
    }
  }

  /**
   * Validate that password requirements are met.
   */
  private validate(): void {
    if (this.password_input && this.password_requirements) {
      if (this.password_input.validity.valid) {
        this.password_requirements.classList.remove('error');
        this.password_requirements.classList.add('success');
      } else {
        this.password_requirements.classList.add('error');
        this.password_requirements.classList.remove('success');
      }
    }
  }

  /**
   * Calculate password strength.
   */
  private calculate(): string {
    if (this.password_input && this.strength_span) {
      const password = this.password_input.value;
      //Assigning points for the password
      let points = 0;
      //Check that it's long enough
      if ((/.{8,}/v).test(password)) {
        points += 1;
      }
      //Add one more point if it's twice as long as the minimum requirement
      if ((/.{16,}/v).test(password)) {
        points += 1;
      }
      //Add one more point if it's 3 times as long as the minimum requirement
      if ((/.{32,}/v).test(password)) {
        points += 1;
      }
      //Add one more point if it's 64 characters or more
      if ((/.{64,}/v).test(password)) {
        points += 1;
      }
      //Check for lower case letters
      if ((/\p{Ll}/v).test(password)) {
        points += 1;
      }
      //Check for upper case letters
      if ((/\p{Lu}/v).test(password)) {
        points += 1;
      }
      //Check for letters without a case (glyphs)
      if ((/\p{Lo}/v).test(password)) {
        points += 1;
      }
      //Check for numbers
      if ((/\p{N}/v).test(password)) {
        points += 1;
      }
      //Check for punctuation
      if ((/[\p{P}\p{S}]/v).test(password)) {
        points += 1;
      }
      //Reduce point for repeating characters
      if ((/(?<character>.)\k<character>{2,}/v).test(password)) {
        points -= 1;
      }
      //Set strength
      let strength;
      //Return value based on points. Note that order is important.
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
      this.strength_span.textContent = strength;
      //Remove classes
      this.strength_span.classList.remove('password_weak', 'password_medium', 'password_strong', 'password_very_strong');
      //Add class
      if (strength === 'very strong') {
        this.strength_span.classList.add('password_very_strong');
      } else {
        this.strength_span.classList.add(`password_${strength}`);
      }
      return strength;
    }
    return '';
  }

  /**
   * Show an element.
   * @param target - Element to show.
   */
  private show(target: HTMLDivElement): void {
    if (this.password_input) {
      const autocomplete = this.password_input.getAttribute('autocomplete') ?? null;
      if (autocomplete === 'new-password') {
        target.classList.remove('hidden');
      } else {
        target.classList.add('hidden');
      }
    }
  }

  /**
   * Hide an element.
   * @param target - Element to hide.
   */
  private hide(target: HTMLDivElement): void {
    target.classList.add('hidden');
  }
}
