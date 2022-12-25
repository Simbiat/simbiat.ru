export class Threads {
    editThreadForm = null;
    closeThreadButton = null;
    deleteThreadButton = null;
    ogimage = null;
    constructor() {
        this.editThreadForm = document.getElementById('editThreadForm');
        this.closeThreadButton = document.getElementById('close_thread');
        this.deleteThreadButton = document.getElementById('delete_thread');
        this.ogimage = document.getElementById('thread_ogimage');
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
    }
    deleteThread() {
        if (this.deleteThreadButton) {
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
                        window.location.reload();
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
                    location.reload();
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