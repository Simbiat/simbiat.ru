export class Posts
{
    private readonly postForm: HTMLFormElement | null = null;
    private readonly deletePostButton: HTMLInputElement | null = null;
    
    constructor()
    {
        this.postForm = document.querySelector('post-form form') as HTMLFormElement;
        this.deletePostButton = document.getElementById('delete_post') as HTMLInputElement;
        //Listener for form
        if (this.postForm) {
            submitIntercept(this.postForm, this.editPost.bind(this));
        }
        //Listener for deletion
        if (this.deletePostButton) {
            this.deletePostButton.addEventListener('click', () => {
                this.deletePost();
            });
        }
    }
    
    private editPost()
    {
        if (this.postForm) {
            //Get submit button
            let button = this.postForm.querySelector('input[type=submit]')
            //Get form data
            let formData = new FormData(this.postForm);
            buttonToggle(button as HTMLInputElement);
            ajax(location.protocol + '//' + location.host + '/api/talks/posts/'+(formData.get('postForm[postid]') ?? '0')+'/edit/', formData, 'json', 'PATCH', 60000, true).then(data => {
                if (data.data === true) {
                    //Notify TinyMCE, that data was saved
                    let textarea = (this.postForm as HTMLFormElement).querySelector('textarea');
                    if (textarea && textarea.id) {
                        saveTinyMCE(textarea.id)
                    }
                    new Snackbar('Post updated. Reloading...', 'success');
                    window.location.href = window.location.href+'?forceReload=true';
                } else {
                    new Snackbar(data.reason, 'failure', 10000);
                    buttonToggle(button as HTMLInputElement);
                }
            });
        }
    }
    
    private deletePost()
    {
        if (this.deletePostButton) {
            if (confirm('This is the last chance to back out.\nIf you press \'OK\' this post will be permanently deleted.\nPress \'Cancel\' to cancel the action.')) {
                let id = this.deletePostButton.getAttribute('data-post');
                if (id) {
                    buttonToggle(this.deletePostButton as HTMLInputElement);
                    ajax(location.protocol + '//' + location.host + '/api/talks/posts/' + id + '/delete/', null, 'json', 'DELETE', 60000, true).then(data => {
                        if (data.data === true) {
                            new Snackbar('Post removed. Redirecting to thread...', 'success');
                            window.location.href = data.location;
                        } else {
                            new Snackbar(data.reason, 'failure', 10000);
                        }
                        buttonToggle(this.deletePostButton as HTMLInputElement);
                    });
                }
            }
        }
    }
}
