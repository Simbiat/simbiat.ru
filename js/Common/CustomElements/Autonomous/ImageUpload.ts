//Class for input type "file" with image preview
class ImageUpload extends HTMLElement
{
    private readonly preview: HTMLImageElement | null = null;
    private readonly file: HTMLInputElement | null = null;
    private readonly label: HTMLLabelElement | null = null;
    
    constructor() {
        super();
        this.file = this.querySelector('input[type=file]') as HTMLInputElement;
        this.label = this.querySelector('label') as HTMLLabelElement;
        this.preview = this.querySelector('img') as HTMLImageElement;
        //Enforcing certain values of the items
        this.file.accept = 'image/avif,image/bmp,image/gif,image/jpeg,image/png,image/webp,image/svg+xml';
        this.file.placeholder = 'Image file';
        this.preview.alt = 'Preview of ' + this.label.innerText.charAt(0).toLowerCase() + this.label.innerText.slice(1);
        this.preview.setAttribute('data-tooltip', this.preview.alt);
        //Attach listener to file upload field
        if (this.file) {
            this.file.addEventListener('change', () => {
                this.update();
            });
        }
    }
    
    //Function to update preview of the avatar
    private update()
    {
        if (this.preview && this.file) {
            if (this.file.files && this.file.files[0]) {
                this.preview.src = URL.createObjectURL(this.file.files[0] as File);
                this.preview.classList.remove('hidden');
            } else {
                this.preview.classList.add('hidden');
            }
        }
    }
}
