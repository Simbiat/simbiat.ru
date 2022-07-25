export class bicKeying {
    constructor() {
        document.getElementById('bic_key').addEventListener('input', () => { this.calc(); });
        document.getElementById('account_key').addEventListener('input', () => { this.calc(); });
    }
    calc() {
        let form = document.getElementById('bic_keying');
        let formData = new FormData(form);
        let result = document.getElementById('accCheckResult');
        let bicKey = String(formData.get('bic_key'));
        let accKey = String(formData.get('account_key'));
        let bicKeySample = document.getElementById('bic_key_sample');
        let accKeySample = document.getElementById('account_key_sample');
        result.classList.remove(...result.classList);
        if (/^\d{9}$/u.exec(bicKey) === null) {
            result.classList.add('failure');
            result.innerHTML = 'Неверный формат БИКа';
            this.styleBic(bicKeySample, 'warning', 'БИК');
            return;
        }
        else {
            this.styleBic(bicKeySample, 'success', bicKey);
        }
        if (/^\d{5}[\dАВСЕНКМРТХавсенкмртх]\d{14}$/u.exec(accKey) === null) {
            result.classList.add('failure');
            result.innerHTML = 'Неверный формат счёта';
            this.styleBic(accKeySample, 'warning', 'СЧЁТ');
            return;
        }
        else {
            this.styleBic(accKeySample, 'success', accKey);
        }
        updateHistory(location.protocol + '//' + location.host + '/bictracker/keying/' + bicKey + '/' + accKey + '/', 'Ключевание счёта ' + accKey + pageTitle);
        result.classList.add('warning');
        result.innerHTML = 'Проверяем...';
        let spinner = document.getElementById('bic_spinner');
        spinner.classList.remove('hidden');
        ajax(location.protocol + '//' + location.host + '/api/bictracker/keying/', formData, 'json', 'POST', 60000, true).then(data => {
            result.classList.remove(...result.classList);
            if (data.data === true) {
                result.classList.add('success');
                result.innerHTML = 'Правильное ключевание';
            }
            else {
                result.classList.add('failure');
                if (data.data === false) {
                    result.innerHTML = 'Непредвиденная ошибка';
                }
                else {
                    result.innerHTML = 'Неверное ключевание. Ожидаемый ключ: ' + data.data + ' (' + accKey.replace(/(^\d{5}[\dАВСЕНКМРТХавсенкмртх]\d{2})(\d)(\d{11})$/u, '$1<span class="success">' + data.data + '</span>$3') + ')';
                }
            }
            spinner.classList.add('hidden');
        });
        return;
    }
    styleBic(element, newClass, text = '') {
        element.classList.remove(...element.classList);
        element.classList.add(newClass);
        element.innerHTML = text;
    }
}
//# sourceMappingURL=keying.js.map