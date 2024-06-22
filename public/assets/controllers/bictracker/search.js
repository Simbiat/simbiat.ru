export class bicRefresh {
    refreshButton = null;
    constructor() {
        this.refreshButton = document.querySelector('#bicRefresh');
        if (this.refreshButton) {
            this.refreshButton.addEventListener('click', (event) => {
                this.refresh(event);
            });
        }
    }
    refresh(event) {
        if (!this.refreshButton) {
            return;
        }
        if (this.refreshButton.classList.contains('spin')) {
            event.stopPropagation();
            event.preventDefault();
        }
        else {
            this.refreshButton.classList.add('spin');
            window.setTimeout(() => {
                void ajax(`${location.protocol}//${location.host}/api/bictracker/dbupdate`, null, 'json', 'PUT', 300000).then((response) => {
                    const data = response;
                    if (this.refreshButton) {
                        if (data.data === true) {
                            addSnackbar('Библиотека БИК обновлена', 'success');
                            this.refreshButton.classList.remove('spin');
                        }
                        else if (typeof data.data === 'number') {
                            const timestamp = new Date(data.data * 1000);
                            const dateTime = document.querySelector('.bic_date');
                            if (dateTime) {
                                dateTime.setAttribute('datetime', timestamp.toISOString());
                                dateTime.innerHTML = `${`0${String(timestamp.getUTCDate())}`.slice(-2)}.${`0${String(timestamp.getMonth() + 1)}`.slice(-2)}.${String(timestamp.getUTCFullYear())}`;
                                addSnackbar(`Применено обновление за ${dateTime.innerHTML}`, 'success');
                                this.refreshButton.classList.remove('spin');
                                this.refresh(event);
                            }
                        }
                        else {
                            addSnackbar('Не удалось обновить библиотеку БИК', 'failure', 10000);
                            this.refreshButton.classList.remove('spin');
                        }
                    }
                });
            }, 500);
        }
    }
}
//# sourceMappingURL=search.js.map