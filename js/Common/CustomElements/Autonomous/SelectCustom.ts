//Class for select with description and image for each option
class SelectCustom extends HTMLElement
{
    private readonly icon: HTMLImageElement | null = null;
    private readonly select: HTMLSelectElement | null = null;
    private readonly label: HTMLLabelElement | null = null;
    private readonly description: HTMLDivElement | null = null;
    
    constructor() {
        super();
        this.select = this.querySelector('select') as HTMLSelectElement;
        this.label = this.querySelector('label') as HTMLLabelElement;
        this.icon = this.querySelector('.select_icon') as HTMLImageElement;
        this.description = this.querySelector('.select_description') as HTMLDivElement;
        //Enforcing certain values of the items
        this.icon.alt = 'Icon for ' + this.label.innerText.charAt(0).toLowerCase() + this.label.innerText.slice(1);
        this.icon.setAttribute('data-tooltip', this.icon.alt);
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
    private update()
    {
        if (this.select) {
            let option = this.select[this.select.selectedIndex] as HTMLOptionElement;
            let description = option.getAttribute('data-description') ?? '';
            let icon = option.getAttribute('data-icon') ?? '';
            if (this.description) {
                if (!/^\s*$/ui.test(description)) {
                    this.description.innerHTML = description;
                    this.description.classList.remove('hidden');
                } else {
                    this.description.classList.add('hidden');
                }
            }
            if (this.icon) {
                if (!/^\s*$/ui.test(icon)) {
                    this.icon.src = icon;
                    this.icon.classList.remove('hidden');
                } else {
                    this.icon.classList.add('hidden');
                }
            }
        }
    }
}
