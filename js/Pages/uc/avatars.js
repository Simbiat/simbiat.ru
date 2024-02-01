export class EditAvatars {
    form = null;
    currentAvatar = null;
    sidebarAvatar = null;
    avatarFile = null;
    avatarsList = null;
    template = null;
    constructor() {
        this.currentAvatar = document.querySelector('#currentAvatar');
        this.sidebarAvatar = document.querySelector('#sidebarAvatar');
        this.avatarFile = document.querySelector('#profile_avatar_file');
        this.form = document.querySelector('#profile_avatar');
        this.avatarsList = document.querySelector('#avatars_list');
        this.template = document.querySelector('#avatar_item');
        if (this.form) {
            submitIntercept(this.form, this.upload.bind(this));
        }
        this.listen();
    }
    listen() {
        document.querySelectorAll('input[id^="avatar_"]').forEach((item) => {
            item.addEventListener('change', (event) => {
                this.setActive(event.target);
            });
        });
        document.querySelectorAll('input[id^="del_"]').forEach((item) => {
            item.addEventListener('click', (event) => {
                this.delete(event.target);
            });
        });
    }
    upload() {
        if (this.form) {
            if (this.avatarFile?.files && this.avatarFile.files.length === 0) {
                addSnackbar('No file selected', 'failure', 10000);
                return;
            }
            if (this.avatarFile?.files && this.avatarFile.files[0] && this.avatarFile.files[0].size === 0) {
                addSnackbar('Selected file is empty', 'failure', 10000);
                return;
            }
            const formData = new FormData(this.form);
            const button = this.form.querySelector('#avatar_submit');
            buttonToggle(button);
            void ajax(`${location.protocol}//${location.host}/api/uc/avatars/add`, formData, 'json', 'POST', 60000, true).
                then((response) => {
                const data = response;
                if (data.data === true) {
                    this.addToList(data.location);
                }
                else {
                    addSnackbar(data.reason, 'failure', 10000);
                }
                if (this.avatarFile) {
                    this.avatarFile.value = '';
                    this.avatarFile.dispatchEvent(new Event('change'));
                }
                buttonToggle(button);
            });
        }
    }
    setActive(avatar) {
        const li = avatar.parentElement?.closest('li');
        if (li) {
            const formData = new FormData();
            formData.append('avatar', li.id);
            void ajax(`${location.protocol}//${location.host}/api/uc/avatars/setactive`, formData, 'json', 'PATCH', 60000, true).then((response) => {
                const data = response;
                if (data.data === true) {
                    this.refresh(data.location);
                }
                else {
                    avatar.checked = false;
                    addSnackbar(data.reason, 'failure', 10000);
                }
            });
        }
    }
    refresh(avatar) {
        const hash = basename(avatar);
        if (this.avatarsList) {
            this.avatarsList.querySelectorAll('li').
                forEach((item) => {
                const radio = item.querySelector('input[id^=avatar_]');
                const close = item.querySelector('input[id^=del_]');
                if (item.id === hash) {
                    radio.checked = true;
                    close.classList.add('hidden');
                    close.disabled = true;
                }
                else {
                    radio.checked = false;
                    close.classList.remove('hidden');
                    close.disabled = false;
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
    addToList(avatar) {
        const hash = basename(avatar);
        if (this.template) {
            const clone = this.template.content.cloneNode(true);
            const li = clone.querySelector('li');
            if (li) {
                li.id = hash;
            }
            const inputs = clone.querySelectorAll('input');
            if (inputs[0]) {
                inputs[0].id = inputs[0].id.replace('hash', hash);
                inputs[0].addEventListener('change', (event) => {
                    this.setActive(event.target);
                });
            }
            if (inputs[1]) {
                inputs[1].id = inputs[1].id.replace('hash', hash);
                inputs[1].addEventListener('click', (event) => {
                    this.delete(event.target);
                });
            }
            const label = clone.querySelector('label');
            if (label) {
                label.setAttribute('for', String(label.getAttribute('for')).replace('hash', hash));
            }
            const img = clone.querySelector('img');
            if (img) {
                img.src = avatar;
            }
            if (this.avatarsList) {
                this.avatarsList.appendChild(clone);
            }
            this.refresh(avatar);
        }
    }
    delete(avatar) {
        const li = avatar.parentElement?.closest('li');
        if (li) {
            const formData = new FormData();
            formData.append('avatar', li.id);
            void ajax(`${location.protocol}//${location.host}/api/uc/avatars/delete`, formData, 'json', 'DELETE', 60000, true).then((response) => {
                const data = response;
                if (data.data === true) {
                    li.remove();
                    this.refresh(data.location);
                }
                else {
                    addSnackbar(data.reason, 'failure', 10000);
                }
            });
        }
    }
}
//# sourceMappingURL=avatars.js.map