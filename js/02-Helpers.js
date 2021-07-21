/*exported getMeta, timer, openDetails, colorValue, colorValueOnEvent, toggleSidebar, toggleNav*/

//Get meta content
function getMeta(metaName) {
    const metas = document.getElementsByTagName('meta');
    for (let i = 0; i < metas.length; i++) {
        if (metas[i].getAttribute('name') === metaName) {
            return metas[i].getAttribute('content');
        }
    }
    return null;
}

//Timer to show remaining or elapsed time
function timer(target, increase = true) {
    setInterval(function() {
        if (parseInt(target.innerHTML) > 0) {
            if (increase === true) {
                target.innerHTML = parseInt(target.innerHTML) + 1;
            } else {
                target.innerHTML = parseInt(target.innerHTML) - 1;
            }
        }
    }, 1000);
}

//Get and show color in attribute. For some reason, CSS's attr(value) does not show the value, if I do not do this
function colorValue(target) {
    target.setAttribute('value', target.value);
}
function colorValueOnEvent(event) {
    colorValue(event.target);
}

//Toggle sidebar for small screens
function toggleSidebar(event) {
    event.preventDefault();
    const sidebar = document.getElementById('sidebar');
    if (sidebar.classList.contains('shown')) {
        sidebar.classList.remove('shown');
    } else {
        sidebar.classList.add('shown');
    }
}
//Toggle navigation for small screens
function toggleNav(event) {
    event.preventDefault();
    const sidebar = document.getElementById('navigation');
    if (sidebar.classList.contains('shown')) {
        sidebar.classList.remove('shown');
    } else {
        sidebar.classList.add('shown');
    }
}
