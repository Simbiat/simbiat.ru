/**
 * @file Customization of the native heading elements.
 */
import { empty } from '../Common/Helpers.mts';
import { Snackbar } from '../Common/Snackbar.mts';

/**
 * Customization of the native heading elements.
 */
export class Heading {
  /**
   * Maximum length of hash generated for HX tags.
   */
  private static readonly MAX_HEADER_HASH_LENGTH = 64;

  /**
   * Apply customizations.
   * @param heading - Heading element to customize.
   */
  public static init(heading: HTMLHeadingElement): void {
    //Add ID attribute to header tags if it's missing (needed for unique anchor links)
    if (!heading.hasAttribute('id')) {
      //Get initial ID
      let id = String(heading.textContent)
        .replaceAll(/\s/gv, '_')
        .replaceAll(/[^\w\-]/gv, '')
        .replaceAll(/^\d+/gmv, '')
        .replaceAll(/_{2,}/gv, '_')
        .replaceAll(/^_+$/gmv, '')
        .substring(0, this.MAX_HEADER_HASH_LENGTH);
      if (empty(id)) {
        id = 'heading';
      }
      //Get ID index, in case it's already used
      let index = 1;
      let alt_id = id;
      //Check if alt_id exists
      while (document.querySelector<HTMLElement>(`#${alt_id}`)) {
        //Increase index
        index += 1;
        alt_id = `${id}_${index}`;
      }
      heading.setAttribute('id', alt_id);
    }
    heading.addEventListener('click', (event: MouseEvent) => {
      //Get the element under the mouse pointer
      const element_under_mouse = document.elementFromPoint(event.clientX, event.clientY);
      //Check if it's an <a> element
      if (element_under_mouse?.tagName === 'A' || element_under_mouse?.closest('a')) {
        //Cancel this event if we clicked on an anchor, because it can confuse if we get notification about copy and follow the link right away
        return;
      }
      //Checking for selection. If it's present, most likely the text in anchor is being selected with the intention of copying it.
      //In this case, if we copy the anchor link, we may provide an undesired effect (although ctrl+c will most likely fire after this).
      const selection = window.getSelection();
      if (selection && selection.type !== 'Range') {
        //Generate anchor link
        const link = `${window.location.href.split('#')[0]}#${(event.target as HTMLHeadingElement).getAttribute('id') ?? ''}`;
        // Copy anchor link to clipboard
        try {
          void navigator.clipboard.writeText(link);
          void new Snackbar(`Anchor link for "${(event.target as HTMLHeadingElement).textContent ?? ''}" copied to clipboard`, 'success');
        } catch {
          void new Snackbar(`Failed to copy anchor link for "${(event.target as HTMLHeadingElement).textContent ?? ''}"`, 'failure');
        }
      }
    });
  }
}
