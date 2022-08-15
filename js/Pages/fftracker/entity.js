export class ffEntity {
    forceRefresh;
    constructor() {
        this.forceRefresh = document.getElementById('ff_refresh');
        if (this.forceRefresh) {
            this.forceRefresh.addEventListener('click', this.refresh.bind(this));
        }
    }
    refresh(event) {
        if (this.forceRefresh.classList.contains('spin')) {
            event.stopPropagation();
            event.preventDefault();
        }
        else {
            this.forceRefresh.classList.add('spin');
            setTimeout(async () => {
                await ajax(location.protocol + '//' + location.host + this.forceRefresh.getAttribute('data-link'), null, 'json', 'PUT', 300000).then(data => {
                    if (data.data === true) {
                        new Snackbar('Data updated. Reloading page...', 'success');
                        this.forceRefresh.classList.remove('spin');
                        location.reload();
                    }
                    else {
                        new Snackbar('Failed to update data', 'failure', 10000);
                        this.forceRefresh.classList.remove('spin');
                    }
                });
            }, 500);
        }
    }
}
//# sourceMappingURL=entity.js.map