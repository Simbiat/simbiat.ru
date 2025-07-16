export class bicKeying
{
    private readonly form: HTMLFormElement | null = null;
    private readonly result: HTMLSpanElement | null = null;
    private readonly bicKeySample: HTMLSpanElement | null = null;
    private readonly accKeySample: HTMLSpanElement | null = null;
    private readonly spinner: HTMLImageElement | null = null;
    
    public constructor()
    {
        this.form = document.querySelector('#bic_keying');
        this.result = document.querySelector('#acc_check_result');
        this.bicKeySample = document.querySelector('#bic_key_sample');
        this.accKeySample = document.querySelector('#account_key_sample');
        this.spinner = document.querySelector('#bic_spinner');
        ['change', 'input', 'paste'].forEach((eventType: string) => {
            document.querySelector('#bic_key')
                    ?.addEventListener(eventType, () => {
                        this.calc();
                    });
            document.querySelector('#account_key')
                    ?.addEventListener(eventType, () => {
                        this.calc();
                    });
        });
    }
    
    private calc(): boolean
    {
        if (this.form && this.result && this.bicKeySample && this.accKeySample) {
            //Get form data
            const formData = new FormData(this.form);
            const bicKey = String(formData.get('bic_key') ?? '');
            const accKey = String(formData.get('account_key') ?? '');
            this.result.classList.remove(...this.result.classList);
            if ((/^\d{9}$/u).exec(bicKey) === null) {
                this.result.classList.add('failure');
                this.result.innerHTML = 'Неверный формат БИКа';
                bicKeying.styleBic(this.bicKeySample, 'warning', 'БИК');
                return false;
            }
            bicKeying.styleBic(this.bicKeySample, 'success', bicKey);
            if ((/^\d{5}[\dАВСЕНКМРТХавсенкмртх]\d{14}$/u).exec(accKey) === null) {
                this.result.classList.add('failure');
                this.result.innerHTML = 'Неверный формат счёта';
                bicKeying.styleBic(this.accKeySample, 'warning', 'СЧЁТ');
                return false;
            }
            bicKeying.styleBic(this.accKeySample, 'success', accKey);
            //Initiate request
            this.result.classList.add('warning');
            this.result.innerHTML = 'Проверяем...';
            if (this.spinner) {
                this.spinner.classList.remove('hidden');
            }
            void ajax(`${location.protocol}//${location.host}/api/bictracker/keying`, formData, 'json', 'POST', AJAX_TIMEOUT, true)
                .then((response) => {
                    const data = response as ajaxJSONResponse;
                    //Change address
                    updateHistory(`${location.protocol}//${location.host}/bictracker/keying/${bicKey}/${accKey}/`, `Ключевание счёта ${accKey}`);
                    if (this.result) {
                        this.result.classList.remove(...this.result.classList);
                        if (data.data === true) {
                            this.result.classList.add('success');
                            this.result.innerHTML = 'Правильное ключевание';
                        } else {
                            this.result.classList.add('failure');
                            if (data.data === false) {
                                this.result.innerHTML = 'Непредвиденная ошибка';
                            } else {
                                this.result.innerHTML = `Неверное ключевание. Ожидаемый ключ: ${data.data} (${accKey.replace(/(?<beforeKey>^\d{5}[\dАВСЕНКМРТХавсенкмртх]\d{2})(?<key>\d)(?<afterKey>\d{11})$/u, `$<beforeKey><span class="success">${data.data}</span>$<afterKey>`)})`;
                            }
                        }
                    }
                    if (this.spinner) {
                        this.spinner.classList.add('hidden');
                    }
                    return true;
                });
        }
        return false;
    }

    //Helper function for styling
    private static styleBic(element: HTMLSpanElement, newClass: string, text = ''): void
    {
        element.classList.remove(...element.classList);
        element.classList.add(newClass);
        element.innerHTML = text;
    }
}
