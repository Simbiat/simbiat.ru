class Tooltip extends HTMLElement
{
    private x: number = 0;
    private y: number = 0;

    constructor()
    {
        super();
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
        document.querySelectorAll('[data-tooltip]:not([data-tooltip=""])').forEach(item => {
            item.addEventListener('focus', this.onFocus.bind(this));
        });
        //Remove tooltip if an element without data-tooltip is selected. Needed to prevent focused tooltips from persisting
        document.querySelectorAll(':not([data-tooltip])').forEach(item => {
            item.addEventListener('focus', () => {this.removeAttribute('data-tooltip');})
        });
    }
    
    private onMouseMove(event: MouseEvent): void
    {
        this.update(event.target as HTMLElement);
        this.x = event.clientX;
        this.y = event.clientY;
        //Get block dimensions
        this.tooltipCursor();
    }
    
    private onFocus(event: Event): void
    {
        this.update(event.target as HTMLElement);
        let coordinates = (event.target as HTMLElement).getBoundingClientRect();
        this.x = coordinates.x;
        this.y = coordinates.y - this.offsetHeight * 1.5;
        this.tooltipCursor();
    }

    //Update "cursor" position data in document style
    private tooltipCursor(): void
    {
        if (this.y + this.offsetHeight > window.innerHeight) {
            this.y = window.innerHeight - this.offsetHeight * 2;
        }
        if (this.x + this.offsetWidth > window.innerWidth) {
            this.x = window.innerWidth - this.offsetWidth * 1.5;
        }
        document.documentElement.style.setProperty('--cursorX', this.x + 'px');
        document.documentElement.style.setProperty('--cursorY', this.y + 'px');
    }

    //Update tooltip data
    private update(element: HTMLElement): void
    {
        let parent = element.parentElement as HTMLElement;
        let tooltip = element.getAttribute('data-tooltip') ?? parent.getAttribute('data-tooltip') ?? null;
        if (tooltip && element !== this && matchMedia('(pointer:fine)').matches) {
            this.setAttribute('data-tooltip', 'true');
            this.innerHTML = tooltip;
        } else {
            this.removeAttribute('data-tooltip');
            this.innerHTML = '';
        }
    }
}
