export class EditAvatars
{
    private readonly form: HTMLFormElement | null = null;
    private readonly currentAvatar: HTMLImageElement | null = null;
    private readonly sidebarAvatar: HTMLImageElement | null = null;
    private readonly avatarFile: HTMLInputElement | null = null;
    private readonly avatarsList: HTMLUListElement | null = null;
    private readonly template: HTMLTemplateElement | null = null;
    
    public constructor()
    {
        this.currentAvatar = document.querySelector('#currentAvatar');
        this.sidebarAvatar = document.querySelector('#sidebarAvatar');
        this.avatarFile = document.querySelector('#profile_avatar_file');
        this.form = document.querySelector('#profile_avatar');
        this.avatarsList = document.querySelector('#avatars_list');
        this.template = document.querySelector('#avatar_item');
        //Attach form listener
        if (this.form) {
            submitIntercept(this.form, this.upload.bind(this));
        }
        this.listen();
    }
    
    private listen(): void
    {
        //Listen to avatar change
        document.querySelectorAll('input[id^="avatar_"]').forEach((item) => {
            item.addEventListener('change', (event: Event) => {
                this.setActive(event.target as HTMLInputElement);
            });
        });
        //Listen to avatar deletion
        document.querySelectorAll('input[id^="del_"]').forEach((item) => {
            item.addEventListener('click', (event: MouseEvent) => {
                this.delete(event.target as HTMLInputElement);
            });
        });
    }
    
    //Upload a new avatar
    private upload(): void
    {
        if (this.form) {
            if (this.avatarFile?.files && this.avatarFile.files.length === 0) {
                addSnackbar('No file selected', 'failure', snackbarFailLife);
                return;
            }
            if (this.avatarFile?.files && this.avatarFile.files[0] && this.avatarFile.files[0].size === 0) {
                addSnackbar('Selected file is empty', 'failure', snackbarFailLife);
                return;
            }
            //Get form data
            const formData = new FormData(this.form);
            const button = this.form.querySelector('#avatar_submit');
            buttonToggle(button as HTMLInputElement);
            void ajax(`${location.protocol}//${location.host}/api/uc/avatars/add`, formData, 'json', 'POST', ajaxTimeout, true).
                then((response) => {
                    const data = response as ajaxJSONResponse;
                    if (data.data === true) {
                        //Add avatar to the list
                        this.addToList(data.location);
                    } else {
                        addSnackbar(data.reason, 'failure', snackbarFailLife);
                    }
                    //Remove file and preview
                    if (this.avatarFile) {
                        this.avatarFile.value = '';
                        this.avatarFile.dispatchEvent(new Event('change'));
                    }
                    buttonToggle(button as HTMLInputElement);
                });
        }
    }
    
    //Change current avatar
    private setActive(avatar: HTMLInputElement): void
    {
        //Get li element
        const li = avatar.parentElement?.closest('li');
        if (li) {
            const formData = new FormData();
            formData.append('avatar', (li as HTMLElement).id);
            void ajax(`${location.protocol}//${location.host}/api/uc/avatars/setactive`, formData, 'json', 'PATCH', ajaxTimeout, true).then((response) => {
                const data = response as ajaxJSONResponse;
                if (data.data === true) {
                    //Update avatar on page
                    this.refresh(data.location);
                } else {
                    avatar.checked = false;
                    addSnackbar(data.reason, 'failure', snackbarFailLife);
                }
            });
        }
    }
    
    //Function to refresh the current avatar on the page without reloading
    private refresh(avatar: string): void {
        const hash = basename(avatar);
        if (this.avatarsList) {
            this.avatarsList.querySelectorAll('li').
                forEach((item) => {
                    const radio = item.querySelector('input[id^=avatar_]');
                    const close = item.querySelector('input[id^=del_]');
                    //Deselect all nodes, that are not current one
                    if (item.id === hash) {
                        (radio as HTMLInputElement).checked = true;
                        //Remove "delete" button
                        (close as HTMLInputElement).classList.add('hidden');
                        (close as HTMLInputElement).disabled = true;
                    } else {
                        (radio as HTMLInputElement).checked = false;
                        //Show "delete" button
                        (close as HTMLInputElement).classList.remove('hidden');
                        (close as HTMLInputElement).disabled = false;
                    }
                });
            if (this.currentAvatar) {
                this.currentAvatar.src = avatar;
            }
            if (this.sidebarAvatar) {
                this.sidebarAvatar.src = avatar;
            }
        }
    }
    
    //Function to add avatar to list
    private addToList(avatar: string): void
    {
        const hash = basename(avatar);
        if (this.template) {
            //Create list item
            const clone = this.template.content.cloneNode(true) as HTMLElement;
            //Set ID for the item
            const li = clone.querySelector('li');
            if (li) {
                li.id = hash;
            }
            //Set attributes for inputs and attach listeners
            const inputs = clone.querySelectorAll('input');
            if (inputs[0]) {
                inputs[0].id = inputs[0].id.replace('hash', hash);
                inputs[0].addEventListener('change', (event: Event) => {
                    this.setActive(event.target as HTMLInputElement);
                });
            }
            if (inputs[1]) {
                inputs[1].id = inputs[1].id.replace('hash', hash);
                inputs[1].addEventListener('click', (event: MouseEvent) => {
                    this.delete(event.target as HTMLInputElement);
                });
            }
            //Update label
            const label: HTMLLabelElement | null = clone.querySelector('label');
            if (label) {
                label.setAttribute('for', String(label.getAttribute('for')).replace('hash', hash));
            }
            //Update image source
            const img: HTMLImageElement | null = clone.querySelector('img');
            if (img) {
                img.src = avatar;
            }
            //Attach new item to the list
            if (this.avatarsList) {
                this.avatarsList.appendChild(clone);
            }
            //Update avatar on the page
            this.refresh(avatar);
        }
    }
    
    //Function to delete avatar
    private delete(avatar: HTMLInputElement): void
    {
        //Get li element
        const li = avatar.parentElement?.closest('li');
        if (li) {
            const formData = new FormData();
            formData.append('avatar', (li as HTMLElement).id);
            void ajax(`${location.protocol}//${location.host}/api/uc/avatars/delete`, formData, 'json', 'DELETE', ajaxTimeout, true).then((response) => {
                const data = response as ajaxJSONResponse;
                if (data.data === true) {
                    //Delete from list
                    (li as HTMLElement).remove();
                    //Update avatar on page
                    this.refresh(data.location);
                } else {
                    addSnackbar(data.reason, 'failure', snackbarFailLife);
                }
            });
        }
    }
}
