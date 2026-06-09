/**
 * @file Custom logic for the `post-form` element.
 */
import { empty } from '../Common/Helpers.mts';
import { loadTinyMCE } from '../Common/TinyMCE.mts';
import type { TinyMCE } from 'tinymce/tinymce.d.ts';

declare const tinymce: TinyMCE;

/**
 * Custom logic for the `post-form` element.
 */
export class PostForm extends HTMLElement {
  private textarea: HTMLTextAreaElement | null = null;
  private reply_to_input: HTMLInputElement | null = null;
  private label: HTMLLabelElement | null = null;

  /**
   * Initial processing when the element is added to DOM.
   */
  public connectedCallback(): void {
    this.textarea = this.querySelector<HTMLTextAreaElement>('textarea');
    this.reply_to_input = this.querySelector<HTMLInputElement>('#replying_to');
    this.label = this.querySelector<HTMLLabelElement>('.label_for_tinymce');
    if (this.textarea && !empty(this.textarea.id)) {
      void loadTinyMCE(this.textarea.id, false, true);
    }
  }

  /**
   * Reply to a post by attaching its ID to a form and focusing on the form.
   * @param post_id - ID of the post to reply to.
   */
  public replyTo(post_id: string): void {
    if (this.reply_to_input && !((/^\s*$/v).exec(post_id))) {
      //Update value
      this.reply_to_input.value = post_id;
      if (this.label) {
        this.label.textContent = `Replying to post #${post_id}`;
      }
      if (this.textarea) {
        tinymce.execCommand('mceFocus', false, this.textarea.id);
      }
    }
  }
}
