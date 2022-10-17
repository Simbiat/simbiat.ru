export class EditAvatars
{
    private readonly form: HTMLFormElement | null = null;
    
    constructor()
    {
        this.form = document.getElementById('profile_avatar') as HTMLFormElement;
        if (this.form) {
            submitIntercept(this.form, this.upload.bind(this));
        }
    }
    
    public upload()
    {
        //Get form data
        let formData = new FormData(this.form as HTMLFormElement);
        let spinner = document.getElementById('avatar_spinner') as HTMLImageElement;
        formData.append('avatarUpload', 'true');
        spinner.classList.remove('hidden');
        ajax(location.protocol+'//'+location.host+'/api/upload/', formData, 'json', 'POST', 60000, true).then(data => {
            if (data.data === true) {
                new Snackbar('File <a target="_blank" href="'+data.location+'">uploaded</a> successfully', 'success', 10000);
            } else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            spinner.classList.add('hidden');
        });
    }
}
