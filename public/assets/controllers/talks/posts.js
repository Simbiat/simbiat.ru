export class Posts {
    post_form = null;
    deletePostButton = null;
    constructor() {
        this.post_form = document.querySelector('post-form form');
        this.deletePostButton = document.querySelector('#delete_post');
        if (this.post_form) {
            submitIntercept(this.post_form, this.editPost.bind(this));
        }
        if (this.deletePostButton) {
            this.deletePostButton.addEventListener('click', () => {
                this.deletePost();
            });
        }
    }
    editPost() {
        if (this.post_form) {
            const button = this.post_form.querySelector('input[type=submit]');
            const formData = new FormData(this.post_form);
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/posts/${String(formData.get('post_form[post_id]') ?? '0')}/edit`, formData, 'json', 'POST', AJAX_TIMEOUT, true)
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
                    window.location.assign(encodeURI(data.location));
                }
                else {
                    if (data.location) {
                        addSnackbar(data.reason + ` View the post <a href="${data.location}" target="_blank">here</a>.`, 'failure', 0);
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
        if (this.deletePostButton) {
            if (confirm('This is the last chance to back out.\nIf you press \'OK\' this post will be permanently deleted.\nPress \'Cancel\' to cancel the action.')) {
                const id = this.deletePostButton.getAttribute('data-post') ?? '';
                if (!empty(id)) {
                    buttonToggle(this.deletePostButton);
                    ajax(`${location.protocol}//${location.host}/api/talks/posts/${id}/delete`, null, 'json', 'DELETE', AJAX_TIMEOUT, true)
                        .then((response) => {
                        const data = response;
                        if (data.data === true) {
                            addSnackbar('Post removed. Redirecting to thread...', 'success');
                            window.location.assign(encodeURI(data.location));
                        }
                        else {
                            addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
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
//# sourceMappingURL=posts.js.map