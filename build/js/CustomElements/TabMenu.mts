/**
 * @file Custom logic for the `tab-menu` element.
 */
import { pageRefresh } from '../Common/Helpers.mts';

/**
 * Custom logic for the `time-r` element.
 */
export class TabMenu extends HTMLElement {
  private tabs: HTMLAnchorElement[] = [];
  private contents: NodeListOf<HTMLDivElement> = [] as unknown as NodeListOf<HTMLDivElement>;
  private current_tab = 0;

  /**
   * Initial processing when the element is added to DOM.
   */
  public connectedCallback(): void {
    this.tabs = Array.from(this.querySelectorAll<HTMLAnchorElement>('a.tab_name[id^=tab_name_]'));
    this.contents = this.querySelectorAll<HTMLDivElement>('div.tab_content[id^=tab_content_]');
    let current_tab = 0;
    for (const tab of this.tabs) {
      tab.setAttribute('role', 'tab');
      tab.setAttribute('aria-controls', tab.id.replace('tab_name_', 'tab_content_'));
      tab.setAttribute('href', `#${tab.id}`);
      if (tab.classList.contains('active')) {
        this.current_tab = current_tab;
        this.showTab(tab);
      } else {
        this.hideTab(tab);
      }
      current_tab++;
    }
    for (const content of this.contents) {
      content.setAttribute('role', 'tabpanel');
      content.setAttribute('aria-labelledby', content.id.replace('tab_content_', 'tab_name_'));
      if (content.classList.contains('active')) {
        this.showContent(content);
      } else {
        this.hideContent(content);
      }
    }
    for (const tab of this.tabs) {
      tab.addEventListener('click', (event: MouseEvent) => {
        event.preventDefault();
        event.stopImmediatePropagation();
        const target = event.target as HTMLAnchorElement;
        this.tabSwitch(target);
      });
      tab.addEventListener('keydown', (event: KeyboardEvent) => {
        this.keyboardNavigation(event);
      });
    }
    // Get count of all tabs, including regular links
    if (this.tabs.length < 2) {
      for (const tab of this.tabs) {
        tab.classList.add('hidden', 'active');
        if (this.contents[0]) {
          this.contents[0].classList.add('active');
        }
      }
    }
  }

  /**
   * Switch the tab.
   * @param target - Tab to switch to.
   */
  public tabSwitch(target: HTMLAnchorElement): void {
    let tab_index = 0;
    // Hide all the content first, so that there would not be a case when we have 2 content divs shown at once
    for (const [index, tab] of this.tabs.entries()) {
      if (typeof index !== 'number') {
        // Something is very wrong, index is supposed to be numeric
        return;
      }
      if (tab === target) {
        // Set the index for
        tab_index = index;
      }
      this.hideTab(tab);
      // eslint-disable-next-line security/detect-object-injection
      if (this.contents[index]) {
        // eslint-disable-next-line security/detect-object-injection
        this.hideContent(this.contents[index]);
      }
    }
    // Mark the current tab and respective content as active
    this.showTab(target);
    target.focus();
    // Follow the link if we have one
    if (target.href !== '' && target.href !== window.location.href && ((target.getAttribute('href')
                                                                              ?.startsWith('#tab_name_')) === false)) {
      pageRefresh(target.href);
      return;
    }
    // Otherwise, try to switch tab contents
    // eslint-disable-next-line security/detect-object-injection
    const tab_content = this.contents[tab_index];
    if (tab_content) {
      this.showContent(tab_content);
      const url = new URL(document.location.href, window.location.origin);
      if (((target.getAttribute('href')
                  ?.startsWith('#tab_name_')) === true)) {
        // @ts-expect-error Already know it's a string here
        url.hash = target.getAttribute('href');
      }
      window.history.replaceState(document.title, document.title, url.toString());
      this.current_tab = tab_index;
    }
  }

  /**
   * Show the tab.
   * @param target - Tab to show.
   */
  private showTab(target: HTMLAnchorElement): void {
    target.classList.add('active');
    target.setAttribute('aria-selected', 'true');
    target.setAttribute('tabindex', '-1');
  }

  /**
   * Hide the tab.
   * @param target - Tab to hide.
   */
  private hideTab(target: HTMLAnchorElement): void {
    target.classList.remove('active');
    target.setAttribute('aria-selected', 'false');
    target.setAttribute('tabindex', '0');
  }

  /**
   * Show the tab's content.
   * @param target - Tab content to show.
   */
  private showContent(target: HTMLDivElement): void {
    target.classList.add('active');
    target.setAttribute('aria-hidden', 'false');
    target.setAttribute('tabindex', '0');
  }

  /**
   * Hide the tab's content.
   * @param target - Tab content to hide.
   */
  private hideContent(target: HTMLDivElement): void {
    target.classList.remove('active');
    target.setAttribute('aria-hidden', 'true');
    target.setAttribute('tabindex', '-1');
  }

  /**
   * Process keyboard events.
   * @param event - Event to process.
   */
  private keyboardNavigation(event: KeyboardEvent): void {
    let flag = false;
    let new_tab = 0;
    switch (event.key) {
      case 'ArrowLeft':
        if (this.current_tab === 0) {
          new_tab = -1;
        } else {
          new_tab = this.current_tab - 1;
        }
        flag = true;
        break;
      case 'ArrowRight':
        if (this.current_tab !== this.tabs.length - 1) {
          new_tab = this.current_tab + 1;
        }
        flag = true;
        break;
      case 'Home':
        flag = true;
        break;
      case 'End':
        new_tab = -1;
        flag = true;
        break;
      default:
        break;
    }
    const tab = this.tabs.at(new_tab);
    if (flag && tab) {
      event.stopPropagation();
      event.preventDefault();
      this.tabSwitch(tab);
    }
  }
}
