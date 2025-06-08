export class Posts
{
    private readonly post_form: HTMLFormElement | null = null;
    private readonly deletePostButton: HTMLInputElement | null = null;
    
    public constructor()
    {
        this.post_form = document.querySelector('post-form form');
        this.deletePostButton = document.querySelector('#delete_post');
        //Listener for form
        if (this.post_form) {
            submitIntercept(this.post_form, this.editPost.bind(this));
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
        if (this.post_form) {
            //Get submit button
            const button = this.post_form.querySelector('input[type=submit]');
            //Get form data
            const formData = new FormData(this.post_form);
            buttonToggle(button as HTMLInputElement);
            ajax(`${location.protocol}//${location.host}/api/talks/posts/${String(formData.get('post_form[post_id]') ?? '0')}/edit`, formData, 'json', 'POST', ajaxTimeout, true)
                .then((response) => {
                    const data = response as ajaxJSONResponse;
                    if (data.data === true) {
                        //Notify TinyMCE, that data was saved
                        if (this.post_form) {
                            const textarea = this.post_form.querySelector('textarea');
                            if (textarea && !empty(textarea.id)) {
                                saveTinyMCE(textarea.id);
                            }
                        }
                        addSnackbar('Post updated. Reloading...', 'success');
                        //pageRefresh();
                        window.location.href = data.location;
                    } else {
                        if (data.location) {
                            addSnackbar(data.reason + ` View the post <a href="${data.location}" target="_blank">here</a>.`, 'failure', 0);
                        } else {
                            addSnackbar(data.reason, 'failure', snackbarFailLife);
                        }
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
                    ajax(`${location.protocol}//${location.host}/api/talks/posts/${id}/delete`, null, 'json', 'DELETE', ajaxTimeout, true)
                        .then((response) => {
                            const data = response as ajaxJSONResponse;
                            if (data.data === true) {
                                addSnackbar('Post removed. Redirecting to thread...', 'success');
                                window.location.href = data.location;
                            } else {
                                addSnackbar(data.reason, 'failure', snackbarFailLife);
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
