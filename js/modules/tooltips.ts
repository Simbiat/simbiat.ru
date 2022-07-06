/*exported tooltipInit*/

function tooltipInit(): void
{
    addTooltips();
    tabForTips();
    //Handle tooltip positioning for mouse hover
    document.onmousemove = function (e: MouseEvent) {
        tooltip(e.target as HTMLElement);
        let x = e.clientX,
            y = e.clientY;
        //Get block dimensions
        tooltipCursor(x, y);
    };
    //Handle tooltip positioning for focus
    document.querySelectorAll('[data-tooltip]:not([Data-Attribute=""])').forEach(item => {
        item.addEventListener('focus', function(e) {
            tooltip(e.target as HTMLElement);
            let coordinates = (e.target as HTMLElement).getBoundingClientRect();
            let block = document.getElementById('tooltip') as HTMLDivElement;
            let x = coordinates.x,
                y = coordinates.y - block.offsetHeight * 1.5;
            tooltipCursor(x, y);
        });
    });
    //Remove tooltip if an element without data-tooltip is selected. Needed to prevent focused tooltips from persisting
    document.querySelectorAll(':not([data-tooltip])').forEach(item => {
        item.addEventListener('focus', function() {
            let block = document.getElementById('tooltip') as HTMLDivElement;
            block.removeAttribute('data-tooltip');
        });
    });
}

//Update "cursor" position data in document style
function tooltipCursor(x: number, y: number): void
{
    let block = document.getElementById('tooltip') as HTMLDivElement;
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
function tabForTips(): void
{
    document.querySelectorAll('[data-tooltip]:not([tabindex])').forEach(item => {
        item.setAttribute('tabindex', '0');
    });
}

//Add data-tooltip attribute for elements, that have title or alt and do not have tooltip either on them or their parent
function addTooltips(): void
{
    document.querySelectorAll('[alt]:not([alt=""]):not([data-tooltip]), [title]:not([title=""]):not([data-tooltip])').forEach(item => {
        //Add tooltip only if it's not set on parent element already
        if (!(item.parentElement as HTMLElement).hasAttribute('data-tooltip')) {
            item.setAttribute('data-tooltip', item.getAttribute('alt') ?? item.getAttribute('title') ?? '');
        }
    });
}

//Update tooltip data
function tooltip(element: HTMLElement): void
{
    let parent = element.parentElement as HTMLElement;
    let block = document.getElementById('tooltip') as HTMLDivElement;
    if (element.hasAttribute('data-tooltip') || parent.hasAttribute('data-tooltip')) {
        block.setAttribute('data-tooltip', element.getAttribute('data-tooltip') ?? parent.getAttribute('data-tooltip') ?? '');
    } else {
        block.removeAttribute('data-tooltip');
    }
}
