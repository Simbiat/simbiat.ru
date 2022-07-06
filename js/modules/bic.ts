/*globals ajax, pageTitle, updateHistory, addSnackbar*/
/*exported bicInit*/

function bicInit(): void
{
    let bicKey = document.getElementById('bic_key');
    let accKey = document.getElementById('account_key');
    if (bicKey && accKey) {
        bicKey.addEventListener('input', bicCalc);
        accKey.addEventListener('input', bicCalc);
    }
    let refresh = document.getElementById('bicRefresh');
    if (refresh) {
        refresh.addEventListener('click', bicRefresh);
    }
}

function bicCalc(): void | boolean
{
    let form = document.getElementById('bic_keying') as HTMLFormElement;
    //Get form data
    let formData = new FormData(form);
    let result = document.getElementById('accCheckResult') as HTMLSpanElement;
    let bicKey = String(formData.get('bic_key'));
    let accKey = String(formData.get('account_key'));
    let bicKeySample = document.getElementById('bic_key_sample') as HTMLSpanElement;
    let accKeySample = document.getElementById('account_key_sample') as HTMLSpanElement;
    result.classList.remove(...result.classList);
    if (/^\d{9}$/u.exec(bicKey) === null) {
        result.classList.add('failure');
        result.innerHTML = 'Неверный формат БИКа';
        bicStyle(bicKeySample, 'warning', 'БИК');
        return;
    } else {
        bicStyle(bicKeySample, 'success', bicKey);
    }
    if (/^\d{5}[\dАВСЕНКМРТХавсенкмртх]\d{14}$/u.exec(accKey) === null) {
        result.classList.add('failure');
        result.innerHTML = 'Неверный формат счёта';
        bicStyle(accKeySample, 'warning', 'СЧЁТ');
        return;
    } else {
        bicStyle(accKeySample, 'success', accKey);
    }
    //Change address
    updateHistory(location.protocol+'//'+location.host+'/bictracker/keying/'+bicKey+'/'+accKey+'/', 'Ключевание счёта '+accKey+pageTitle);
    //Initiate request
    result.classList.add('warning');
    result.innerHTML = 'Проверяем...';
    //Get spinner
    let spinner = document.getElementById('bic_spinner') as HTMLImageElement;
    spinner.classList.remove('hidden');
    ajax(location.protocol+'//'+location.host+'/api/bictracker/keying/', formData, 'json', 'POST', 60000, true).then(data => {
        result.classList.remove(...result.classList);
        if (data.data === true) {
            result.classList.add('success');
            result.innerHTML = 'Правильное ключевание';
        } else {
            result.classList.add('failure');
            if (data.data === false) {
                result.innerHTML = 'Непредвиденная ошибка';
            } else {
                result.innerHTML = 'Неверное ключевание. Ожидаемый ключ: ' + data.data + ' (' + accKey.replace(/(^\d{5}[\dАВСЕНКМРТХавсенкмртх]\d{2})(\d)(\d{11})$/u, '$1<span class="success">' + data.data + '</span>$3') + ')';
            }
        }
        spinner.classList.add('hidden');
    });
    return;
}

//Helper function for styling
function bicStyle(element: HTMLSpanElement, newClass: string, text: string = ''): void
{
    element.classList.remove(...element.classList);
    element.classList.add(newClass);
    element.innerHTML = text;
}

//Refresh BIC library through API
function bicRefresh(event: Event): void
{
    let refresh = event.target as HTMLInputElement;
    if (refresh.classList.contains('spin')) {
        //It already has been clicked, cancel event
        event.stopPropagation();
        event.preventDefault();
    } else {
        refresh.classList.add('spin');
        setTimeout(async function() {
            await ajax(location.protocol + '//' + location.host + '/api/bictracker/dbupdate/', null, 'json', 'PUT', 300000).then(data => {
                if (data.data === true) {
                    addSnackbar('Библиотека БИК обновлена', 'success');
                } else {
                    addSnackbar('Не удалось обновить библиотеку БИК', 'failure', 10000);
                }
            });
            refresh.classList.remove('spin');
        }, 500);
    }
}
