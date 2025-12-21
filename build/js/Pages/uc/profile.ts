export class EditProfile {
  private readonly usernameForm: HTMLFormElement | null = null;
  private readonly usernameSubmit: HTMLInputElement | null = null;
  private readonly usernameField: HTMLInputElement | null = null;
  private readonly profileForm: HTMLFormElement | null = null;
  private readonly profileSubmit: HTMLInputElement | null = null;
  private readonly aboutValue: HTMLTextAreaElement | null = null;
  private readonly autoTime: HTMLParagraphElement | null = null;
  private readonly timeTag: HTMLTimeElement | null = null;
  private profileFormData = '';
  private timeOut: number | null = null;

  public constructor() {
    this.aboutValue = document.querySelector('#about_value');
    this.usernameForm = document.querySelector('#profile_username');
    this.autoTime = document.querySelector('#last_auto_save');
    if (this.autoTime) {
      this.timeTag = this.autoTime.querySelector('time');
    }
    if (this.usernameForm) {
      this.usernameField = document.querySelector('#username_value');
      this.usernameSubmit = document.querySelector('#username_submit');
      ['focus', 'change', 'input',].forEach((eventType: string) => {
        if (this.usernameField) {
          this.usernameField.addEventListener(eventType, this.usernameOnChange.bind(this));
        }
      });
      this.usernameOnChange();
      submitIntercept(this.usernameForm, this.username.bind(this));
    }
    this.profileForm = document.querySelector('#profile_details');
    if (this.profileForm) {
      this.profileSubmit = document.querySelector('#details_submit');
      //Save initial values
      this.profileFormData = JSON.stringify([...new FormData(this.profileForm).entries()]);
      this.profileOnChange();
      //Monitor changes in all fields of the form
      ['select', 'textarea', 'input',].forEach((elementType: string) => {
        if (this.profileForm) {
          Array.from(this.profileForm.querySelectorAll(elementType)).forEach((element: Element) => {
                 ['focus', 'change', 'input',].forEach((eventType: string) => {
                   (element as HTMLElement).addEventListener(eventType, this.profileOnChange.bind(this));
                 });
               });
        }
      });
      submitIntercept(this.profileForm, this.profile.bind(this));
    }
  }

  private profile(auto = false): void {
    if (this.profileForm) {
      //Get form data
      const formData = new FormData(this.profileForm);
      void ajax(`${location.protocol}//${location.host}/api/uc/profile`, formData, 'json', 'PATCH', AJAX_TIMEOUT, true).then((response) => {
          const data = response as ajaxJSONResponse;
          if (data.data === true) {
            this.profileFormData = JSON.stringify([...formData.entries()]);
            this.profileOnChange();
            addSnackbar('Profile updated', 'success');
            //If auto-save, update the time
            if (auto) {
              this.autoTime?.classList.remove('hidden');
              if (this.timeTag) {
                const time = new Date();
                this.timeTag.setAttribute('datetime', time.toISOString());
                this.timeTag.innerHTML = time.toLocaleTimeString();
              }
            }
            //Notify TinyMCE, that data was saved
            if (this.aboutValue && !empty(this.aboutValue.id)) {
              saveTinyMCE(this.aboutValue.id);
            }
          } else {
            addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
          }
        });
    }
  }

  private profileOnChange(): void {
    if (this.profileForm && this.profileSubmit) {
      if (this.timeOut !== null) {
        window.clearTimeout(this.timeOut);
      }
      const formData = new FormData(this.profileForm);
      //Comparing stringify versions of data, because FormData === FormData always returns false
      this.profileSubmit.disabled = this.profileFormData === JSON.stringify([...formData.entries()]);
      if (!this.profileSubmit.disabled) {
        //Schedule auto save
        this.timeOut = window.setTimeout(() => {
          this.profile(true);
        }, 10000);
      }
    }
  }

  private usernameOnChange(): void {
    if (this.usernameField && this.usernameSubmit) {
      this.usernameSubmit.disabled = this.usernameField.getAttribute('data-original') === this.usernameField.value;
    }
  }

  private username(): void {
    if (this.usernameForm && this.usernameSubmit) {
      //Get form data
      const formData = new FormData(this.usernameForm);
      buttonToggle(this.usernameSubmit);
      void ajax(`${location.protocol}//${location.host}/api/uc/username`, formData, 'json', 'PATCH', AJAX_TIMEOUT, true).then((response) => {
          const data = response as ajaxJSONResponse;
          if (data.data === true) {
            (this.usernameField as HTMLInputElement).setAttribute('data-original', (this.usernameField as HTMLInputElement).value);
            this.usernameOnChange();
            addSnackbar('Username changed', 'success');
          } else {
            addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
          }
          if (this.usernameSubmit) {
            buttonToggle(this.usernameSubmit);
          }
        });
    }
  }
}
