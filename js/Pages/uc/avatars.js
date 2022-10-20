export class EditAvatars {
    form = null;
    currentAvatar = null;
    sidebarAvatar = null;
    previewAvatar = null;
    avatarFile = null;
    constructor() {
        this.currentAvatar = document.getElementById('currentAvatar');
        this.sidebarAvatar = document.getElementById('sidebarAvatar');
        this.previewAvatar = document.getElementById('previewAvatar');
        this.avatarFile = document.getElementById('new_avatar_file');
        this.form = document.getElementById('profile_avatar');
        if (this.form) {
            submitIntercept(this.form, this.upload.bind(this));
        }
        if (this.avatarFile) {
            this.avatarFile.addEventListener('change', (event) => {
                this.preview(event.target);
            });
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
    preview(field) {
        if (this.previewAvatar) {
            if (field.id === 'new_avatar_file' && field.files && field.files[0]) {
                this.previewAvatar.src = URL.createObjectURL(field.files[0]);
                this.previewAvatar.classList.remove('hidden');
            }
            else {
                this.previewAvatar.classList.add('hidden');
            }
        }
    }
    upload() {
        let formData = new FormData(this.form);
        let spinner = document.getElementById('avatar_spinner');
        spinner.classList.remove('hidden');
        ajax(location.protocol + '//' + location.host + '/api/uc/avatars/add/', formData, 'json', 'POST', 60000, true).then(data => {
            if (data.data === true) {
                this.addToList(data.location);
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            if (this.avatarFile) {
                this.avatarFile.value = '';
                this.preview(this.avatarFile);
            }
            spinner.classList.add('hidden');
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
        console.log(avatar);
        console.log(hash);
        document.querySelectorAll('#avatars_list li').forEach(item => {
            let radio = item.querySelectorAll('input[id^=avatar_]')[0];
            let close = item.querySelectorAll('input[id^=del_]')[0];
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
        let li = '<li id="' + hash + '"><span class="radio_and_label"><input id="avatar_' + hash + '" type="radio" checked><label for="avatar_' + hash + '"><img loading="lazy" decoding="async" alt="New avatar" src="' + avatar + '" class="avatar"></label></span><input id="del_' + hash + '" alt="Delete avatar" type="image" class="delete_avatar hidden" disabled src="/img/close.svg"></li>';
        let ul = document.getElementById('avatars_list');
        if (ul) {
            ul.innerHTML += li;
            this.listen();
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