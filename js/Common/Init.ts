const pageTitle = ' on Simbiat Software';
//Regex for proper email. This is NOT JS Regex, thus it has doubled slashes.
const emailRegex = '[\\p{L}\\d.!#$%&\'*+\\/=?^_`{|}~-]+@[a-zA-Z\\d](?:[a-zA-Z\\d\\-]{0,61}[a-zA-Z\\d])?(?:\\.[a-zA-Z\\d](?:[a-zA-Z\\d\\-]{0,61}[a-zA-Z\\d])?)*';
//Regex for username. This is NOT JS Regex, thus it has doubled slashes.
const userRegex = '^[\\p{L}\\d.!#$%&\'*+\\\\/=?^_`{|}~\\- ]{1,64}$';

//Stuff to do on load
document.addEventListener('DOMContentLoaded', init);
window.addEventListener('hashchange', function() {hashCheck();});

//Runs initialization routines
function init()
{
    //Input tags standardization
    new Input();
    //Minor standardization of textarea
    new Textarea();
    //Customization of forms
    new Form();
    //Customization for details tags
    new Details();
    //Customization for code and quote blocks
    new Quotes();
    //Click handling for toggling sidebar
    new Aside();
    new Nav();
    //Some customization for H1-H6 tags
    new Headings();
    //Back-to-top buttons
    customElements.define('back-to-top', BackToTop);
    //Timers
    customElements.define('time-r', Timer);
    //Web-share button
    customElements.define('web-share', WebShare);
    //Floating tooltip
    customElements.define('tool-tip', Tooltip);
    //Snackbar close button
    customElements.define('snack-close', SnackbarClose);
    //Gallery overlay
    customElements.define('gallery-overlay', Gallery);
    //Define image carousels
    customElements.define('image-carousel', CarouselList);
    //Define show-password icons
    customElements.define('password-show', PasswordShow);
    //Define password strength fields
    customElements.define('password-requirements', PasswordRequirements);
    //Define password strength fields
    customElements.define('password-strength', PasswordStrength);
    //Define block for (dis)likes
    customElements.define('like-dis', Likedis);
    //Define vertical tabs
    customElements.define('vertical-tabs', VerticalTabs);
    //Define image upload blocks
    customElements.define('image-upload', ImageUpload);
    //Define custom select blocks
    customElements.define('select-custom', SelectCustom);
    //Add new tab icon to links opening in new tab
    new A();
    //Process URL
    cleanGET();
    hashCheck();
    router();
    let tinyList = document.querySelectorAll('textarea.tinymce');
    if (tinyList.length > 0) {
        // I fail to make TS see the file with anything I do in "paths", yet this is correct code, and it does work as expected, so just ignoring this
        // @ts-ignore
        import('/js/tinymce/tinymce.min.js').then(() => {
            // @ts-ignore
            tinymce.init(tinySettings);
        });
    }
}
