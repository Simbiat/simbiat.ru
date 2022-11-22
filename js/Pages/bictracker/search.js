export class bicRefresh {
    refreshButton;
    constructor() {
        this.refreshButton = document.getElementById('bicRefresh');
        this.refreshButton.addEventListener('click', (event) => { this.refresh(event); });
    }
    refresh(event) {
        if (this.refreshButton.classList.contains('spin')) {
            event.stopPropagation();
            event.preventDefault();
        }
        else {
            this.refreshButton.classList.add('spin');
            setTimeout(async () => {
                await ajax(location.protocol + '//' + location.host + '/api/bictracker/dbupdate/', null, 'json', 'PUT', 300000).then(data => {
                    if (data.data === true) {
                        new Snackbar('Библиотека БИК обновлена', 'success');
                        this.refreshButton.classList.remove('spin');
                    }
                    else if (typeof data.data === 'number') {
                        let timestamp = new Date(data.data * 1000);
                        let dateTime = document.querySelector('.bic_date');
                        dateTime.setAttribute('datetime', timestamp.toISOString());
                        dateTime.innerHTML = ('0' + String(timestamp.getUTCDate())).slice(-2) + '.' + ('0' + String(timestamp.getMonth() + 1)).slice(-2) + '.' + String(timestamp.getUTCFullYear());
                        new Snackbar('Применено обновление за ' + dateTime.innerHTML, 'success');
                        this.refreshButton.classList.remove('spin');
                        this.refresh(event);
                    }
                    else {
                        new Snackbar('Не удалось обновить библиотеку БИК', 'failure', 10000);
                        this.refreshButton.classList.remove('spin');
                    }
                });
            }, 500);
        }
    }
}
//# sourceMappingURL=search.js.map