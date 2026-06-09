/**
 * @file Customization of the native image elements.
 */
import { basename, empty } from '../Common/Helpers.mts';

/**
 * Customization of the native image elements.
 */
export class Image {
  /**
   * Apply customizations.
   * @param img - Image element to customize.
   */
  public static init(img: HTMLImageElement): void {
    //Add alt, if empty
    if (empty(img.alt)) {
      img.alt = basename(String(img.src));
    }
    if (empty(img.loading)) {
      img.loading = 'lazy';
    }
    if (empty(img.decoding)) {
      img.decoding = 'async';
    }
    if (empty(img.crossOrigin) && !img.src.startsWith('http')) {
      img.crossOrigin = 'anonymous';
    }
    //Wrap gallery_zoom images in anchor
    if (img.classList.contains('gallery_zoom')) {
      //Check if the parent is already a link
      const parent = img.parentElement;
      if (parent && parent.nodeName.toLowerCase() !== 'a') {
        //Prepare the link
        const link = document.createElement('a');
        link.href = img.src;
        link.target = '_blank';
        if (!link.hasAttribute('data-tooltip')) {
          link.setAttribute('data-tooltip', (img.hasAttribute('data-tooltip') ? String(img.getAttribute('data-tooltip')) : String(img.alt)));
        }
        link.classList.add('gallery_zoom');
        //Create a clone of the image, and remove gallery_zoom class for cleanliness
        const clone = img.cloneNode(true) as HTMLImageElement;
        clone.classList.remove('gallery_zoom');
        //Append the clone to the link
        link.appendChild(clone);
        //Replace the original image with the link
        img.replaceWith(link);
      } else if (parent?.nodeName.toLowerCase() === 'a') {
        //Handle existing anchor
        (parent as HTMLAnchorElement).href = img.src;
        (parent as HTMLAnchorElement).target = '_blank';
        if (!parent.hasAttribute('data-tooltip')) {
          parent.setAttribute('data-tooltip', (img.hasAttribute('data-tooltip') ? String(img.getAttribute('data-tooltip')) : String(img.alt)));
        }
        parent.classList.add('gallery_zoom');
        img.classList.contains('gallery_zoom');
      }
    }
  }
}
