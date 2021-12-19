/*exported tooltipInit*/

function tooltipInit()
{
    tabForTips();
    //Handle tooltip positioning for mouse hover
    document.onmousemove = function (e) {
        tooltip(e.target);
        let x = e.clientX,
            y = e.clientY;
        document.documentElement.style.setProperty('--cursorX', x + 'px');
        document.documentElement.style.setProperty('--cursorY', y + 'px');
    };
    //Handle tooltip positioning for focus
    document.querySelectorAll('[data-tooltip]').forEach(item => {
        item.addEventListener('focus', function(e) {
            tooltip(e.target);
            let coordinates = e.target.getBoundingClientRect();
            let block = document.getElementById('tooltip');
            let x = coordinates.x,
                y = coordinates.y - block.offsetHeight * 1.5;
            document.documentElement.style.setProperty('--cursorX', x + 'px');
            document.documentElement.style.setProperty('--cursorY', y + 'px');
        });
    });
    //Remove tooltip if an element without data-tooltip is selected. Needed to prevent focused tooltips from persisting
    document.querySelectorAll(':not([data-tooltip])').forEach(item => {
        item.addEventListener('focus', function() {
            let block = document.getElementById('tooltip');
            block.removeAttribute('data-tooltip');
        });
    });
}

//Add tabindex to elements with data-tooltip attribute, if missing
function tabForTips()
{
    document.querySelectorAll('[data-tooltip]:not([tabindex])').forEach(item => {
        item.setAttribute('tabindex', '0');
    });
}

function tooltip(element)
{
    let parent = element.parentElement;
    let block = document.getElementById('tooltip');
    if (element.hasAttribute('data-tooltip') || parent.hasAttribute('data-tooltip')) {
        block.setAttribute('data-tooltip', element.getAttribute('data-tooltip') ?? parent.getAttribute('data-tooltip'));// jshint ignore:line
    } else {
        block.removeAttribute('data-tooltip');
    }
}
