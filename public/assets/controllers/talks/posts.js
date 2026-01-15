export class Posts {
    post_form = null;
    delete_post_button = null;
    constructor() {
        this.post_form = document.querySelector('post-form form');
        this.delete_post_button = document.querySelector('#delete_post');
        if (this.post_form) {
            submitIntercept(this.post_form, this.editPost.bind(this));
        }
        if (this.delete_post_button) {
            this.delete_post_button.addEventListener('click', () => {
                this.deletePost();
            });
        }
    }
    editPost() {
        if (this.post_form) {
            const button = this.post_form.querySelector('input[type=submit]');
            const form_data = new FormData(this.post_form);
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/posts/${String(form_data.get('post_data[post_id]') ?? '0')}/edit`, form_data, 'json', 'POST', AJAX_TIMEOUT, true)
                .then((response) => {
                const data = response;
                if (data.data === true) {
                    if (this.post_form) {
                        const textarea = this.post_form.querySelector('textarea');
                        if (textarea && !empty(textarea.id)) {
                            saveTinyMCE(textarea.id);
                        }
                    }
                    addSnackbar('Post updated. Reloading...', 'success');
                    pageRefresh(data.location);
                }
                else {
                    if (data.location) {
                        addSnackbar(data.reason + ` View the post <a href="${data.location}" target="_blank" rel="noopener noreferrer">here</a>.`, 'failure', 0);
                    }
                    else {
                        addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                    }
                    buttonToggle(button);
                }
            });
        }
    }
    deletePost() {
        if (this.delete_post_button) {
            if (confirm('This is the last chance to back out.\nIf you press \'OK\' this post will be permanently deleted.\nPress \'Cancel\' to cancel the action.')) {
                const id = this.delete_post_button.getAttribute('data-post') ?? '';
                if (!empty(id)) {
                    buttonToggle(this.delete_post_button);
                    ajax(`${location.protocol}//${location.host}/api/talks/posts/${id}/delete`, null, 'json', 'DELETE', AJAX_TIMEOUT, true)
                        .then((response) => {
                        const data = response;
                        if (data.data === true) {
                            addSnackbar('Post removed. Redirecting to thread...', 'success');
                            pageRefresh(data.location);
                        }
                        else {
                            addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                        }
                        if (this.delete_post_button) {
                            buttonToggle(this.delete_post_button);
                        }
                    });
                }
            }
        }
    }
}
//# sourceMappingURL=posts.js.map