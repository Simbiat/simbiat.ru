/**
 * @file Custom logic for `like-dis` element.
 */
import { Ajax } from '../Common/Ajax.mts';
import { Snackbar, SNACKBAR_FAIL_TIMEOUT } from '../Common/Snackbar.mts';

/**
 * Custom logic for `like-dis` element.
 */
export class LikeDis extends HTMLElement {
  private post_id = 0;
  private like_value = 0;
  private likes_count: HTMLSpanElement | null = null;
  private dislikes_count: HTMLSpanElement | null = null;
  private like_button: HTMLButtonElement | null = null;
  private dislike_button: HTMLButtonElement | null = null;

  /**
   * Initial processing when the element is added to DOM.
   */
  public connectedCallback(): void {
    this.likes_count = this.querySelector<HTMLSpanElement>('.likes_count');
    this.dislikes_count = this.querySelector<HTMLSpanElement>('.dislikes_count');
    this.like_button = this.querySelector<HTMLButtonElement>('.like_button');
    this.dislike_button = this.querySelector<HTMLButtonElement>('.dislike_button');
    //Set initial values for the object
    this.like_value = Number(this.getAttribute('data-liked') ?? 0);
    this.post_id = Number(this.getAttribute('data-post-id') ?? 0);
    //Attach listeners
    if (this.like_button) {
      this.like_button.addEventListener('click', (event) => {
        void this.like(event);
      });
    }
    if (this.dislike_button) {
      this.dislike_button.addEventListener('click', (event) => {
        void this.like(event);
      });
    }
  }

  /**
   * Function to update like/dislike on the backend.
   * @param event - Event to handle.
   */
  private async like(event: Event): Promise<void> {
    const button = event.currentTarget as HTMLInputElement;
    let action: string;
    if (button.classList.contains('like_button')) {
      action = 'like';
    } else {
      action = 'dislike';
    }
    if (this.post_id === 0) {
      void new Snackbar('No post ID', 'failure', SNACKBAR_FAIL_TIMEOUT);
      return;
    }
    await Ajax.request({
      url: `${location.protocol}//${location.host}/api/talks/posts/${this.post_id}/${action}`,
      method: 'PATCH',
      button,
      /**
       * Further processing on success.
       * @param response - Response from API endpoint.
       */
      onSuccess: (response) => {
        if ((response.data === 0 || response.data === 1 || response.data === -1)) {
          this.updateCounts(response.data);
        }
      },
    });
  }

  /**
   * Function to update counts and styling in UI.
   * @param new_value - New value.
   */
  private updateCounts(new_value: number): void {
    if (this.likes_count && this.dislikes_count && this.like_button && this.dislike_button) {
      //Remove styling
      this.likes_count.classList.remove('success');
      this.dislikes_count.classList.remove('failure');
      if (new_value === 0) {
        //Update values depending on previous ones
        if (this.like_value === 1) {
          this.likes_count.textContent = String(Number(this.likes_count.textContent) - 1);
        } else if (this.like_value === -1) {
          this.dislikes_count.textContent = String(Number(this.dislikes_count.textContent) - 1);
        }
        //Update tooltips
        this.like_button.setAttribute('data-tooltip', 'Like');
        this.dislike_button.setAttribute('data-tooltip', 'Dislike');
      } else if (new_value === 1) {
        //Reduce dislikes
        if (this.like_value === -1) {
          this.dislikes_count.textContent = String(Number(this.dislikes_count.textContent) - 1);
        }
        //Increase likes
        this.likes_count.textContent = String(Number(this.likes_count.textContent) + 1);
        //Style the span
        this.likes_count.classList.add('success');
        //Update tooltips
        this.like_button.setAttribute('data-tooltip', 'Remove like');
        this.dislike_button.setAttribute('data-tooltip', 'Dislike');
      } else if (new_value === -1) {
        //Reduce likes
        if (this.like_value === 1) {
          this.likes_count.textContent = String(Number(this.likes_count.textContent) - 1);
        }
        //Increase dislikes
        this.dislikes_count.textContent = String(Number(this.dislikes_count.textContent) + 1);
        //Style the span
        this.dislikes_count.classList.add('failure');
        //Update tooltips
        this.like_button.setAttribute('data-tooltip', 'Like');
        this.dislike_button.setAttribute('data-tooltip', 'Remove dislike');
      }
      //Sanitize counts, just in case
      if (Number(this.likes_count.textContent) < 0) {
        this.likes_count.textContent = '0';
      }
      if (Number(this.dislikes_count.textContent) < 0) {
        this.dislikes_count.textContent = '0';
      }
      //Update pre-saved value of the (dis)like
      this.setAttribute('data-liked', String(new_value));
      this.like_value = new_value;
    }
  }
}
