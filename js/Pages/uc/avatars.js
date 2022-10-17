export class EditAvatars {
    form = null;
    constructor() {
        this.form = document.getElementById('profile_avatar');
        if (this.form) {
            submitIntercept(this.form, this.upload.bind(this));
        }
    }
    upload() {
        let formData = new FormData(this.form);
        let spinner = document.getElementById('avatar_spinner');
        formData.append('avatarUpload', 'true');
        spinner.classList.remove('hidden');
        ajax(location.protocol + '//' + location.host + '/api/upload/', formData, 'json', 'POST', 60000, true).then(data => {
            if (data.data === true) {
                new Snackbar('File <a target="_blank" href="' + data.location + '">uploaded</a> successfully', 'success', 10000);
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            spinner.classList.add('hidden');
        });
    }
}
//# sourceMappingURL=avatars.js.map