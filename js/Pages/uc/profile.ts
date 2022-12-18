export class EditProfile
{
    private readonly usernameForm: HTMLFormElement | null = null;
    private readonly usernameSubmit: HTMLInputElement | null = null;
    private readonly usernameField: HTMLInputElement | null = null;
    private readonly profileForm: HTMLFormElement | null = null;
    private readonly profileSubmit: HTMLInputElement | null = null;
    private profileFormData: string = '';
    private timeOut: number | null = null;

    constructor()
    {
        this.usernameForm = document.getElementById('profile_username') as HTMLFormElement;
        if (this.usernameForm) {
            this.usernameField = document.getElementById('username_value') as HTMLInputElement;
            this.usernameSubmit = document.getElementById('username_submit') as HTMLInputElement;
            ['focus', 'change', 'input',].forEach((eventType: string) => {
                (this.usernameField as HTMLInputElement).addEventListener(eventType, this.usernameOnChange.bind(this));
            });
            this.usernameOnChange();
            submitIntercept(this.usernameForm, this.username.bind(this));
        }
        this.profileForm = document.getElementById('profile_details') as HTMLFormElement;
        if (this.profileForm) {
            this.profileSubmit = document.getElementById('details_submit') as HTMLInputElement;
            //Save initial values
            this.profileFormData = JSON.stringify([...new FormData(this.profileForm).entries()]);
            this.profileOnChange();
            //Monitor changes in all fields of the form
            ['select', 'textarea', 'input',].forEach((elementType: string) => {
                Array.from((this.profileForm as HTMLFormElement).getElementsByTagName(elementType)).forEach((element: Element) => {
                    ['focus', 'change', 'input',].forEach((eventType: string) => {
                        (element as HTMLElement).addEventListener(eventType, this.profileOnChange.bind(this));
                    });
                });
            });
            submitIntercept(this.profileForm, this.profile.bind(this));
        }
    }
    
    private profile(auto: boolean = false): void
    {
        //Get form data
        let formData = new FormData(this.profileForm as HTMLFormElement);
        let button = (this.profileForm as HTMLFormElement).querySelector('#details_submit');
        buttonToggle(button as HTMLInputElement);
        ajax(location.protocol+'//'+location.host+'/api/uc/profile/', formData, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                this.profileFormData = JSON.stringify([...formData.entries()]);
                this.profileOnChange();
                new Snackbar('Profile updated', 'success');
                //If auto-save, update the time
                if (auto) {
                    let autoTime = document.getElementById('lastAutoSave') as HTMLParagraphElement;
                    autoTime.classList.remove('hidden');
                    let timeTag = autoTime.querySelector('time') as HTMLTimeElement;
                    let time = new Date();
                    timeTag.setAttribute('datetime', time.toISOString());
                    timeTag.innerHTML = time.toLocaleTimeString();
                }
            } else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(button as HTMLInputElement);
        });
    }
    
    private profileOnChange(): void
    {
        if (this.timeOut) {
            clearTimeout(this.timeOut);
        }
        let formData = new FormData(this.profileForm as HTMLFormElement);
        //Comparing stringify versions of data, because FormData === FormData always returns false
        (this.profileSubmit as HTMLInputElement).disabled = this.profileFormData === JSON.stringify([...formData.entries()]);
        if (!(this.profileSubmit as HTMLInputElement).disabled) {
            //Schedule auto save
            this.timeOut = setTimeout(() => {this.profile(true)}, 10000);
        }
    }
    
    private usernameOnChange(): void
    {
        (this.usernameSubmit as HTMLInputElement).disabled = (this.usernameField as HTMLInputElement).getAttribute('data-original') === (this.usernameField as HTMLInputElement).value;
    }
    
    private username(): void
    {
        //Get form data
        let formData = new FormData(this.usernameForm as HTMLFormElement);
        let button = (this.usernameForm as HTMLFormElement).querySelector('#username_submit');
        buttonToggle(button as HTMLInputElement);
        ajax(location.protocol+'//'+location.host+'/api/uc/username/', formData, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                (this.usernameField as HTMLInputElement).setAttribute('data-original', (this.usernameField as HTMLInputElement).value);
                this.usernameOnChange();
                new Snackbar('Username changed', 'success');
            } else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(button as HTMLInputElement);
        });
    }
}
