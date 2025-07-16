class Tooltip extends HTMLElement
{
    private x = 0;
    private y = 0;
    private width = 0;
    private height = 0;

    public constructor()
    {
        super();
        //Add data-tooltip attribute for elements, that have title or alt and do not have tooltip either on them or their parent
        document.querySelectorAll('[alt]:not([alt=""]):not([data-tooltip]), [title]:not([title=""]):not([data-tooltip]):not(link)').forEach((item) => {
            //Add tooltip only if it's not set on parent element already
            if (item.parentElement?.hasAttribute('data-tooltip') === false) {
                item.setAttribute('data-tooltip', item.getAttribute('alt') ?? item.getAttribute('title') ?? '');
            }
        });
        //Add tabindex to elements with data-tooltip attribute, if missing
        document.querySelectorAll('[data-tooltip]:not([tabindex])').forEach((item) => {
            item.setAttribute('tabindex', '0');
        });
        //Handle tooltip positioning for mouse hover
        document.addEventListener('pointermove', this.onPointerMove.bind(this));
        //Handle tooltip positioning for focus
        document.querySelectorAll('[data-tooltip]:not([data-tooltip=""])').forEach((item) => {
            item.addEventListener('focus', this.onFocus.bind(this));
        });
        //Remove tooltip if an element without data-tooltip is selected. Needed to prevent focused tooltips from persisting
        document.querySelectorAll(':not([data-tooltip])').forEach((item) => {
            item.addEventListener('focus', () => { this.removeAttribute('data-tooltip'); });
        });
    }
    
    private onPointerMove(event: PointerEvent): void
    {
        this.update(event.target as HTMLElement);
        this.width = Math.max(event.width, 10);
        this.height = Math.max(event.height, 10);
        this.x = event.clientX + this.width;
        this.y = event.clientY - this.height;
        //Get block dimensions
        this.tooltipCursor();
    }
    
    private onFocus(event: Event): void
    {
        this.update(event.target as HTMLElement);
        const coordinates = (event.target as HTMLElement).getBoundingClientRect();
        this.x = coordinates.x + this.width;
        this.y = coordinates.y - (this.offsetHeight * 1.5);
        this.tooltipCursor();
    }

    //Update "cursor" position data in document style
    private tooltipCursor(): void
    {
        if (this.y + this.offsetHeight > window.innerHeight) {
            this.y = window.innerHeight - (this.offsetHeight * 2);
        }
        if (this.x + this.offsetWidth > window.innerWidth) {
            this.x = window.innerWidth - (this.offsetWidth * 1.5);
        }
        if (this.x - this.width < 0) {
            this.x = this.width;
        }
        if (this.y - this.height < 0) {
            this.y = this.height;
        }
        document.documentElement.style.setProperty('--cursor_x', `${this.x}px`);
        document.documentElement.style.setProperty('--cursor_y', `${this.y}px`);
    }

    //Update tooltip data
    private update(element: HTMLElement): void
    {
        const parent = element.parentElement;
        const tooltip = element.getAttribute('data-tooltip') ?? parent?.getAttribute('data-tooltip') ?? '';
        if (!empty(tooltip) && element !== this && matchMedia('(pointer:fine)').matches) {
            this.setAttribute('data-tooltip', 'true');
            this.innerHTML = tooltip;
        } else {
            this.removeAttribute('data-tooltip');
            this.innerHTML = '';
        }
    }
}
