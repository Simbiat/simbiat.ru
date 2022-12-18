class Textarea
{
    private static _instance: null | Textarea = null;

    constructor()
    {
        if (Textarea._instance) {
            return Textarea._instance;
        }
        //Enforce placeholder for textarea similar to text inputs
        Array.from(document.getElementsByTagName('textarea')).forEach(item => {
            if (!item.hasAttribute('placeholder')) {
                item.setAttribute('placeholder', item.value || item.type || 'placeholder');
            }
            if (item.maxLength > 0) {
                //Attach listener
                ['change', 'keydown', 'keyup',].forEach((eventType: string) => {
                    item.addEventListener(eventType, (event) => {this.countCharacters(event.target as HTMLTextAreaElement);})
                });
                //Call to set initial value
                this.countCharacters(item);
            }
        });
        Textarea._instance = this;
    }
    
    public countCharacters(textarea: HTMLTextAreaElement)
    {
        let label = textarea.labels[0] as HTMLLabelElement;
        label.setAttribute('data-curlength', '('+String(textarea.value.length)+'/'+String(textarea.maxLength)+'ch)');
        label.className = '';
        if (textarea.value.length >= textarea.maxLength) {
            label.classList.add('at_the_limit');
        } else {
            if (((100 * textarea.value.length) / textarea.maxLength) >= 75) {
                label.classList.add('close_to_limit');
            }
        }
    }
}
