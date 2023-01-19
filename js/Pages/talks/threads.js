export class Threads {
    addPostForm = null;
    editThreadForm = null;
    closeThreadButton = null;
    deleteThreadButton = null;
    postForm = null;
    ogimage = null;
    constructor() {
        this.addPostForm = document.querySelector('#postForm');
        this.editThreadForm = document.querySelector('#editThreadForm');
        this.closeThreadButton = document.querySelector('#close_thread');
        this.deleteThreadButton = document.querySelector('#delete_thread');
        this.ogimage = document.querySelector('#thread_ogimage');
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
        document.querySelectorAll('.replyto_button').forEach((item) => {
            item.addEventListener('click', (event) => {
                this.replyTo(event.target);
            });
        });
    }
    replyTo(button) {
        const replyto = button.getAttribute('data-postid') ?? '';
        if (this.postForm && replyto) {
            this.postForm.replyTo(replyto);
        }
    }
    addPost() {
        if (this.addPostForm) {
            const button = this.addPostForm.querySelector('input[type=submit]');
            const formData = new FormData(this.addPostForm);
            formData.append('postForm[timezone]', Intl.DateTimeFormat().resolvedOptions().timeZone);
            buttonToggle(button);
            void ajax(`${location.protocol}//${location.host}/api/talks/posts/`, formData, 'json', 'POST', 60000, true).then((response) => {
                const data = response;
                if (data.data === true) {
                    if (this.addPostForm) {
                        const textarea = this.addPostForm.querySelector('textarea');
                        if (textarea && !empty(textarea.id)) {
                            saveTinyMCE(textarea.id);
                        }
                    }
                    addSnackbar('Post created. Reloading...', 'success');
                    window.location.href = data.location;
                }
                else {
                    addSnackbar(data.reason, 'failure', 10000);
                }
                buttonToggle(button);
            });
        }
    }
    deleteThread() {
        if (this.deleteThreadButton) {
            if (confirm('This is the last chance to back out.\nIf you press \'OK\' this thread will be permanently deleted.\nPress \'Cancel\' to cancel the action.')) {
                const id = this.deleteThreadButton.getAttribute('data-thread') ?? '';
                if (!empty(id)) {
                    buttonToggle(this.deleteThreadButton);
                    void ajax(`${location.protocol}//${location.host}/api/talks/threads/${id}/delete/`, null, 'json', 'DELETE', 60000, true).then((response) => {
                        const data = response;
                        if (data.data === true) {
                            addSnackbar('Thread removed. Redirecting to parent...', 'success');
                            window.location.href = data.location;
                        }
                        else {
                            addSnackbar(data.reason, 'failure', 10000);
                        }
                        if (this.deleteThreadButton) {
                            buttonToggle(this.deleteThreadButton);
                        }
                    });
                }
            }
        }
    }
    closeThread() {
        if (this.closeThreadButton) {
            const id = this.closeThreadButton.getAttribute('data-thread') ?? '';
            const verb = this.closeThreadButton.value.toLowerCase();
            if (!empty(id)) {
                buttonToggle(this.closeThreadButton);
                void ajax(`${location.protocol}//${location.host}/api/talks/threads/${id}/${verb}/`, null, 'json', 'PATCH', 60000, true).then((response) => {
                    const data = response;
                    if (data.data === true) {
                        if (verb === 'close') {
                            addSnackbar('Thread closed. Refreshing...', 'success');
                        }
                        else {
                            addSnackbar('Thread reopened. Refreshing...', 'success');
                        }
                        pageRefresh();
                    }
                    else {
                        addSnackbar(data.reason, 'failure', 10000);
                    }
                    if (this.closeThreadButton) {
                        buttonToggle(this.closeThreadButton);
                    }
                });
            }
        }
    }
    editThread() {
        if (this.editThreadForm) {
            const button = this.editThreadForm.querySelector('input[type=submit]');
            const formData = new FormData(this.editThreadForm);
            const ogimage = this.editThreadForm.querySelector('input[type=file]');
            if (ogimage?.files?.[0]) {
                formData.append('curThread[ogimage]', 'true');
            }
            else {
                formData.append('curThread[ogimage]', 'false');
            }
            buttonToggle(button);
            void ajax(`${location.protocol}//${location.host}/api/talks/threads/${String(formData.get('curThread[threadid]') ?? '0')}/edit/`, formData, 'json', 'POST', 60000, true).then((response) => {
                const data = response;
                if (data.data === true) {
                    addSnackbar('Thread updated. Reloading...', 'success');
                    pageRefresh();
                }
                else {
                    addSnackbar(data.reason, 'failure', 10000);
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