export class EditAvatars
{
    private readonly form: HTMLFormElement | null = null;
    private readonly currentAvatar: HTMLImageElement | null = null;
    private readonly sidebarAvatar: HTMLImageElement | null = null;
    private readonly previewAvatar: HTMLImageElement | null = null;
    private readonly avatarFile: HTMLInputElement | null = null;
    
    constructor()
    {
        this.currentAvatar = document.getElementById('currentAvatar') as HTMLImageElement;
        this.sidebarAvatar = document.getElementById('sidebarAvatar') as HTMLImageElement;
        this.previewAvatar = document.getElementById('previewAvatar') as HTMLImageElement;
        this.avatarFile = document.getElementById('new_avatar_file') as HTMLInputElement;
        this.form = document.getElementById('profile_avatar') as HTMLFormElement;
        //Attach form listener
        if (this.form) {
            submitIntercept(this.form, this.upload.bind(this));
        }
        //Attach listener to file upload fields
        if (this.avatarFile) {
            this.avatarFile.addEventListener('change', (event: Event) => {
                this.preview(event.target as HTMLInputElement);
            });
        }
        this.listen();
    }
    
    private listen()
    {
        //Listen to avatar change
        document.querySelectorAll('input[id^="avatar_"]').forEach(item => {
            item.addEventListener('change', (event: Event) => {
                this.setActive(event.target as HTMLInputElement);
            });
        });
        //Listen to avatar deletion
        document.querySelectorAll('input[id^="del_"]').forEach(item => {
            item.addEventListener('click', (event: Event) => {
                this.delete(event.target as HTMLInputElement);
            });
        });
    }
    
    //Function to update preview of the avatar
    public preview(field: HTMLInputElement)
    {
        if (this.previewAvatar) {
            if (field.id === 'new_avatar_file' && field.files && field.files[0]) {
                this.previewAvatar.src = URL.createObjectURL(field.files[0] as File);
                this.previewAvatar.classList.remove('hidden');
            } else {
                this.previewAvatar.classList.add('hidden');
            }
        }
    }
    
    //Upload a new avatar
    public upload()
    {
        //Get form data
        let formData = new FormData(this.form as HTMLFormElement);
        let button = (this.form as HTMLFormElement).querySelector('#avatar_submit');
        buttonToggle(button as HTMLInputElement);
        ajax(location.protocol+'//'+location.host+'/api/uc/avatars/add/', formData, 'json', 'POST', 60000, true).then(data => {
            if (data.data === true) {
                //Add avatar to the list
                this.addToList(data.location);
            } else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            //Remove file and preview
            if (this.avatarFile) {
                this.avatarFile.value = '';
                this.preview(this.avatarFile);
            }
            buttonToggle(button as HTMLInputElement);
        });
    }
    
    //Change current avatar
    public setActive(avatar: HTMLInputElement)
    {
        //Get li element
        let li = (avatar.parentElement as HTMLElement).closest('li');
        let formData = new FormData();
        formData.append('avatar', (li as HTMLElement).id);
        ajax(location.protocol+'//'+location.host+'/api/uc/avatars/setactive/', formData, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                //Update avatar on page
                this.refresh(data.location);
            } else {
                avatar.checked = false;
                new Snackbar(data.reason, 'failure', 10000);
            }
        });
    }
    
    //Function to refresh the current avatar on the page without reloading
    public refresh(avatar: string)
    {
        let hash = basename(avatar);
        console.log(avatar);
        console.log(hash);
        document.querySelectorAll('#avatars_list li').forEach(item => {
            let radio = item.querySelectorAll('input[id^=avatar_]')[0];
            let close = item.querySelectorAll('input[id^=del_]')[0];
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
    
    //Function to add avatar to list
    public addToList(avatar: string)
    {
        let hash = basename(avatar);
        //Create list item
        let li = '<li id="'+hash+'"><span class="radio_and_label"><input id="avatar_'+hash+'" type="radio" checked><label for="avatar_'+hash+'"><img loading="lazy" decoding="async" alt="New avatar" src="'+avatar+'" class="avatar"></label></span><input id="del_'+hash+'" alt="Delete avatar" type="image" class="delete_avatar hidden" disabled src="/img/close.svg"></li>';
        //Get the list
        let ul = document.getElementById('avatars_list');
        //Attach new item
        if (ul) {
            ul.innerHTML += li;
            //Attach listeners
            this.listen();
        }
        //Update avatar on the page
        this.refresh(avatar);
    }
    
    //Function to delete avatar
    public delete(avatar: HTMLInputElement)
    {
        //Get li element
        let li = (avatar.parentElement as HTMLElement).closest('li');
        let formData = new FormData();
        formData.append('avatar', (li as HTMLElement).id);
        ajax(location.protocol+'//'+location.host+'/api/uc/avatars/delete/', formData, 'json', 'DELETE', 60000, true).then(data => {
            if (data.data === true) {
                //Delete from list
                (li as HTMLElement).remove();
                //Update avatar on page
                this.refresh(data.location);
            } else {
                new Snackbar(data.reason, 'failure', 10000);
            }
        });
    }
}
