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
        });
        Textarea._instance = this;
    }
}
