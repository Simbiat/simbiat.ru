/**
 * @file Class holding various sanitization functions.
 */
import { type Config as PurifyConfig } from 'dompurify';

/**
 * Class holding various sanitization functions.
 */
export class Sanitize {
  /**
   * Only these tags are allowed inside HTML. `#text` means text nodes.
   */
  private static readonly ALLOWED_TAGS = ['#text', 'br', 'kbd', 'a', 'p', 'span', 'div'];
  /**
   * Only these attributes are allowed inside HTML.
   */
  private static readonly ALLOWED_ATTRIBUTES = ['class', 'crossorigin', 'decoding', 'loading', 'alt', 'src', 'href', 'rel', 'id', 'target', 'download'];

  public static readonly PURIFY_CONFIG: PurifyConfig = {
    RETURN_TRUSTED_TYPE: true,
    ALLOWED_TAGS: this.ALLOWED_TAGS,
    ALLOWED_ATTR: this.ALLOWED_ATTRIBUTES,
    KEEP_CONTENT: false,
  };
}
