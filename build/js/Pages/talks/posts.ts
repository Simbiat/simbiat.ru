export class Posts
{
    private readonly postForm: HTMLFormElement | null = null;
    private readonly deletePostButton: HTMLInputElement | null = null;
    
    public constructor()
    {
        this.postForm = document.querySelector('post-form form');
        this.deletePostButton = document.querySelector('#delete_post');
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
    
    private editPost(): void
    {
        if (this.postForm) {
            //Get submit button
            const button = this.postForm.querySelector('input[type=submit]');
            //Get form data
            const formData = new FormData(this.postForm);
            buttonToggle(button as HTMLInputElement);
            void ajax(`${location.protocol}//${location.host}/api/talks/posts/${String(formData.get('postForm[postid]') ?? '0')}/edit`, formData, 'json', 'POST', 60000, true).then((response) => {
                const data = response as ajaxJSONResponse;
                if (data.data === true) {
                    //Notify TinyMCE, that data was saved
                    if (this.postForm) {
                        const textarea = this.postForm.querySelector('textarea');
                        if (textarea && !empty(textarea.id)) {
                            saveTinyMCE(textarea.id);
                        }
                    }
                    addSnackbar('Post updated. Reloading...', 'success');
                    //pageRefresh();
                    window.location.href = data.location;
                } else {
                    addSnackbar(data.reason, 'failure', 10000);
                    buttonToggle(button as HTMLInputElement);
                }
            });
        }
    }
    
    private deletePost(): void
    {
        if (this.deletePostButton) {
            if (confirm('This is the last chance to back out.\nIf you press \'OK\' this post will be permanently deleted.\nPress \'Cancel\' to cancel the action.')) {
                const id = this.deletePostButton.getAttribute('data-post') ?? '';
                if (!empty(id)) {
                    buttonToggle(this.deletePostButton);
                    void ajax(`${location.protocol}//${location.host}/api/talks/posts/${id}/delete`, null, 'json', 'DELETE', 60000, true).then((response) => {
                        const data = response as ajaxJSONResponse;
                        if (data.data === true) {
                            addSnackbar('Post removed. Redirecting to thread...', 'success');
                            window.location.href = data.location;
                        } else {
                            addSnackbar(data.reason, 'failure', 10000);
                        }
                        if (this.deletePostButton) {
                            buttonToggle(this.deletePostButton);
                        }
                    });
                }
            }
        }
    }
}
