export class Threads {
    add_post_form = null;
    edit_thread_form = null;
    private_thread_form = null;
    pin_thread_form = null;
    closed_thread_form = null;
    move_thread_form = null;
    delete_thread_form = null;
    post_form = null;
    constructor() {
        this.add_post_form = document.querySelector('#post_form');
        this.edit_thread_form = document.querySelector('#thread_form');
        this.private_thread_form = document.querySelector('#thread_private_form');
        this.pin_thread_form = document.querySelector('#thread_pin_form');
        this.closed_thread_form = document.querySelector('#thread_closed_form');
        this.move_thread_form = document.querySelector('#thread_move_form');
        this.delete_thread_form = document.querySelector('#thread_delete_form');
        this.post_form = document.querySelector('post-form');
        if (this.add_post_form) {
            submitIntercept(this.add_post_form, this.addPost.bind(this));
        }
        if (this.edit_thread_form) {
            submitIntercept(this.edit_thread_form, this.edit.bind(this));
        }
        if (this.private_thread_form) {
            submitIntercept(this.private_thread_form, this.makePrivate.bind(this));
        }
        if (this.pin_thread_form) {
            submitIntercept(this.pin_thread_form, this.pin.bind(this));
        }
        if (this.closed_thread_form) {
            submitIntercept(this.closed_thread_form, this.close.bind(this));
        }
        if (this.move_thread_form) {
            submitIntercept(this.move_thread_form, this.move.bind(this));
        }
        if (this.delete_thread_form) {
            submitIntercept(this.delete_thread_form, this.delete.bind(this));
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
            const form_data = new FormData(this.add_post_form);
            form_data.append('post_data[timezone]', TIMEZONE);
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/posts`, form_data, 'json', 'POST', AJAX_TIMEOUT, true)
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
    move() {
        if (this.move_thread_form) {
            const button = this.move_thread_form.querySelector('input[type=submit]');
            const form_data = new FormData(this.move_thread_form);
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/threads/${String(form_data.get('thread_data[thread_id]') ?? '0')}`, form_data, 'json', 'PATCH', AJAX_TIMEOUT, true)
                .then((response) => {
                const data = response;
                if (data.data === true) {
                    addSnackbar('Thread moved', 'success');
                    pageRefresh();
                }
                else {
                    addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                }
                buttonToggle(button);
            });
        }
    }
    delete() {
        if (this.delete_thread_form) {
            if (confirm('This is the last chance to back out.\nIf you press \'OK\' this thread will be permanently deleted.\nPress \'Cancel\' to cancel the action.')) {
                const button = this.delete_thread_form.querySelector('input[type=submit]');
                const form_data = new FormData(this.delete_thread_form);
                buttonToggle(button);
                ajax(`${location.protocol}//${location.host}/api/talks/threads/${String(form_data.get('thread_data[thread_id]') ?? '0')}`, form_data, 'json', 'DELETE', AJAX_TIMEOUT, true)
                    .then((response) => {
                    const data = response;
                    if (data.data === true) {
                        addSnackbar('Thread removed. Redirecting to parent...', 'success');
                        pageRefresh(data.location);
                    }
                    else {
                        addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                    }
                    buttonToggle(button);
                });
            }
        }
    }
    close() {
        if (this.closed_thread_form) {
            const button = this.closed_thread_form.querySelector('input[type=submit]');
            const form_data = new FormData(this.closed_thread_form);
            let verb = form_data.get('verb') ?? 'close';
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/threads/${String(form_data.get('thread_data[thread_id]') ?? '0')}`, form_data, 'json', 'PATCH', AJAX_TIMEOUT, true)
                .then((response) => {
                const data = response;
                if (data.data === true) {
                    if (verb === 'open') {
                        addSnackbar('Thread marked as open', 'success');
                    }
                    else {
                        addSnackbar('Thread marked as closed', 'success');
                    }
                    pageRefresh();
                }
                else {
                    addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                }
                buttonToggle(button);
            });
        }
    }
    edit() {
        if (this.edit_thread_form) {
            const button = this.edit_thread_form.querySelector('input[type=submit]');
            const form_data = new FormData(this.edit_thread_form);
            const og_image = this.edit_thread_form.querySelector('input[type=file]');
            if (og_image?.files?.[0]) {
                form_data.append('thread_data[og_image]', 'true');
            }
            else {
                form_data.append('thread_data[og_image]', 'false');
            }
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/threads/${String(form_data.get('thread_data[thread_id]') ?? '0')}`, form_data, 'json', 'PATCH', AJAX_TIMEOUT, true)
                .then((response) => {
                const data = response;
                if (data.data === true) {
                    addSnackbar('Thread updated. Reloading...', 'success');
                    pageRefresh();
                }
                else {
                    if (data.location) {
                        addSnackbar(data.reason + ` View the thread <a href="${data.location}" target="_blank" rel="noopener noreferrer">here</a>.`, 'failure', 0);
                    }
                    else {
                        addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                    }
                    buttonToggle(button);
                }
            });
        }
    }
    makePrivate() {
        if (this.private_thread_form) {
            const button = this.private_thread_form.querySelector('input[type=submit]');
            const form_data = new FormData(this.private_thread_form);
            let verb = form_data.get('verb') ?? 'mark_private';
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/threads/${String(form_data.get('thread_data[thread_id]') ?? '0')}`, form_data, 'json', 'PATCH', AJAX_TIMEOUT, true)
                .then((response) => {
                const data = response;
                if (data.data === true) {
                    if (verb === 'mark_public') {
                        addSnackbar('Thread marked as public', 'success');
                    }
                    else {
                        addSnackbar('Thread marked as private', 'success');
                    }
                    pageRefresh();
                }
                else {
                    addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                }
                buttonToggle(button);
            });
        }
    }
    pin() {
        if (this.pin_thread_form) {
            const button = this.pin_thread_form.querySelector('input[type=submit]');
            const form_data = new FormData(this.pin_thread_form);
            let verb = form_data.get('verb') ?? 'unpin';
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/threads/${String(form_data.get('thread_data[thread_id]') ?? '0')}`, form_data, 'json', 'PATCH', AJAX_TIMEOUT, true)
                .then((response) => {
                const data = response;
                if (data.data === true) {
                    if (verb === 'pin') {
                        addSnackbar('Thread pinned', 'success');
                    }
                    else {
                        addSnackbar('Thread unpinned', 'success');
                    }
                    pageRefresh();
                }
                else {
                    addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                }
                buttonToggle(button);
            });
        }
    }
}
//# sourceMappingURL=threads.js.map