class VerticalTabs extends HTMLElement
{
    private readonly tabs: HTMLSpanElement[];
    private readonly contents: HTMLDivElement[];
    private readonly wrapper: HTMLDivElement;
    private currentTab: number | null = null;
    
    constructor() {
        super();
        this.wrapper = this.querySelector('tab-contents') as HTMLDivElement;
        this.tabs = Array.from(this.querySelectorAll('tab-name'));
        this.contents = Array.from(this.querySelectorAll('tab-content'));
        //Attach listener to tabs
        this.tabs.forEach((item) => {
            item.addEventListener('click', (event) => {
                this.tabSwitch(event.target as HTMLSpanElement)
            });
        });
        //Hide tab-contents block, if there is no active tab at the initial load
        if (this.wrapper.querySelector('.active')) {
            this.wrapper.classList.remove('hidden');
        }
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
        //If we clicked on the same tab - hide everything
        if (this.currentTab === tabIndex) {
            this.wrapper.classList.add('hidden');
        } else {
            this.currentTab = tabIndex;
            this.wrapper.classList.remove('hidden');
        }
    }
}
