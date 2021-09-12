/*globals ajax, pageTitle, updateHistory*/
/*exported bicInit*/

function bicInit()
{
    let bicKey = document.getElementById('bic_key');
    let accKey = document.getElementById('account_key');
    if (bicKey && accKey) {
        bicKey.addEventListener('input', bicCalc);
        accKey.addEventListener('input', bicCalc);
    }
}

function bicCalc()
{
    let result = document.getElementById('accCheckResult');
    let bicKey = document.getElementById('bic_key');
    let accKey = document.getElementById('account_key');
    let bicKeySample = document.getElementById('bic_key_sample');
    let accKeySample = document.getElementById('account_key_sample');
    result.classList.remove(...result.classList);
    if (/^[0-9]{9}$/u.exec(bicKey.value) === null) {
        result.classList.add('failure');
        result.innerHTML = 'Неверный формат БИКа';
        bicStyle(bicKeySample, 'warning', 'БИК');
        return;
    } else {
        bicStyle(bicKeySample, 'success', bicKey.value);
    }
    if (/^[0-9]{5}[0-9АВСЕНКМРТХавсенкмртх][0-9]{14}$/u.exec(accKey.value) === null) {
        result.classList.add('failure');
        result.innerHTML = 'Неверный формат счёта';
        bicStyle(accKeySample, 'warning', 'СЧЁТ');
        return;
    } else {
        bicStyle(accKeySample, 'success', accKey.value);
    }
    //Change address
    updateHistory(location.protocol+'//'+location.host+'/bictracker/keying/'+bicKey.value+'/'+accKey.value+'/', 'Ключевание счёта '+accKey.value+pageTitle);
    //Initiate request
    result.classList.add('warning');
    result.innerHTML = 'Проверяем...';
    ajax(location.protocol+'//'+location.host+'/api/bictracker/keying/'+bicKey.value+'/'+accKey.value+'/').then(data => {
        result.classList.remove(...result.classList);
        if (data === true) {
            result.classList.add('success');
            result.innerHTML = 'Правильное ключевание';
        } else {
            result.classList.add('failure');
            if (data === false) {
                result.innerHTML = 'Непредвиденная ошибка';
            } else {
                result.innerHTML = 'Неверное ключевание. Ожидаемый ключ: ' + data + ' (' + accKey.value.replace(/(^[0-9]{5}[0-9АВСЕНКМРТХавсенкмртх][0-9]{2})([0-9])([0-9]{11})$/u, '$1<span class="success">' + data + '</span>$3') + ')';
            }
        }
    });
}

function bicStyle(element, newClass, text = '')
{
    element.classList.remove(...element.classList);
    element.classList.add(newClass);
    element.innerHTML = text;
}
