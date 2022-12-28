export class Threads {
    addPostForm = null;
    editThreadForm = null;
    closeThreadButton = null;
    deleteThreadButton = null;
    postForm = null;
    ogimage = null;
    constructor() {
        this.addPostForm = document.getElementById('postForm');
        this.editThreadForm = document.getElementById('editThreadForm');
        this.closeThreadButton = document.getElementById('close_thread');
        this.deleteThreadButton = document.getElementById('delete_thread');
        this.ogimage = document.getElementById('thread_ogimage');
        this.postForm = document.querySelector('post-form');
        if (this.addPostForm) {
            submitIntercept(this.addPostForm, this.addPost.bind(this));
        }
        if (this.editThreadForm) {
            submitIntercept(this.editThreadForm, this.editThread.bind(this));
        }
        if (this.ogimage) {
            this.ogimage.addEventListener('click', () => {
                this.hideBanner();
            });
        }
        if (this.closeThreadButton) {
            this.closeThreadButton.addEventListener('click', () => {
                this.closeThread();
            });
        }
        if (this.deleteThreadButton) {
            this.deleteThreadButton.addEventListener('click', () => {
                this.deleteThread();
            });
        }
        document.querySelectorAll('.replyto_button').forEach(item => {
            item.addEventListener('click', (event) => {
                this.replyTo(event.target);
            });
        });
    }
    replyTo(button) {
        let replyto = button.getAttribute('data-postid') ?? '';
        if (this.postForm && replyto) {
            this.postForm.replyTo(replyto);
        }
    }
    addPost() {
        if (this.addPostForm) {
            let button = this.addPostForm.querySelector('input[type=submit]');
            let formData = new FormData(this.addPostForm);
            formData.append('postForm[timezone]', Intl.DateTimeFormat().resolvedOptions().timeZone);
            buttonToggle(button);
            ajax(location.protocol + '//' + location.host + '/api/talks/posts/', formData, 'json', 'POST', 60000, true).then(data => {
                if (data.data === true) {
                    let textarea = this.addPostForm.querySelector('textarea');
                    if (textarea && textarea.id) {
                        saveTinyMCE(textarea.id);
                    }
                    new Snackbar('Post created. Reloading...', 'success');
                    window.location.href = data.location;
                }
                else {
                    new Snackbar(data.reason, 'failure', 10000);
                }
                buttonToggle(button);
            });
        }
    }
    deleteThread() {
        if (this.deleteThreadButton) {
            if (confirm('This is the last chance to back out.\nIf you press \'OK\' this thread will be permanently deleted.\nPress \'Cancel\' to cancel the action.')) {
                let id = this.deleteThreadButton.getAttribute('data-thread');
                if (id) {
                    buttonToggle(this.deleteThreadButton);
                    ajax(location.protocol + '//' + location.host + '/api/talks/threads/' + id + '/delete/', null, 'json', 'DELETE', 60000, true).then(data => {
                        if (data.data === true) {
                            new Snackbar('Thread removed. Redirecting to parent...', 'success');
                            window.location.href = data.location;
                        }
                        else {
                            new Snackbar(data.reason, 'failure', 10000);
                        }
                        buttonToggle(this.deleteThreadButton);
                    });
                }
            }
        }
    }
    closeThread() {
        if (this.closeThreadButton) {
            let id = this.closeThreadButton.getAttribute('data-thread');
            let verb = this.closeThreadButton.value.toLowerCase();
            if (id) {
                buttonToggle(this.closeThreadButton);
                ajax(location.protocol + '//' + location.host + '/api/talks/threads/' + id + '/' + verb + '/', null, 'json', 'PATCH', 60000, true).then(data => {
                    if (data.data === true) {
                        if (verb === 'close') {
                            new Snackbar('Thread closed. Refreshing...', 'success');
                        }
                        else {
                            new Snackbar('Thread reopened. Refreshing...', 'success');
                        }
                        window.location.href = window.location.href + '?forceReload=true';
                    }
                    else {
                        new Snackbar(data.reason, 'failure', 10000);
                    }
                    buttonToggle(this.closeThreadButton);
                });
            }
        }
    }
    editThread() {
        if (this.editThreadForm) {
            let button = this.editThreadForm.querySelector('input[type=submit]');
            let formData = new FormData(this.editThreadForm);
            let ogimage = this.editThreadForm.querySelector('input[type=file]');
            if (ogimage && ogimage.files && ogimage.files[0]) {
                formData.append('curThread[ogimage]', 'true');
            }
            else {
                formData.append('curThread[ogimage]', 'false');
            }
            buttonToggle(button);
            ajax(location.protocol + '//' + location.host + '/api/talks/threads/' + (formData.get('curThread[threadid]') ?? '0') + '/edit/', formData, 'json', 'POST', 60000, true).then(data => {
                if (data.data === true) {
                    new Snackbar('Thread updated. Reloading...', 'success');
                    window.location.href = window.location.href + '?forceReload=true';
                }
                else {
                    new Snackbar(data.reason, 'failure', 10000);
                    buttonToggle(button);
                }
            });
        }
    }
    hideBanner() {
        if (this.ogimage) {
            this.ogimage.classList.add('hidden');
        }
    }
}
//# sourceMappingURL=threads.js.map