export class Threads
{
    private readonly editThreadForm: HTMLFormElement | null = null;
    private readonly closeThreadButton: HTMLInputElement | null = null;
    private readonly deleteThreadButton: HTMLInputElement | null = null;
    private readonly ogimage: HTMLImageElement | null = null;
    
    constructor()
    {
        this.editThreadForm = document.getElementById('editThreadForm') as HTMLFormElement;
        this.closeThreadButton = document.getElementById('close_thread') as HTMLInputElement;
        this.deleteThreadButton = document.getElementById('delete_thread') as HTMLInputElement;
        this.ogimage = document.getElementById('thread_ogimage') as HTMLImageElement;
        if (this.editThreadForm) {
            submitIntercept(this.editThreadForm, this.editThread.bind(this));
        }
        //Listener to hide ogimage
        if (this.ogimage) {
            this.ogimage.addEventListener('click', () => {
                this.hideBanner();
            });
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
    }
    
    private deleteThread()
    {
        if (this.deleteThreadButton) {
            let id = this.deleteThreadButton.getAttribute('data-thread');
            if (id) {
                buttonToggle(this.deleteThreadButton as HTMLInputElement);
                ajax(location.protocol + '//' + location.host + '/api/talks/threads/'+id+'/delete/', null, 'json', 'DELETE', 60000, true).then(data => {
                    if (data.data === true) {
                        new Snackbar('Thread removed. Redirecting to parent...', 'success');
                        window.location.href = data.location;
                    } else {
                        new Snackbar(data.reason, 'failure', 10000);
                    }
                    buttonToggle(this.deleteThreadButton as HTMLInputElement);
                });
            }
        }
    }
    
    private closeThread()
    {
        if (this.closeThreadButton) {
            let id = this.closeThreadButton.getAttribute('data-thread');
            let verb = this.closeThreadButton.value.toLowerCase();
            if (id) {
                buttonToggle(this.closeThreadButton as HTMLInputElement);
                ajax(location.protocol + '//' + location.host + '/api/talks/threads/'+id+'/'+verb+'/', null, 'json', 'PATCH', 60000, true).then(data => {
                    if (data.data === true) {
                        if (verb === 'close') {
                            new Snackbar('Thread closed. Refreshing...', 'success');
                        } else {
                            new Snackbar('Thread reopened. Refreshing...', 'success');
                        }
                        window.location.reload();
                    } else {
                        new Snackbar(data.reason, 'failure', 10000);
                    }
                    buttonToggle(this.closeThreadButton as HTMLInputElement);
                });
            }
        }
    }
    
    private editThread()
    {
        if (this.editThreadForm) {
            //Get submit button
            let button = this.editThreadForm.querySelector('input[type=submit]')
            //Get form data
            let formData = new FormData(this.editThreadForm);
            //Check if custom icon is being attached
            let ogimage = this.editThreadForm.querySelector('input[type=file]') as HTMLInputElement;
            if (ogimage && ogimage.files && ogimage.files[0]) {
                formData.append('curThread[ogimage]', 'true');
            } else {
                formData.append('curThread[ogimage]', 'false');
            }
            buttonToggle(button as HTMLInputElement);
            ajax(location.protocol + '//' + location.host + '/api/talks/threads/'+(formData.get('curThread[threadid]') ?? '0')+'/edit/', formData, 'json', 'POST', 60000, true).then(data => {
                if (data.data === true) {
                    new Snackbar('Thread updated. Reloading...', 'success');
                    location.reload();
                } else {
                    new Snackbar(data.reason, 'failure', 10000);
                    buttonToggle(button as HTMLInputElement);
                }
            });
        }
    }
    
    private hideBanner()
    {
        if (this.ogimage) {
            this.ogimage.classList.add('hidden');
        }
    }
}
