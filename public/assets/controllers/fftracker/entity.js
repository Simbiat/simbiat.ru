export class ffEntity {
    forceRefresh = null;
    constructor() {
        this.forceRefresh = document.querySelector('#ff_refresh');
        if (this.forceRefresh) {
            this.forceRefresh.addEventListener('click', this.refresh.bind(this));
        }
    }
    refresh(event) {
        if (this.forceRefresh) {
            if (this.forceRefresh.classList.contains('spin')) {
                event.stopPropagation();
                event.preventDefault();
            }
            else {
                this.forceRefresh.classList.add('spin');
                window.setTimeout(() => {
                    if (!this.forceRefresh) {
                        return;
                    }
                    void ajax(`${location.protocol}//${location.host}${this.forceRefresh.getAttribute('data-link') ?? ''}`, null, 'json', 'PUT', 300000).
                        then((response) => {
                        const data = response;
                        if (this.forceRefresh) {
                            this.forceRefresh.classList.remove('spin');
                        }
                        if (data.data === true) {
                            addSnackbar('Data updated. Reloading page...', 'success');
                            pageRefresh();
                        }
                        else {
                            addSnackbar('Failed to update data', 'failure', SNACKBAR_FAIL_LIFE);
                        }
                    });
                }, 500);
            }
        }
    }
}
//# sourceMappingURL=entity.js.map