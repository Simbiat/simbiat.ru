/*These are functions, that are used to somehow style or standardise different elements*/
function placeholders(): void
{
    //Enforce placeholder for textarea similar to text inputs
    Array.from(document.getElementsByTagName('textarea')).forEach(item => {
        if (!item.hasAttribute('placeholder')) {
            item.setAttribute('placeholder', item.value || item.type || 'placeholder');
        }
    });
}
