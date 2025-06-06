export class ffCrests
{
    private readonly form: HTMLFormElement | null = null;
    private readonly background: HTMLInputElement | null = null;
    private readonly frame: HTMLInputElement | null = null;
    private readonly emblem: HTMLInputElement | null = null;
    private readonly preview: HTMLDivElement | null = null;
    private readonly backgroundImage: HTMLImageElement | null = null;
    private readonly frameImage: HTMLImageElement | null = null;
    private readonly emblemImage: HTMLImageElement | null = null;
    
    public constructor()
    {
        //Get form
        this.form = document.querySelector('#ff_merge_crest');
        //Get input fields
        this.background = document.querySelector('#crest_background');
        this.frame = document.querySelector('#crest_frame');
        this.emblem = document.querySelector('#crest_emblem');
        //Get image elements
        this.preview = document.querySelector('#crest_preview');
        this.backgroundImage = document.querySelector('#preview_background');
        this.frameImage = document.querySelector('#preview_frame');
        this.emblemImage = document.querySelector('#preview_emblem');
        if (this.background && this.frame && this.emblem && this.backgroundImage && this.frameImage && this.emblemImage) {
            [this.background, this.frame, this.emblem].forEach((item) => {
                item.addEventListener('click', () => {
                    this.updatePreview();
                });
                ['change', 'input', 'paste'].forEach((eventType: string) => {
                    item.addEventListener(eventType, this.updatePreview.bind(this));
                });
            });
            this.updatePreview();
            if (this.form) {
                submitIntercept(this.form, this.merge.bind(this));
            }
        }
    }
    
    private updatePreview(): void
    {
        if (this.background && this.frame && this.emblem && this.backgroundImage && this.frameImage && this.emblemImage) {
            //Get values of the fields
            const background: string = this.background.value;
            const frame: string = this.frame.value;
            const emblem: string = this.emblem.value;
            //Reset images
            this.backgroundImage.setAttribute('src', '');
            this.frameImage.setAttribute('src', '');
            this.emblemImage.setAttribute('src', '');
            //Generate links and update src of image tags
            if (!empty(background) && this.background.checkValidity()) {
                this.backgroundImage.setAttribute('src', `/assets/images/fftracker/crests-components/backgrounds/${background.slice(0, 3).toLowerCase()}/${background}`);
            }
            if (!empty(frame) && this.frame.checkValidity()) {
                this.frameImage.setAttribute('src', `/assets/images/fftracker/crests-components/frames/${frame}`);
            }
            if (!empty(emblem) && this.emblem.checkValidity()) {
                this.emblemImage.setAttribute('src', `/assets/images/fftracker/crests-components/emblems/${emblem.slice(0, 3).toLowerCase()}/${emblem}`);
            }
            if (this.preview) {
                //Hide preview element if it's empty
                if (empty(this.backgroundImage.getAttribute('src')) && empty(this.frameImage.getAttribute('src')) && empty(this.emblemImage.getAttribute('src'))) {
                    this.preview.classList.add('hidden');
                } else {
                    this.preview.classList.remove('hidden');
                }
            }
        }
    }
    
    private merge(): void
    {
        if (this.form) {
            const formData = new FormData(this.form);
            const button = document.querySelector('#ff_merge_crest_submit');
            buttonToggle(button as HTMLInputElement);
            void ajax(`${location.protocol}//${location.host}/api/fftracker/merge_crest`, formData, 'json', 'POST', ajaxTimeout, true).
                then((response) => {
                    const data = response as ajaxJSONResponse;
                    if (data.data === true) {
                        addSnackbar(`Crest merged successfully. Click <a href="${data.location}" download>here</a> to download.`, 'success', 0);
                    } else {
                        addSnackbar(data.reason, 'failure', snackbarFailLife);
                    }
                    buttonToggle(button as HTMLInputElement);
                });
        }
    }
}
