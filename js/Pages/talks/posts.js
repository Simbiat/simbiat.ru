export class Posts {
    postForm = null;
    deletePostButton = null;
    constructor() {
        this.postForm = document.querySelector('post-form form');
        this.deletePostButton = document.getElementById('delete_post');
        if (this.postForm) {
            submitIntercept(this.postForm, this.editPost.bind(this));
        }
        if (this.deletePostButton) {
            this.deletePostButton.addEventListener('click', () => {
                this.deletePost();
            });
        }
    }
    editPost() {
        if (this.postForm) {
            let button = this.postForm.querySelector('input[type=submit]');
            let formData = new FormData(this.postForm);
            buttonToggle(button);
            ajax(location.protocol + '//' + location.host + '/api/talks/posts/' + (formData.get('postForm[postid]') ?? '0') + '/edit/', formData, 'json', 'PATCH', 60000, true).then(data => {
                if (data.data === true) {
                    let textarea = this.postForm.querySelector('textarea');
                    if (textarea && textarea.id) {
                        saveTinyMCE(textarea.id);
                    }
                    new Snackbar('Post updated. Reloading...', 'success');
                    location.reload();
                }
                else {
                    new Snackbar(data.reason, 'failure', 10000);
                    buttonToggle(button);
                }
            });
        }
    }
    deletePost() {
        if (this.deletePostButton) {
            if (confirm('This is the last chance to back out.\nIf you press \'OK\' this post will be permanently deleted.\nPress \'Cancel\' to cancel the action.')) {
                let id = this.deletePostButton.getAttribute('data-post');
                if (id) {
                    buttonToggle(this.deletePostButton);
                    ajax(location.protocol + '//' + location.host + '/api/talks/posts/' + id + '/delete/', null, 'json', 'DELETE', 60000, true).then(data => {
                        if (data.data === true) {
                            new Snackbar('Post removed. Redirecting to thread...', 'success');
                            window.location.href = data.location;
                        }
                        else {
                            new Snackbar(data.reason, 'failure', 10000);
                        }
                        buttonToggle(this.deletePostButton);
                    });
                }
            }
        }
    }
}
//# sourceMappingURL=posts.js.map