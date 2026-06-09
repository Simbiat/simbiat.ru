/**
 * @file Customization of the native code- and quote-like elements.
 */
import { empty } from '../Common/Helpers.mts';
import { Snackbar } from '../Common/Snackbar.mts';

/**
 * Customization of the native code- and quote-like elements.
 */
export class CodeQuote {
  /**
   * Copy the text of tags like q, samp, code, blockquote, var.
   * @param target - Target element.
   */
  private static async copyQuote(target: HTMLElement): Promise<string> {
    let node;
    //Get the parent node if click was on the copy picture/button
    if (target.tagName.toLowerCase() === 'q' || target.tagName.toLowerCase() === 'var') {
      node = target;
    } else {
      node = target.parentElement;
    }
    if (!node) {
      return '';
    }
    const tag_name = node.tagName.toLowerCase();
    let tag: string;
    switch (tag_name) {
      case 'samp':
        tag = 'Sample';
        break;
      case 'code':
        tag = 'Code';
        break;
      case 'blockquote':
      case 'q':
        tag = 'Quote';
        break;
      case 'var':
        tag = 'Variable';
        break;
      default:
        //Exit, since we do not support this tpy of node
        return '';
    }
    //Set text
    let quote_text = String(node.textContent);
    //Remove author from blockquotes
    if (tag_name === 'blockquote' && node.hasAttribute('data-author')) {
      // The string is being escaped. https://github.com/eslint-community/eslint-plugin-security/issues/201
      // eslint-disable-next-line security/detect-non-literal-regexp
      const author_match = new RegExp(`^(${RegExp.escape(node.getAttribute('data-author') ?? '')})`, 'iv');
      quote_text = quote_text.replace(author_match, '');
    }
    //Remove description from code and samp
    if ((tag_name === 'samp' || tag_name === 'code') && node.hasAttribute('data-description')) {
      // The string is being escaped. https://github.com/eslint-community/eslint-plugin-security/issues/201
      // eslint-disable-next-line security/detect-non-literal-regexp
      const desc_match = new RegExp(`^(${RegExp.escape(node.getAttribute('data-description') ?? '')})`, 'iv');
      quote_text = quote_text.replace(desc_match, '');
    }
    //Remove source from blockquotes, code and samp
    if (['blockquote', 'samp', 'code'].includes(tag_name) && node.hasAttribute('data-source')) {
      // The string is being escaped. https://github.com/eslint-community/eslint-plugin-security/issues/201
      // eslint-disable-next-line security/detect-non-literal-regexp
      const source_match = new RegExp(`(${RegExp.escape(node.getAttribute('data-source') ?? '')})$`, 'iv');
      quote_text = quote_text.replace(source_match, '');
    }
    try {
      await navigator.clipboard.writeText(quote_text);
      void new Snackbar(`${tag} copied to clipboard`, 'success');
    } catch {
      void new Snackbar(`Failed to copy ${tag.toLowerCase()}`, 'failure');
    }
    return String(node.textContent);
  }

  /**
   * Common function to style copiable blocks.
   * @param element - Element to style.
   */
  private static stylingCopiableBlocks(element: HTMLElement): void {
    const tag_name = element.tagName.toLowerCase();
    let attribute_name = 'data-description';
    let description_class_name = 'code_desc';
    if (tag_name === 'blockquote') {
      attribute_name = 'data-author';
      description_class_name = 'quote_author';
    }
    // Add a visual button
    const img_copy_block = document.createElement('img');
    img_copy_block.loading = 'lazy';
    img_copy_block.decoding = 'async';
    img_copy_block.alt = 'Click to copy block';
    img_copy_block.src = '/assets/images/copy.svg';
    img_copy_block.classList.add('copy_quote');
    element.insertAdjacentElement('afterbegin', img_copy_block);
    //Add description
    const description = element.getAttribute(attribute_name) ?? '';
    if (!empty(description)) {
      const description_span = document.createElement('span');
      description_span.classList.add(description_class_name);
      description_span.textContent = description;
      element.insertAdjacentElement('afterbegin', description_span);
    }
    //Add the source
    const source = element.getAttribute('data-source') ?? '';
    if (!empty(source)) {
      const source_span = document.createElement('span');
      source_span.classList.add('quote_source');
      source_span.textContent = source;
      element.insertAdjacentElement('beforeend', source_span);
    }
    //Add a listener to the button. Needs to be the last one due to manipulations with innerHTML
    element.querySelector<HTMLImageElement>('.copy_quote')
           ?.addEventListener('click', (event) => {
             void this.copyQuote(event.target as HTMLElement);
           });
  }

  /**
   * Process `samp` element.
   * @param element - `samp` element.
   */
  public static samp(element: HTMLElement): void {
    this.stylingCopiableBlocks(element);
  }

  /**
   * Process `code` element.
   * @param element - `code` element.
   */
  public static code(element: HTMLElement): void {
    this.stylingCopiableBlocks(element);
  }

  /**
   * Process `blockquote` element.
   * @param element - `blockquote` element.
   */
  public static blockquote(element: HTMLElement): void {
    this.stylingCopiableBlocks(element);
  }

  /**
   * Process `quote` element.
   * @param quote - `quote` element.
   */
  public static quote(quote: HTMLQuoteElement): void {
    // q tag is inline and a visual button does not suit it, so we add a tooltip to it
    if (!quote.hasAttribute('data-tooltip')) {
      quote.setAttribute('data-tooltip', 'Click to copy quote');
    }
    // Add listener
    quote.addEventListener('click', (event: MouseEvent) => {
      void this.copyQuote(event.target as HTMLElement);
    });
  }

  /**
   * Process `var` element.
   * @param variable - `var` element.
   */
  public static var(variable: HTMLElement): void {
    // var tag is inline and a visual button does not suit it, so we add a tooltip to it
    if (!variable.hasAttribute('data-tooltip')) {
      variable.setAttribute('data-tooltip', 'Click to copy variable');
    }
    // Add listener
    variable.addEventListener('click', (event: MouseEvent) => {
      void this.copyQuote(event.target as HTMLElement);
    });
  }
}
