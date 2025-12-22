export class Threads {
    add_post_form = null;
    edit_thread_form = null;
    closeThreadButton = null;
    deleteThreadButton = null;
    post_form = null;
    constructor() {
        this.add_post_form = document.querySelector('#post_form');
        this.edit_thread_form = document.querySelector('#edit_thread_form');
        this.closeThreadButton = document.querySelector('#close_thread');
        this.deleteThreadButton = document.querySelector('#delete_thread');
        this.post_form = document.querySelector('post-form');
        if (this.add_post_form) {
            submitIntercept(this.add_post_form, this.addPost.bind(this));
        }
        if (this.edit_thread_form) {
            submitIntercept(this.edit_thread_form, this.editThread.bind(this));
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
        document.querySelectorAll('.reply_to_button')
            .forEach((item) => {
            item.addEventListener('click', (event) => {
                this.replyTo(event.target);
            });
        });
    }
    replyTo(button) {
        const reply_to = button.getAttribute('data-post_id') ?? '';
        if (this.post_form && reply_to) {
            this.post_form.replyTo(reply_to);
        }
    }
    addPost() {
        if (this.add_post_form) {
            const textarea = this.add_post_form.querySelector('textarea');
            if (textarea && !empty(textarea.id)) {
                saveTinyMCE(textarea.id, true);
            }
            const button = this.add_post_form.querySelector('input[type=submit]');
            const formData = new FormData(this.add_post_form);
            formData.append('post_form[timezone]', TIMEZONE);
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/posts`, formData, 'json', 'POST', AJAX_TIMEOUT, true)
                .then((response) => {
                const data = response;
                if (data.data === true) {
                    if (this.add_post_form) {
                        if (textarea && !empty(textarea.id)) {
                            saveTinyMCE(textarea.id);
                        }
                    }
                    addSnackbar('Post created. Reloading...', 'success');
                    pageRefresh(data.location);
                }
                else {
                    if (data.location) {
                        addSnackbar(data.reason + ` View the post <a href="${data.location}" target="_blank" rel="noopener noreferrer">here</a>.`, 'failure', 0);
                    }
                    else {
                        addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                    }
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
                    ajax(`${location.protocol}//${location.host}/api/talks/threads/${id}/delete`, null, 'json', 'DELETE', AJAX_TIMEOUT, true)
                        .then((response) => {
                        const data = response;
                        if (data.data === true) {
                            addSnackbar('Thread removed. Redirecting to parent...', 'success');
                            pageRefresh(data.location);
                        }
                        else {
                            addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
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
                ajax(`${location.protocol}//${location.host}/api/talks/threads/${id}/${verb}`, null, 'json', 'PATCH', AJAX_TIMEOUT, true)
                    .then((response) => {
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
                        addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                    }
                    if (this.closeThreadButton) {
                        buttonToggle(this.closeThreadButton);
                    }
                });
            }
        }
    }
    editThread() {
        if (this.edit_thread_form) {
            const button = this.edit_thread_form.querySelector('input[type=submit]');
            const formData = new FormData(this.edit_thread_form);
            const og_image = this.edit_thread_form.querySelector('input[type=file]');
            if (og_image?.files?.[0]) {
                formData.append('current_thread[og_image]', 'true');
            }
            else {
                formData.append('current_thread[og_image]', 'false');
            }
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/threads/${String(formData.get('current_thread[thread_id]') ?? '0')}/edit`, formData, 'json', 'POST', AJAX_TIMEOUT, true)
                .then((response) => {
                const data = response;
                if (data.data === true) {
                    addSnackbar('Thread updated. Reloading...', 'success');
                    pageRefresh();
                }
                else {
                    if (data.location) {
                        addSnackbar(data.reason + ` View the section <a href="${data.location}" target="_blank" rel="noopener noreferrer">here</a>.`, 'failure', 0);
                    }
                    else {
                        addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                    }
                    buttonToggle(button);
                }
            });
        }
    }
}
//# sourceMappingURL=threads.js.map