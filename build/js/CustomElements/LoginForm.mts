/**
 * @file Custom logic for `login-form` element.
 */
import { TIMEZONE } from '../Common/Constants.mts';
import { empty, pageRefresh } from '../Common/Helpers.mts';
import { Ajax } from '../Common/Ajax.mts';
import { Snackbar } from '../Common/Snackbar.mts';
import { Form } from '../NativeElements/Form.mts';

/**
 * Custom logic for `login-form` element.
 */
export class LoginForm extends HTMLElement {
  /**
   * Regex for username. This is NOT JS Regex, thus it has doubled slashes.
   */
  private readonly user_regex = '[\\p{L}\\d.!$%&\'*+\\/=?^_`\\{\\|\\}~\\- ]{1,64}';
  /**
   * Regex for proper email. This is NOT JS Regex, thus it has doubled slashes.
   */
  private readonly email_regex = '[\\p{L}\\d.!#$%&\'*+\\/=?^_`\\{\\|\\}~\\-]+@[a-zA-Z\\d](?:[a-zA-Z\\d\\-]{0,61}[a-zA-Z\\d])?(?:\\.[a-zA-Z\\d](?:[a-zA-Z\\d\\-]{0,61}[a-zA-Z\\d])?)*';
  private readonly login_form = document.querySelector<HTMLFormElement>('#signinup');
  private readonly exist_user = document.querySelector<HTMLInputElement>('#radio_exist_user');
  private readonly new_user = document.querySelector<HTMLInputElement>('#radio_new_user');
  private readonly forget = document.querySelector<HTMLInputElement>('#radio_forget');
  private readonly login = document.querySelector<HTMLInputElement>('#signinup_email');
  private readonly password = document.querySelector<HTMLInputElement>('#signinup_password');
  private readonly button = document.querySelector<HTMLButtonElement>('#signinup_submit');
  private readonly rememberme = document.querySelector<HTMLInputElement>('#rememberme');
  private readonly username = document.querySelector<HTMLInputElement>('#signinup_username');

  public constructor() {
    super();
    if (this.login_form) {
      //Register function for radio buttons toggling on the login form
      for (const radio of this.login_form.querySelectorAll<HTMLInputElement>('#radio_signinup input[type=radio]')) {
        radio.addEventListener('change', this.loginRadioCheck.bind(this));
      }
      //Force loginRadioCheck for consistency
      this.loginRadioCheck();
      Form.submitIntercept(this.login_form, () => {
        void this.singInUpSubmit();
      });
    }
  }

  /**
   * Submit the form.
   */
  private async singInUpSubmit(): Promise<void> {
    if (this.login_form) {
      //Get form data
      const form_data = new FormData(this.login_form);
      if (empty(form_data.get('signinup[type]'))) {
        form_data.set('signinup[type]', 'logout');
      }
      form_data.set('signinup[timezone]', TIMEZONE);
      const button = this.login_form.querySelector<HTMLInputElement>('#signinup_submit');
      const action = form_data.get('signinup[type]') as string;
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/uc/${action}`,
        form_data,
        method: 'POST',
        button,
        /**
         * Further processing on success.
         */
        onSuccess: () => {
          if (action === 'remind') {
            void new Snackbar('If respective account is registered an email has been sent with password reset link.', 'success');
          } else {
            if (action === 'login') {
              void new Snackbar('Successfully logged in. Reloading the page...', 'success');
            } else if (action === 'logout') {
              void new Snackbar('Successfully logged out. Reloading the page...', 'success');
            } else if (action === 'register') {
              void new Snackbar('Successfully registered. Reloading the page...', 'success');
            }
            pageRefresh();
          }
        },
        keep_disabled: false,
      });
    }
  }

  /**
   * Handle some adjustments when using a radio-button switch.
   */
  private loginRadioCheck(): void {
    if (this.login && this.password && this.button && this.rememberme && this.username) {
      let login_label;
      if (this.login.labels) {
        login_label = this.login.labels[0];
      }
      this.login.placeholder = 'Email or name';
      if (login_label) {
        login_label.textContent = 'Email or name';
      }
      //Set a pattern for login
      this.login.setAttribute('pattern', `^((${this.email_regex})|(${this.user_regex}))$`);
      //Additionally uncheck rememberme as precaution
      this.rememberme.checked = false;
      //Enforce the minimum length for password
      this.password.setAttribute('minlength', '8');
      //Autocomplete suggestion for login
      this.login.setAttribute('type', 'text');
      this.login.setAttribute('autocomplete', 'username');
      //Adjust elements based on the toggle
      if ((this.exist_user?.checked) === true) {
        //Whether a password field is required
        this.password.required = true;
        //Autocomplete suggestion for password
        this.password.setAttribute('autocomplete', 'current-password');
        //Adjust the name of the button
        this.button.textContent = 'Sign in';
        this.button.value = 'Sign in';
        //Show or hide the password field
        (this.password.parentElement as HTMLDivElement).classList.remove('hidden');
        //Show or hide remember me checkbox
        (this.rememberme.parentElement as HTMLDivElement).classList.remove('hidden');
        //Hide the username field
        (this.username.parentElement as HTMLDivElement).classList.add('hidden');
        this.username.required = false;
      }
      if ((this.new_user?.checked) === true) {
        this.password.required = true;
        this.password.setAttribute('autocomplete', 'new-password');
        this.login.setAttribute('type', 'email');
        this.login.setAttribute('autocomplete', 'email');
        this.login.setAttribute('pattern', `^${this.email_regex}$`);
        this.button.textContent = 'Join';
        this.button.value = 'Join';
        (this.password.parentElement as HTMLDivElement).classList.remove('hidden');
        (this.rememberme.parentElement as HTMLDivElement).classList.remove('hidden');
        this.login.placeholder = 'Email';
        if (login_label) {
          login_label.textContent = 'Email';
        }
        //Show the username field
        (this.username.parentElement as HTMLDivElement).classList.remove('hidden');
        this.username.required = true;
      }
      if ((this.forget?.checked) === true) {
        this.password.required = false;
        this.password.removeAttribute('autocomplete');
        this.password.removeAttribute('minlength');
        this.button.textContent = 'Remind';
        this.button.value = 'Remind';
        (this.password.parentElement as HTMLDivElement).classList.add('hidden');
        //Show or hide remember me checkbox
        (this.rememberme.parentElement as HTMLDivElement).classList.add('hidden');
        //Hide the username field
        (this.username.parentElement as HTMLDivElement).classList.add('hidden');
        this.username.required = false;
      }
    }
  }
}
