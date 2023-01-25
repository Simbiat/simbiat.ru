export class Threads
{
    private readonly addPostForm: HTMLFormElement | null = null;
    private readonly editThreadForm: HTMLFormElement | null = null;
    private readonly closeThreadButton: HTMLInputElement | null = null;
    private readonly deleteThreadButton: HTMLInputElement | null = null;
    private readonly postForm: PostForm | null = null;
    
    public constructor()
    {
        this.addPostForm = document.querySelector('#postForm');
        this.editThreadForm = document.querySelector('#editThreadForm');
        this.closeThreadButton = document.querySelector('#close_thread');
        this.deleteThreadButton = document.querySelector('#delete_thread');
        this.postForm = document.querySelector('post-form');
        if (this.addPostForm) {
            submitIntercept(this.addPostForm, this.addPost.bind(this));
        }
        if (this.editThreadForm) {
            submitIntercept(this.editThreadForm, this.editThread.bind(this));
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
        document.querySelectorAll('.replyto_button').forEach((item) => {
            //Tracking click to be able to roll back change easily
            item.addEventListener('click', (event: Event) => {
                this.replyTo(event.target as HTMLInputElement);
            });
        });
    }
    
    private replyTo(button: HTMLInputElement): void
    {
        //Get the post ID
        const replyto = button.getAttribute('data-postid') ?? '';
        if (this.postForm && replyto) {
            this.postForm.replyTo(replyto);
        }
    }
    
    private addPost(): void
    {
        if (this.addPostForm) {
            const textarea = this.addPostForm.querySelector('textarea');
            //Ensure we have the latest version of the text from TinyMCE instance
            if (textarea && !empty(textarea.id)) {
                saveTinyMCE(textarea.id, true);
            }
            //Get submit button
            const button = this.addPostForm.querySelector('input[type=submit]');
            //Get form data
            const formData = new FormData(this.addPostForm);
            //Add timezone
            formData.append('postForm[timezone]', Intl.DateTimeFormat().resolvedOptions().timeZone);
            buttonToggle(button as HTMLInputElement);
            void ajax(`${location.protocol}//${location.host}/api/talks/posts/`, formData, 'json', 'POST', 60000, true).then((response) => {
                const data = response as ajaxJSONResponse;
                if (data.data === true) {
                    if (this.addPostForm) {
                        //Notify TinyMCE, that data was saved
                        if (textarea && !empty(textarea.id)) {
                            saveTinyMCE(textarea.id);
                        }
                    }
                    addSnackbar('Post created. Reloading...', 'success');
                    window.location.href = data.location;
                } else {
                    addSnackbar(data.reason, 'failure', 10000);
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
                    void ajax(`${location.protocol}//${location.host}/api/talks/threads/${id}/delete/`, null, 'json', 'DELETE', 60000, true).then((response) => {
                        const data = response as ajaxJSONResponse;
                        if (data.data === true) {
                            addSnackbar('Thread removed. Redirecting to parent...', 'success');
                            window.location.href = data.location;
                        } else {
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
    
    private closeThread(): void
    {
        if (this.closeThreadButton) {
            const id = this.closeThreadButton.getAttribute('data-thread') ?? '';
            const verb = this.closeThreadButton.value.toLowerCase();
            if (!empty(id)) {
                buttonToggle(this.closeThreadButton);
                void ajax(`${location.protocol}//${location.host}/api/talks/threads/${id}/${verb}/`, null, 'json', 'PATCH', 60000, true).then((response) => {
                    const data = response as ajaxJSONResponse;
                    if (data.data === true) {
                        if (verb === 'close') {
                            addSnackbar('Thread closed. Refreshing...', 'success');
                        } else {
                            addSnackbar('Thread reopened. Refreshing...', 'success');
                        }
                        pageRefresh();
                    } else {
                        addSnackbar(data.reason, 'failure', 10000);
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
        if (this.editThreadForm) {
            //Get submit button
            const button = this.editThreadForm.querySelector('input[type=submit]');
            //Get form data
            const formData = new FormData(this.editThreadForm);
            //Check if custom icon is being attached
            const ogimage: HTMLInputElement | null = this.editThreadForm.querySelector('input[type=file]');
            if (ogimage?.files?.[0]) {
                formData.append('curThread[ogimage]', 'true');
            } else {
                formData.append('curThread[ogimage]', 'false');
            }
            buttonToggle(button as HTMLInputElement);
            void ajax(`${location.protocol}//${location.host}/api/talks/threads/${String(formData.get('curThread[threadid]') ?? '0')}/edit/`, formData, 'json', 'POST', 60000, true).then((response) => {
                const data = response as ajaxJSONResponse;
                if (data.data === true) {
                    addSnackbar('Thread updated. Reloading...', 'success');
                    pageRefresh();
                } else {
                    addSnackbar(data.reason, 'failure', 10000);
                    buttonToggle(button as HTMLInputElement);
                }
            });
        }
    }
}
