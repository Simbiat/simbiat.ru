'use strict';
const pageTitle = ' on Simbiat Software';

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
    ucInit();
    bicInit();
    //Customization for details tags
    new Details();
    //Customization for code and quote blocks
    new Quotes();
    formInit();
    fftrackerInit();
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
    //Add new tab icon to links opening in new tab
    new A();
    cleanGET();
    hashCheck();
}
