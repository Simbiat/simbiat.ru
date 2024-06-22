export class ffCrests {
    form = null;
    background = null;
    frame = null;
    emblem = null;
    preview = null;
    backgroundImage = null;
    frameImage = null;
    emblemImage = null;
    constructor() {
        this.form = document.querySelector('#ff_merge_crest');
        this.background = document.querySelector('#crest_background');
        this.frame = document.querySelector('#crest_frame');
        this.emblem = document.querySelector('#crest_emblem');
        this.preview = document.querySelector('#crest_preview');
        this.backgroundImage = document.querySelector('#preview_background');
        this.frameImage = document.querySelector('#preview_frame');
        this.emblemImage = document.querySelector('#preview_emblem');
        if (this.background && this.frame && this.emblem && this.backgroundImage && this.frameImage && this.emblemImage) {
            [this.background, this.frame, this.emblem].forEach((item) => {
                item.addEventListener('click', () => {
                    this.updatePreview();
                });
                ['change', 'input', 'paste'].forEach((eventType) => {
                    item.addEventListener(eventType, this.updatePreview.bind(this));
                });
            });
            this.updatePreview();
            if (this.form) {
                submitIntercept(this.form, this.merge.bind(this));
            }
        }
    }
    updatePreview() {
        if (this.background && this.frame && this.emblem && this.backgroundImage && this.frameImage && this.emblemImage) {
            const background = this.background.value;
            const frame = this.frame.value;
            const emblem = this.emblem.value;
            this.backgroundImage.setAttribute('src', '');
            this.frameImage.setAttribute('src', '');
            this.emblemImage.setAttribute('src', '');
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
                if (empty(this.backgroundImage.getAttribute('src')) && empty(this.frameImage.getAttribute('src')) && empty(this.emblemImage.getAttribute('src'))) {
                    this.preview.classList.add('hidden');
                }
                else {
                    this.preview.classList.remove('hidden');
                }
            }
        }
    }
    merge() {
        if (this.form) {
            const formData = new FormData(this.form);
            const button = document.querySelector('#ff_merge_crest_submit');
            buttonToggle(button);
            void ajax(`${location.protocol}//${location.host}/api/fftracker/merge_crest`, formData, 'json', 'POST', 60000, true).
                then((response) => {
                const data = response;
                if (data.data === true) {
                    addSnackbar(`Crest merged successfully. Click <a href="${data.location}" download>here</a> to download.`, 'success', 0);
                }
                else {
                    addSnackbar(data.reason, 'failure', 10000);
                }
                buttonToggle(button);
            });
        }
    }
}
//# sourceMappingURL=crests.js.map