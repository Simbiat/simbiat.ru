export class ffEntity
{
    private readonly forceRefresh: HTMLInputElement;
    
    constructor()
    {
        //Get the refresh button, if it exists
        this.forceRefresh = document.getElementById('ff_refresh') as HTMLInputElement;
        if (this.forceRefresh) {
            //Attach on-click event
            this.forceRefresh.addEventListener('click', this.refresh.bind(this));
        }
    }
    
    public refresh(event: Event): void
    {
        if (this.forceRefresh.classList.contains('spin')) {
            //It already has been clicked, cancel event
            event.stopPropagation();
            event.preventDefault();
        } else {
            this.forceRefresh.classList.add('spin');
            setTimeout(async () => {
                await ajax(location.protocol + '//' + location.host + this.forceRefresh.getAttribute('data-link'), null, 'json', 'PUT', 300000).then(data => {
                    if (data.data === true) {
                        new Snackbar('Data updated. Reloading page...', 'success');
                        this.forceRefresh.classList.remove('spin');
                        location.reload();
                    } else {
                        new Snackbar('Failed to update data', 'failure', 10000);
                        this.forceRefresh.classList.remove('spin');
                    }
                });
            }, 500);
        }
    }
}
