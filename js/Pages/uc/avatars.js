export class EditAvatars {
    form = null;
    currentAvatar = null;
    sidebarAvatar = null;
    avatarFile = null;
    constructor() {
        this.currentAvatar = document.getElementById('currentAvatar');
        this.sidebarAvatar = document.getElementById('sidebarAvatar');
        this.avatarFile = document.getElementById('new_avatar_file');
        this.form = document.getElementById('profile_avatar');
        if (this.form) {
            submitIntercept(this.form, this.upload.bind(this));
        }
        this.listen();
    }
    listen() {
        document.querySelectorAll('input[id^="avatar_"]').forEach(item => {
            item.addEventListener('change', (event) => {
                this.setActive(event.target);
            });
        });
        document.querySelectorAll('input[id^="del_"]').forEach(item => {
            item.addEventListener('click', (event) => {
                this.delete(event.target);
            });
        });
    }
    upload() {
        if (this.avatarFile && this.avatarFile.files.length === 0) {
            new Snackbar('No file selected', 'failure', 10000);
            return;
        }
        if (this.avatarFile.files[0].size === 0) {
            new Snackbar('Selected file is empty', 'failure', 10000);
            return;
        }
        let formData = new FormData(this.form);
        let button = this.form.querySelector('#avatar_submit');
        buttonToggle(button);
        ajax(location.protocol + '//' + location.host + '/api/uc/avatars/add/', formData, 'json', 'POST', 60000, true).then(data => {
            if (data.data === true) {
                this.addToList(data.location);
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            if (this.avatarFile) {
                this.avatarFile.value = '';
                this.avatarFile.dispatchEvent(new Event('change'));
            }
            buttonToggle(button);
        });
    }
    setActive(avatar) {
        let li = avatar.parentElement.closest('li');
        let formData = new FormData();
        formData.append('avatar', li.id);
        ajax(location.protocol + '//' + location.host + '/api/uc/avatars/setactive/', formData, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                this.refresh(data.location);
            }
            else {
                avatar.checked = false;
                new Snackbar(data.reason, 'failure', 10000);
            }
        });
    }
    refresh(avatar) {
        let hash = basename(avatar);
        document.querySelectorAll('#avatars_list li').forEach(item => {
            let radio = item.querySelector('input[id^=avatar_]');
            let close = item.querySelector('input[id^=del_]');
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
    addToList(avatar) {
        let hash = basename(avatar);
        let template = document.querySelector('#avatar_item').content.cloneNode(true);
        template.querySelector('li').id = hash;
        let inputs = template.querySelectorAll('input');
        inputs[0].id = inputs[0].id.replace('hash', hash);
        inputs[1].id = inputs[1].id.replace('hash', hash);
        inputs[0].addEventListener('change', (event) => {
            this.setActive(event.target);
        });
        inputs[1].addEventListener('click', (event) => {
            this.delete(event.target);
        });
        template.querySelector('label').setAttribute('for', String(template.querySelector('label').getAttribute('for')).replace('hash', hash));
        template.querySelector('img').src = avatar;
        let ul = document.getElementById('avatars_list');
        if (ul) {
            ul.appendChild(template);
        }
        this.refresh(avatar);
    }
    delete(avatar) {
        let li = avatar.parentElement.closest('li');
        let formData = new FormData();
        formData.append('avatar', li.id);
        ajax(location.protocol + '//' + location.host + '/api/uc/avatars/delete/', formData, 'json', 'DELETE', 60000, true).then(data => {
            if (data.data === true) {
                li.remove();
                this.refresh(data.location);
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
        });
    }
}
//# sourceMappingURL=avatars.js.map