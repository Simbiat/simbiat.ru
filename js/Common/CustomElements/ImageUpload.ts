//Class for input type "file" with image preview
class ImageUpload extends HTMLElement
{
    private readonly preview: HTMLImageElement | null = null;
    private readonly file: HTMLInputElement | null = null;
    private readonly label: HTMLLabelElement | null = null;
    
    public constructor()
    {
        super();
        this.file = this.querySelector('input[type=file]');
        this.label = this.querySelector('label');
        this.preview = this.querySelector('img');
        if (this.file) {
            //Enforcing certain values of the items
            this.file.accept = 'image/avif,image/bmp,image/gif,image/jpeg,image/png,image/webp,image/svg+xml';
            this.file.placeholder = 'Image file';
            //Attach listener to file upload field
            this.file.addEventListener('change', () => {
                this.update();
            });
        }
        if (this.preview && this.label) {
            this.preview.alt = `Preview of ${this.label.innerText.charAt(0).toLowerCase()}${this.label.innerText.slice(1)}`;
            this.preview.setAttribute('data-tooltip', this.preview.alt);
            //In case we have a data-current, that is not empty - attempt to show it
            const current = this.preview.getAttribute('data-current') ?? '';
            if (!(/^\s*$/ui).test(current)) {
                this.preview.src = current;
                this.preview.classList.remove('hidden');
            }
        }
    }
    
    //Function to update preview of the avatar
    private update(): void
    {
        if (this.preview && this.file) {
            if (this.file.files?.[0]) {
                this.preview.src = URL.createObjectURL(this.file.files[0]);
                this.preview.classList.remove('hidden');
            } else {
                this.preview.classList.add('hidden');
            }
        }
    }
}
