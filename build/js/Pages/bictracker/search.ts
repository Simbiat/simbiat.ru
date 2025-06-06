export class bicRefresh
{
    private readonly refreshButton: HTMLInputElement | null = null;

    public constructor()
    {
        this.refreshButton = document.querySelector('#bicRefresh');
        if (this.refreshButton) {
            this.refreshButton.addEventListener('click', (event: MouseEvent) => {
                this.refresh(event);
            });
        }
    }

    //Refresh BIC library through API
    private refresh(event: Event): void
    {
        if (!this.refreshButton) {
            return;
        }
        if (this.refreshButton.classList.contains('spin')) {
            //It already has been clicked, cancel event
            event.stopPropagation();
            event.preventDefault();
        } else {
            this.refreshButton.classList.add('spin');
            window.setTimeout(() => {
                void ajax(`${location.protocol}//${location.host}/api/bictracker/dbupdate`, null, 'json', 'PUT', 300000).then((response) => {
                    const data = response as ajaxJSONResponse;
                    if (this.refreshButton) {
                        if (data.data === true) {
                            addSnackbar('Библиотека БИК обновлена', 'success');
                            this.refreshButton.classList.remove('spin');
                        } else if (typeof data.data === 'number') {
                            //Create date from timestamp
                            const timestamp: Date = new Date(data.data * 1000);
                            //Get time block
                            const dateTime = document.querySelector('.bic_date');
                            if (dateTime) {
                                //Update its value
                                dateTime.setAttribute('datetime', timestamp.toISOString());
                                dateTime.innerHTML = `${`0${String(timestamp.getUTCDate())}`.slice(-2)}.${`0${String(timestamp.getMonth() + 1)}`.slice(-2)}.${String(timestamp.getUTCFullYear())}`;
                                addSnackbar(`Применено обновление за ${dateTime.innerHTML}`, 'success');
                                this.refreshButton.classList.remove('spin');
                                this.refresh(event);
                            }
                        } else {
                            addSnackbar('Не удалось обновить библиотеку БИК', 'failure', snackbarFailLife);
                            this.refreshButton.classList.remove('spin');
                        }
                    }
                });
            }, 500);
        }
    }
}
