class bicRefresh
{
    private refreshButton: HTMLInputElement;

    constructor()
    {
        this.refreshButton = document.getElementById('bicRefresh') as HTMLInputElement;
        this.refreshButton.addEventListener('click', (event: MouseEvent) => {this.refresh(event);});
    }

    //Refresh BIC library through API
    public refresh(event: Event): void
    {
        if (this.refreshButton.classList.contains('spin')) {
            //It already has been clicked, cancel event
            event.stopPropagation();
            event.preventDefault();
        } else {
            this.refreshButton.classList.add('spin');
            setTimeout(async () => {
                await ajax(location.protocol + '//' + location.host + '/api/bictracker/dbupdate/', null, 'json', 'PUT', 300000).then(data => {
                    if (data.data === true) {
                        new Snackbar('Библиотека БИК обновлена', 'success');
                        this.refreshButton.classList.remove('spin');
                    } else if (typeof data.data === 'number') {
                        //Create date from timestamp
                        let timestamp: Date = new Date(data.data * 1000);
                        //Get time block
                        let dateTime = document.getElementsByClassName('bic_date')[0] as HTMLTimeElement;
                        //Update its value
                        dateTime.setAttribute('datetime', timestamp.toISOString());
                        dateTime.innerHTML = ('0'+String(timestamp.getUTCDate())).slice(-2) + '.' + ('0'+String(timestamp.getMonth() + 1)).slice(-2) + '.' + String(timestamp.getUTCFullYear());
                        new Snackbar('Применено обновление за '+dateTime.innerHTML, 'success');
                        this.refreshButton.classList.remove('spin');
                        this.refresh(event);
                    } else {
                        new Snackbar('Не удалось обновить библиотеку БИК', 'failure', 10000);
                        this.refreshButton.classList.remove('spin');
                    }
                });
            }, 500);
        }
    }
}
