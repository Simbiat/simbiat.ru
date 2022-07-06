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

//Timer to show remaining or elapsed time
function timer(target: HTMLElement, increase: boolean = true): void
{
    setInterval(function() {
        if (parseInt(target.innerHTML) > 0) {
            if (increase) {
                target.innerHTML = String(parseInt(target.innerHTML) + 1);
            } else {
                target.innerHTML = String(parseInt(target.innerHTML) - 1);
            }
        }
    }, 1000);
}

//Get and show color in attribute. For some reason, CSS's attr(value) does not show the value, if I do not do this
function colorValue(target: HTMLInputElement): void
{
    target.setAttribute('value', target.value);
}
function colorValueOnEvent(event: Event): void
{
    colorValue(event.target as HTMLInputElement);
}

//Toggle sidebar for small screens
function toggleSidebar(event: Event): void
{
    event.preventDefault();
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        if (sidebar.classList.contains('shown')) {
            sidebar.classList.remove('shown');
        } else {
            sidebar.classList.add('shown');
        }
    }
}
//Toggle navigation for small screens
function toggleNav(event: Event): void
{
    event.preventDefault();
    const sidebar = document.getElementById('navigation');
    if (sidebar) {
        if (sidebar.classList.contains('shown')) {
            sidebar.classList.remove('shown');
        } else {
            sidebar.classList.add('shown');
        }
    }
}

//Update document title and push to history. Required, since browsers mostly ignore title argument in pushState
function updateHistory(newUrl: string, title: string): void
{
    document.title = title;
    window.history.pushState(title, title, newUrl);
}
