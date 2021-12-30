/*exported tooltipInit*/

function tooltipInit()
{
    addTooltips();
    tabForTips();
    //Handle tooltip positioning for mouse hover
    document.onmousemove = function (e) {
        tooltip(e.target);
        let x = e.clientX,
            y = e.clientY;
        //Get block dimensions
        tooltipCursor(x, y);
    };
    //Handle tooltip positioning for focus
    document.querySelectorAll('[data-tooltip]').forEach(item => {
        item.addEventListener('focus', function(e) {
            tooltip(e.target);
            let coordinates = e.target.getBoundingClientRect();
            let block = document.getElementById('tooltip');
            let x = coordinates.x,
                y = coordinates.y - block.offsetHeight * 1.5;
            tooltipCursor(x, y);
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

//Update "cursor" position data in document style
function tooltipCursor(x, y)
{
    let block = document.getElementById('tooltip');
    if (y + block.offsetHeight > window.innerHeight) {
        y = window.innerHeight - block.offsetHeight * 2;
    }
    if (x + block.offsetWidth > window.innerWidth) {
        x = window.innerWidth - block.offsetWidth * 1.5;
    }
    document.documentElement.style.setProperty('--cursorX', x + 'px');
    document.documentElement.style.setProperty('--cursorY', y + 'px');
}

//Add tabindex to elements with data-tooltip attribute, if missing
function tabForTips()
{
    document.querySelectorAll('[data-tooltip]:not([tabindex])').forEach(item => {
        item.setAttribute('tabindex', '0');
    });
}

//Add data-tooltip attribute for elements, that have title or alt and do not have tooltip either on them or their parent
function addTooltips()
{
    document.querySelectorAll('[alt]:not([alt=""]):not([data-tooltip]), [title]:not([title=""]):not([data-tooltip])').forEach(item => {
        //Add tooltip only if it's not set on parent element already
        if (item.parentElement.hasAttribute('data-tooltip') === false) {
            item.setAttribute('data-tooltip', item.getAttribute('alt') ?? item.getAttribute('title'));// jshint ignore:line
        }
    });
}

//Update tooltip data
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
