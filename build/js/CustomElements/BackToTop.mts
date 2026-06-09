/**
 * @file Custom logic for the `back-to-top` element.
 */
import type { HTMLHeaderElement } from '../Common/Aliases.mts';

/**
 * Custom logic for the `back-to-top` element.
 */
export class BackToTop extends HTMLElement {
  /**
   * Using `header`, since it's always the top-most element.
   */
  private readonly sentinel = document.querySelector<HTMLHeaderElement>('header');
  private sentinel_observer: IntersectionObserver | null = null;
  private heading_observer: IntersectionObserver | null = null;

  public constructor() {
    super();
    if (this.sentinel) {
      this.initTopObserver();
      this.initHeadingObserver();
      this.addEventListener('click', () => {
        window.scrollTo({
          behavior: 'smooth',
          left: 0,
          top: 0,
        });
      });
    }
  }

  /**
   * Initializing observer to determine if we are at the top of the page.
   */
  private initTopObserver(): void {
    if (!this.sentinel) {
      return;
    }

    this.sentinel_observer = new IntersectionObserver(
      (entries) => {
        if (entries[0]) {
          const at_top = entries[0].isIntersecting;
          this.classList.toggle('hidden', at_top);
        }
      },
      {
        // Add 5% top margin, so that button does not appear right away after `header` is gone from screen (~1 scroll to go past header and 1 scroll to get the button)
        rootMargin: '5% 0px 0px 0px',
        threshold: 0,
      },
    );

    this.sentinel_observer.observe(this.sentinel);
  }

  /**
   * Initialize observer to determine if there is a heading element at the top of the page (to update URL).
   */
  private initHeadingObserver(): void {
    const headings = document.querySelectorAll<HTMLHeadingElement>('h1:not(#h1_title), h2, h3, h4, h5, h6');
    if (!headings.length) {
      return;
    }

    this.heading_observer = new IntersectionObserver(
      (entries) => {
        if (window.location.hash.toLowerCase()
                  .startsWith('#gallery=')) {
          return;
        }
        for (const entry of entries) {
          if (entry.isIntersecting) {
            history.replaceState(
              document.title,
              document.title,
              `#${(entry.target as HTMLHeadingElement).id}`,
            );
            return;
          }
        }
      },
      {
        // -95% bottom margin help with updating the hash a little bit before the new header reaches the top of the page
        rootMargin: '0px 0px -95% 0px',
        threshold: 0,
      },
    );

    for (const heading of headings) {
      this.heading_observer.observe(heading);
    }
  }
}
