/**
 * @file Class holding various sanitization functions.
 */
export class Sanitize {
  /**
   * Only these tags are allowed inside HTML.
   */
  private static readonly ALLOWED_TAGS = new Set(['br', 'kbd', 'a', 'p', 'span', 'div']);
  /**
   * Only these attributes are allowed inside HTML.
   */
  private static readonly ALLOWED_ATTRIBUTES = new Set(['class', 'crossorigin', 'decoding', 'loading', 'alt', 'src', 'href', 'rel', 'id', 'target']);

  /**
   * Sanitize an HTML node to retain only allowed tags with allowed attributes.
   * @param html - HTML node to sanitize.
   */
  public static html(html: Node | string): DocumentFragment {
    const template = document.createElement('template');
    if (html instanceof Node) {
      template.replaceChildren(html);
    } else {
      // Suppressing inspections, since we are sanitizing here
      /* eslint-disable-next-line github/no-inner-html, no-unsanitized/property */ // noinspection InnerHTMLJS
      template.innerHTML = html;
    }
    this.sanitizeNode(template.content);
    return template.content;
  }

  /**
   * Sanitize each node individually and recursively.
   * @param node - Node to sanitize.
   */
  private static sanitizeNode(node: Node): void {
    for (const child of [...node.childNodes]) {
      if (child.nodeType !== Node.ELEMENT_NODE) {
        // text nodes are fine as-is
        continue;
      }
      const el = child as Element;
      if (this.ALLOWED_TAGS.has(el.tagName.toLowerCase())) {
        // Strip every attribute from allowed elements, except for those attributes that are allowed
        for (const attr of [...el.attributes]) {
          if (!this.ALLOWED_ATTRIBUTES.has(attr.name.toLowerCase())) {
            el.removeAttribute(attr.name);
          }
        }
        // Recurse into allowed children
        this.sanitizeNode(el);
      } else {
        el.remove();
      }
    }
  }
}
