class TabMenu extends HTMLElement {
  private readonly tabs: HTMLSpanElement[];
  private readonly contents: HTMLDivElement[];
  private readonly wrapper: HTMLDivElement | null = null;
  private current_tab: number | null = null;

  public constructor() {
    super();
    this.wrapper = this.querySelector('tab-contents');
    this.tabs = Array.from(this.querySelectorAll('a.tab_name'));
    this.contents = Array.from(this.querySelectorAll('tab-content'));
    // Attach listener to tabs
    for (const tab of this.tabs) {
      tab.addEventListener('click', (event: MouseEvent) => {
        event.preventDefault();
        event.stopImmediatePropagation();
        const target = event.target as HTMLAnchorElement;
        this.tabSwitch(target);
      });
    }
    this.updateCurrentTab();
    // Hide tab-contents block if there is no active tab at the initial load
    if (this.wrapper?.querySelector('.active')) {
      this.wrapper.classList.remove('hidden');
    }
  }

  public tabSwitch(target: HTMLAnchorElement) {
    let tab_index = 0;
    // Hide all the content first, so that there would not be a case, when we have 2 content divs shown at once
    for (const [index, tab] of this.tabs.entries()) {
      if (tab === target) {
        // Set the index for
        tab_index = index;
      }
      tab.classList.remove('active');
      if (this.contents[index]) {
        (this.contents[index] as HTMLSpanElement).classList.remove('active');
      }
    }
    this.wrapper?.classList.add('hidden');
    // Mark the current tab and respective content as active
    if (this.current_tab !== tab_index) {
      target.classList.add('active');
      // Follow the link if we have one
      if (target.href !== '' && target.href !== window.location.href) {
        window.location.assign(encodeURI(target.href));
        return;
      }
      // Otherwise, try to switch tab contents
      if (this.contents[tab_index]) {
        (this.contents[tab_index] as HTMLSpanElement).classList.add('active');
        const url = new URL(document.location.href);
        window.history.replaceState(document.title, document.title, url.toString()
                                                                       .replace('#' + target.id, ''));
      }
    }
    if (this.wrapper) {
      this.updateCurrentTab();
      if (this.wrapper.querySelector('.active')) {
        this.wrapper.classList.remove('hidden');
      }
    }
  }

  private updateCurrentTab(): void {
    this.current_tab = null;
    for (const [index, tab] of this.tabs.entries()) {
      if (tab.classList.contains('active')) {
        this.current_tab = index;
      }
    }
  }
}
