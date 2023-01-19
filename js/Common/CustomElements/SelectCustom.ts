//Class for select with description and image for each option
class SelectCustom extends HTMLElement
{
    private readonly icon: HTMLImageElement | null = null;
    private readonly select: HTMLSelectElement | null = null;
    private readonly label: HTMLLabelElement | null = null;
    private readonly description: HTMLDivElement | null = null;
    
    public constructor() {
        super();
        this.select = this.querySelector('select');
        this.label = this.querySelector('label');
        this.icon = this.querySelector('.select_icon');
        this.description = this.querySelector('.select_description');
        //Enforcing certain values of the items
        if (this.icon && this.label) {
            this.icon.alt = `Icon for ${this.label.innerText.charAt(0).toLowerCase()}${this.label.innerText.slice(1)}`;
            this.icon.setAttribute('data-tooltip', this.icon.alt);
        }
        //Attach listener to file upload field
        if (this.select) {
            this.select.addEventListener('change', () => {
                this.update();
            });
        }
        //Run initial update
        this.update();
    }
    
    //Function to update preview of the avatar
    private update(): void
    {
        if (this.select) {
            const option = this.select[this.select.selectedIndex] as HTMLOptionElement;
            const description = option.getAttribute('data-description') ?? '';
            const icon = option.getAttribute('data-icon') ?? '';
            if (this.description) {
                if ((/^\s*$/ui).test(description)) {
                    this.description.classList.add('hidden');
                } else {
                    this.description.innerHTML = description;
                    this.description.classList.remove('hidden');
                }
            }
            if (this.icon) {
                if ((/^\s*$/ui).test(icon)) {
                    this.icon.classList.add('hidden');
                } else {
                    this.icon.src = icon;
                    this.icon.classList.remove('hidden');
                }
            }
        }
    }
}
