export class ffEntity
{
    private readonly forceRefresh: HTMLInputElement | null = null;
    
    public constructor()
    {
        //Get the refresh button, if it exists
        this.forceRefresh = document.querySelector('#ff_refresh');
        if (this.forceRefresh) {
            //Attach on-click event
            this.forceRefresh.addEventListener('click', this.refresh.bind(this));
        }
    }
    
    private refresh(event: Event): void
    {
        if (this.forceRefresh) {
            if (this.forceRefresh.classList.contains('spin')) {
                //It already has been clicked, cancel event
                event.stopPropagation();
                event.preventDefault();
            } else {
                this.forceRefresh.classList.add('spin');
                window.setTimeout(() => {
                    if (!this.forceRefresh) {
                        return;
                    }
                    void ajax(`${location.protocol}//${location.host}${this.forceRefresh.getAttribute('data-link') ?? ''}`, null, 'json', 'PUT', 300000).
                        then((response) => {
                            const data = response as ajaxJSONResponse;
                            if (this.forceRefresh) {
                                this.forceRefresh.classList.remove('spin');
                            }
                            if (data.data === true) {
                                addSnackbar('Data updated. Reloading page...', 'success');
                                pageRefresh();
                            } else {
                                addSnackbar('Failed to update data', 'failure', 10000);
                            }
                        });
                }, 500);
            }
        }
    }
}
