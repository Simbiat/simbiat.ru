class Tooltip
{
    private readonly tooltip: HTMLDivElement;
    private x: number = 0;
    private y: number = 0;

    constructor()
    {
        this.tooltip = document.getElementById('tooltip') as HTMLDivElement;
        if (this.tooltip) {
            //Add data-tooltip attribute for elements, that have title or alt and do not have tooltip either on them or their parent
            document.querySelectorAll('[alt]:not([alt=""]):not([data-tooltip]), [title]:not([title=""]):not([data-tooltip])').forEach(item => {
                //Add tooltip only if it's not set on parent element already
                if (!(item.parentElement as HTMLElement).hasAttribute('data-tooltip')) {
                    item.setAttribute('data-tooltip', item.getAttribute('alt') ?? item.getAttribute('title') ?? '');
                }
            });
            //Add tabindex to elements with data-tooltip attribute, if missing
            document.querySelectorAll('[data-tooltip]:not([tabindex])').forEach(item => {
                item.setAttribute('tabindex', '0');
            });
            //Handle tooltip positioning for mouse hover
            document.addEventListener('mousemove', this.onMouseMove.bind(this));
            //Handle tooltip positioning for focus
            document.querySelectorAll('[data-tooltip]:not([Data-Attribute=""])').forEach(item => {
                item.addEventListener('focus', this.onFocus.bind(this));
            });
            //Remove tooltip if an element without data-tooltip is selected. Needed to prevent focused tooltips from persisting
            document.querySelectorAll(':not([data-tooltip])').forEach(item => {
                item.addEventListener('focus', this.remove.bind(this))
            });
        }
    }

    onMouseMove(event: MouseEvent): void
    {
        this.update(event.target as HTMLElement);
        this.x = event.clientX;
        this.y = event.clientY;
        //Get block dimensions
        this.tooltipCursor();
    }

    onFocus(event: Event): void
    {
        this.update(event.target as HTMLElement);
        let coordinates = (event.target as HTMLElement).getBoundingClientRect();
        this.x = coordinates.x;
        this.y = coordinates.y - this.tooltip.offsetHeight * 1.5;
        this.tooltipCursor();
    }

    remove(): void
    {
        this.tooltip.removeAttribute('data-tooltip');
    }

    //Update "cursor" position data in document style
    tooltipCursor(): void
    {
        if (this.y + this.tooltip.offsetHeight > window.innerHeight) {
            this.y = window.innerHeight - this.tooltip.offsetHeight * 2;
        }
        if (this.x + this.tooltip.offsetWidth > window.innerWidth) {
            this.x = window.innerWidth - this.tooltip.offsetWidth * 1.5;
        }
        document.documentElement.style.setProperty('--cursorX', this.x + 'px');
        document.documentElement.style.setProperty('--cursorY', this.y + 'px');
    }

    //Update tooltip data
    update(element: HTMLElement): void
    {
        let parent = element.parentElement as HTMLElement;
        if (element.hasAttribute('data-tooltip') || parent.hasAttribute('data-tooltip')) {
            this.tooltip.setAttribute('data-tooltip', element.getAttribute('data-tooltip') ?? parent.getAttribute('data-tooltip') ?? '');
        } else {
            this.tooltip.removeAttribute('data-tooltip');
        }
    }
}
