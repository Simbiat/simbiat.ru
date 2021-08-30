/*exported searchInit*/

//Handle dynamic action attribute for search inputs
function searchInit()
{
    document.querySelectorAll('form.searchForm[data-baseURL]').forEach((item)=>{
        item.addEventListener('input', searchAction);
        item.addEventListener('change', searchAction);
        item.addEventListener('focus', searchAction);
    });
}

function searchAction(event)
{
    let search = event.target;
    let form = search.form;
    if (search.value === '') {
        form.action = form.getAttribute('data-baseURL');
    } else {
        form.action = form.getAttribute('data-baseURL') + search.value;
    }
    //Ensure that form will use POST (will also remove question mark on submit)
    form.method = 'post';
}
