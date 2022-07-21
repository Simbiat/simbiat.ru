//Get meta content
function getMeta(metaName: string): string|null {
    const metas = Array.from(document.getElementsByTagName('meta'));
    let tag = metas.find(obj => {
        return obj.name === metaName
    })
    if (tag) {
        return tag.getAttribute('content');
    } else {
        return null;
    }
}

//Update document title and push to history. Required, since browsers mostly ignore title argument in pushState
function updateHistory(newUrl: string, title: string): void
{
    document.title = title;
    window.history.pushState(title, title, newUrl);
}

//Function to intercept both form submission and Enter key pressed in the form (which normally also submits it)
function submitIntercept(form: HTMLFormElement, callable: Function): void
{
    form.addEventListener('submit', function(event:SubmitEvent) {
        event.preventDefault();
        event.stopPropagation();
        callable();
        return false;
    });
    form.addEventListener('keydown', function(event: KeyboardEvent) {
        if(event.code === 'Enter') {
            event.preventDefault();
            event.stopPropagation();
            callable();
            return false;
        } else {
            return true;
        }
    });
}
