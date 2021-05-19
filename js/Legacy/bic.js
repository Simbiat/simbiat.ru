function bic_init()
{
    $("#bic_search_input").on("input", function(){setTimeout(function(){general_search("/bic/search/", "#bic_search_input", ["nav", "#bic_searchlist"])}, search_delay);});
    $("#bic_search_input").on("keypress", function(e){if(e.which == 13) {general_search("/bic/search/", "#bic_search_input", ["nav", "#bic_searchlist"])}});
    $("#bic_search_input").focus();
    $("#bic_key").on('input', function(){
        bicCalc();
    });
    $("#account_key").on('input', function(){
        bicCalc();
    });
    $("#bic_refresh").on("click", bic_refresh);
}

function bicCalc()
{
    var resultdiv = document.getElementById('acccheckresult');
    var newnum = document.getElementById('bic_key');
    var accdiv = document.getElementById('account_key');
    if (/^[0-9]{9}$/.exec(newnum.value) === null) {
        resultdiv.style.color = "red";
        resultdiv.innerHTML = 'Неверный формат БИКа';
        return;
    }
    if (/^[0-9]{5}[0-9АВСЕНКМРТХавсенкмртх]{1}[0-9]{14}$/.exec(accdiv.value) === null) {
        resultdiv.style.color = "red";
        resultdiv.innerHTML = 'Неверный формат счёта';
        return;
    }
    resultdiv.style.color = "orange";
    resultdiv.innerHTML = 'Проверяем...';
    $.get(location.protocol+"//"+location.host+"/api/bictracker/accheck/"+newnum.value+"/"+accdiv.value+"/", function( data ) {
        if (data == true) {
            resultdiv.style.color = "green";
            resultdiv.innerHTML = 'Правильное ключевание';
        } else {
            resultdiv.style.color = "red";
            resultdiv.innerHTML = 'Неверное ключевание. Ожидаемый ключ: '+data+' ('+accdiv.value.replace(/(^[0-9]{5}[0-9АВСЕНКМРТХавсенкмртх]{1}[0-9]{2})([0-9]{1})([0-9]{11})$/, '$1<span class="bic_correct_key">'+data+'</span>$3')+')';
        }
    });
}