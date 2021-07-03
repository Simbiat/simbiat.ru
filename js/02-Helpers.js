//Get meta content
function getMeta(metaName) {
    'use strict';
    const metas = document.getElementsByTagName('meta');
    for (let i = 0; i < metas.length; i++) {
        if (metas[i].getAttribute('name') === metaName) {
            return metas[i].getAttribute('content');
        }
    }
    return null;
}

//Timer to show remaining or elapsed time
'use strict';
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
