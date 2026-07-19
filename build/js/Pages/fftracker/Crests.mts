/**
 * @file Handle scripts on the FFXIV crests merge page.
 */
import { empty } from 'Common/Helpers.mts';
import { Ajax } from 'Common/Ajax.mts';
import { Snackbar } from 'Common/Snackbar.mts';
import { Form } from 'NativeElements/Form.mts';

/**
 * Handle scripts on the FFXIV crests merge page.
 */
export class Crests {
  private readonly form = document.querySelector<HTMLFormElement>('#ff_merge_crest');
  private readonly background = document.querySelector<HTMLInputElement>('#crest_background');
  private readonly frame = document.querySelector<HTMLInputElement>('#crest_frame');
  private readonly emblem = document.querySelector<HTMLInputElement>('#crest_emblem');
  private readonly preview = document.querySelector<HTMLDivElement>('#crest_preview');
  private readonly background_image = document.querySelector<HTMLImageElement>('#preview_background');
  private readonly frame_image = document.querySelector<HTMLImageElement>('#preview_frame');
  private readonly emblem_image = document.querySelector<HTMLImageElement>('#preview_emblem');

  public constructor() {
    if (this.background && this.frame && this.emblem && this.background_image && this.frame_image && this.emblem_image) {
      for (const item of [this.background, this.frame, this.emblem]) {
        item.addEventListener('click', () => {
          this.updatePreview();
        });
        for (const event_type of ['change', 'input', 'paste']) {
          item.addEventListener(event_type, this.updatePreview.bind(this));
        }
      }
      this.updatePreview();
      if (this.form) {
        Form.submitIntercept(this.form, () => {
          void this.merge();
        });
      }
    }
  }

  /**
   * Update image preview.
   */
  private updatePreview(): void {
    if (this.background && this.frame && this.emblem && this.background_image && this.frame_image && this.emblem_image) {
      //Get values of the fields
      const background: string = this.background.value;
      const frame: string = this.frame.value;
      const emblem: string = this.emblem.value;
      //Reset images
      this.background_image.setAttribute('src', '');
      this.frame_image.setAttribute('src', '');
      this.emblem_image.setAttribute('src', '');
      //Generate links and update src of image tags
      if (!empty(background) && this.background.checkValidity()) {
        this.background_image.setAttribute('src', `/assets/images/fftracker/crests-components/backgrounds/${background.slice(0, 3)
                                                                                                                      .toLowerCase()}/${background}`);
      }
      if (!empty(frame) && this.frame.checkValidity()) {
        this.frame_image.setAttribute('src', `/assets/images/fftracker/crests-components/frames/${frame}`);
      }
      if (!empty(emblem) && this.emblem.checkValidity()) {
        this.emblem_image.setAttribute('src', `/assets/images/fftracker/crests-components/emblems/${emblem.slice(0, 3)
                                                                                                          .toLowerCase()}/${emblem}`);
      }
      if (this.preview) {
        //Hide the preview element if it's empty
        if (empty(this.background_image.getAttribute('src')) && empty(this.frame_image.getAttribute('src')) && empty(this.emblem_image.getAttribute('src'))) {
          this.preview.classList.add('hidden');
        } else {
          this.preview.classList.remove('hidden');
        }
      }
    }
  }

  /**
   * Merge images into a crest.
   */
  private async merge(): Promise<void> {
    if (this.form) {
      const form_data = new FormData(this.form);
      const button = document.querySelector<HTMLButtonElement>('#ff_merge_crest_submit');
      await Ajax.request({
        url: `${location.protocol}//${location.host}/api/fftracker/merge_crest`,
        form_data,
        method: 'POST',
        button,
        /**
         * Further processing on success.
         * @param response - Response from API endpoint.
         */
        onSuccess: (response) => {
          void new Snackbar(`Crest merged successfully. Click <a href="${response.location}" download target="_blank">here</a> to download.`, 'success', 0);
        },
      });
    }
  }
}
