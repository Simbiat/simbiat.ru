export class Threads
{
    private readonly add_post_form: HTMLFormElement | null = null;
    private readonly edit_thread_form: HTMLFormElement | null = null;
    private readonly closeThreadButton: HTMLInputElement | null = null;
    private readonly deleteThreadButton: HTMLInputElement | null = null;
    private readonly post_form: PostForm | null = null;

    public constructor()
    {
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
        //Listener for closure
        if (this.closeThreadButton) {
            this.closeThreadButton.addEventListener('click', () => {
                this.closeThread();
            });
        }
        //Listener for deletion
        if (this.deleteThreadButton) {
            this.deleteThreadButton.addEventListener('click', () => {
                this.deleteThread();
            });
        }
        //Listener for `reply to` buttons
        document.querySelectorAll('.reply_to_button')
                .forEach((item) => {
                    //Tracking click to be able to roll back change easily
                    (item as HTMLElement).addEventListener('click', (event: MouseEvent) => {
                        this.replyTo(event.target as HTMLInputElement);
                    });
                });
    }

    private replyTo(button: HTMLInputElement): void
    {
        //Get the post's ID
        const reply_to = button.getAttribute('data-post_id') ?? '';
        if (this.post_form && reply_to) {
            this.post_form.replyTo(reply_to);
        }
    }

    private addPost(): void
    {
        if (this.add_post_form) {
            const textarea = this.add_post_form.querySelector('textarea');
            //Ensure we have the latest version of the text from TinyMCE instance
            if (textarea && !empty(textarea.id)) {
                saveTinyMCE(textarea.id, true);
            }
            //Get submit button
            const button = this.add_post_form.querySelector('input[type=submit]');
            //Get form data
            const formData = new FormData(this.add_post_form);
            //Add time zone
            formData.append('post_form[timezone]', TIMEZONE);
            buttonToggle(button as HTMLInputElement);
            ajax(`${location.protocol}//${location.host}/api/talks/posts`, formData, 'json', 'POST', AJAX_TIMEOUT, true)
                .then((response) => {
                    const data = response as ajaxJSONResponse;
                    if (data.data === true) {
                        if (this.add_post_form) {
                            //Notify TinyMCE, that data was saved
                            if (textarea && !empty(textarea.id)) {
                                saveTinyMCE(textarea.id);
                            }
                        }
                        addSnackbar('Post created. Reloading...', 'success');
                        window.location.assign(encodeURI(data.location));
                    } else {
                        if (data.location) {
                            addSnackbar(data.reason + ` View the post <a href="${data.location}" target="_blank">here</a>.`, 'failure', 0);
                        } else {
                            addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                        }
                    }
                    buttonToggle(button as HTMLInputElement);
                });
        }
    }

    private deleteThread(): void
    {
        if (this.deleteThreadButton) {
            if (confirm('This is the last chance to back out.\nIf you press \'OK\' this thread will be permanently deleted.\nPress \'Cancel\' to cancel the action.')) {
                const id = this.deleteThreadButton.getAttribute('data-thread') ?? '';
                if (!empty(id)) {
                    buttonToggle(this.deleteThreadButton);
                    ajax(`${location.protocol}//${location.host}/api/talks/threads/${id}/delete`, null, 'json', 'DELETE', AJAX_TIMEOUT, true)
                        .then((response) => {
                            const data = response as ajaxJSONResponse;
                            if (data.data === true) {
                                addSnackbar('Thread removed. Redirecting to parent...', 'success');
                                window.location.assign(encodeURI(data.location));
                            } else {
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

    private closeThread(): void
    {
        if (this.closeThreadButton) {
            const id = this.closeThreadButton.getAttribute('data-thread') ?? '';
            const verb = this.closeThreadButton.value.toLowerCase();
            if (!empty(id)) {
                buttonToggle(this.closeThreadButton);
                ajax(`${location.protocol}//${location.host}/api/talks/threads/${id}/${verb}`, null, 'json', 'PATCH', AJAX_TIMEOUT, true)
                    .then((response) => {
                        const data = response as ajaxJSONResponse;
                        if (data.data === true) {
                            if (verb === 'close') {
                                addSnackbar('Thread closed. Refreshing...', 'success');
                            } else {
                                addSnackbar('Thread reopened. Refreshing...', 'success');
                            }
                            pageRefresh();
                        } else {
                            addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                        }
                        if (this.closeThreadButton) {
                            buttonToggle(this.closeThreadButton);
                        }
                    });
            }
        }
    }

    private editThread(): void
    {
        if (this.edit_thread_form) {
            //Get submit button
            const button = this.edit_thread_form.querySelector('input[type=submit]');
            //Get form data
            const formData = new FormData(this.edit_thread_form);
            //Check if custom icon is being attached
            const og_image: HTMLInputElement | null = this.edit_thread_form.querySelector('input[type=file]');
            if (og_image?.files?.[0]) {
                formData.append('current_thread[og_image]', 'true');
            } else {
                formData.append('current_thread[og_image]', 'false');
            }
            buttonToggle(button as HTMLInputElement);
            ajax(`${location.protocol}//${location.host}/api/talks/threads/${String(formData.get('current_thread[thread_id]') ?? '0')}/edit`, formData, 'json', 'POST', AJAX_TIMEOUT, true)
                .then((response) => {
                    const data = response as ajaxJSONResponse;
                    if (data.data === true) {
                        addSnackbar('Thread updated. Reloading...', 'success');
                        pageRefresh();
                    } else {
                        if (data.location) {
                            addSnackbar(data.reason + ` View the section <a href="${data.location}" target="_blank">here</a>.`, 'failure', 0);
                        } else {
                            addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                        }
                        buttonToggle(button as HTMLInputElement);
                    }
                });
        }
    }
}
