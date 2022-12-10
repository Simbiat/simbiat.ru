class VerticalTabs extends HTMLElement
{
    private readonly tabs: HTMLSpanElement[];
    private readonly contents: HTMLDivElement[];
    
    constructor() {
        super();
        this.tabs = Array.from(this.querySelectorAll('tab-name'));
        this.contents = Array.from(this.querySelectorAll('tab-content'));
        //Attach listener to tabs
        this.tabs.forEach((item) => {
            item.addEventListener('click', (event) => {
                this.tabSwitch(event.target as HTMLSpanElement)
            });
        });
    }
    
    //Function to switch tabs
    private tabSwitch(target: HTMLSpanElement)
    {
        let tabIndex = 0;
        //Hide all the content first, so that there would not be a case, when we have 2 content divs shown at once
        this.tabs.forEach((item, index) => {
            if (item === target) {
                //Set the index for
                tabIndex = index;
            }
            item.classList.remove('active');
            if (this.contents[index]) {
                (this.contents[index] as HTMLSpanElement).classList.remove('active');
            }
        });
        //Mark current tab and respective content as active
        target.classList.add('active');
        if (this.contents[tabIndex]) {
            (this.contents[tabIndex] as HTMLSpanElement).classList.add('active');
        }
    }
}
