const TIMEZONE = Intl.DateTimeFormat()
                     .resolvedOptions().timeZone;
const AJAX_TIMEOUT = 60000;
const SNACKBAR_FAIL_LIFE = 10000;
const ACCESS_TOKEN = getSearchParam('access_token');

//Runs initialization routines
function init(): void {
  //Input tags standardization
  const inputs = document.querySelectorAll('input');
  if (!empty(inputs)) {
    inputs.forEach((input) => {
      inputInit(input);
    });
  }
  //Minor standardization of textarea
  const textAreas = document.querySelectorAll('textarea');
  if (!empty(textAreas)) {
    textAreas.forEach((textarea) => {
      textareaInit(textarea);
    });
  }
  //Icons for new tabs
  const anchors = document.querySelectorAll('a');
  if (!empty(anchors)) {
    anchors.forEach((anchor) => {
      anchorInit(anchor);
    });
  }
  //Allow copying anchor links from Heading
  const headings = document.querySelectorAll('h1:not(#h1_title), h2, h3, h4, h5, h6');
  if (!empty(headings)) {
    headings.forEach((heading) => {
      headingInit(heading as HTMLHeadingElement);
    });
  }
  //Customization of forms
  const forms = document.querySelectorAll('form');
  if (!empty(forms)) {
    forms.forEach((form) => {
      formInit(form);
    });
  }
  //Customization for details tags
  const detailsTags = document.querySelectorAll('details');
  if (!empty(detailsTags)) {
    detailsTags.forEach((details) => {
      detailsInit(details);
    });
  }
  //Customization for code and quote blocks
  const sampTags = document.querySelectorAll('samp');
  if (!empty(sampTags)) {
    sampTags.forEach((samp) => {
      sampInit(samp);
    });
  }
  const codeTags = document.querySelectorAll('code');
  if (!empty(codeTags)) {
    codeTags.forEach((code) => {
      codeInit(code);
    });
  }
  const blockquotes = document.querySelectorAll('blockquote');
  if (!empty(blockquotes)) {
    blockquotes.forEach((blockquote) => {
      blockquoteInit(blockquote);
    });
  }
  const quotes = document.querySelectorAll('q');
  if (!empty(quotes)) {
    quotes.forEach((quote) => {
      qInit(quote);
    });
  }
  //Handle var tags
  const variables = document.querySelectorAll('var');
  if (!empty(variables)) {
    variables.forEach((variable) => {
      varInit(variable);
    });
  }
  //Handle dialog closure when clicking outside dialog content
  const dialogs = document.querySelectorAll('dialog');
  if (!empty(dialogs)) {
    dialogs.forEach((dialog) => {
      dialogInit(dialog);
    });
  }
  //Add images to gallery
  const images = document.querySelectorAll('img');
  if (!empty(images)) {
    images.forEach((image) => {
      imgInit(image);
    });
  }
  //Click handling for toggling navigation and sidebar
  customElements.define('nav-show', NavShow);
  customElements.define('nav-hide', NavHide);
  customElements.define('side-show', SideShow);
  customElements.define('side-hide', SideHide);
  //Login form
  customElements.define('login-form', LoginForm);
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
  customElements.define('gallery-close', GalleryClose);
  customElements.define('gallery-prev', GalleryPrev);
  customElements.define('gallery-next', GalleryNext);
  customElements.define('gallery-image', GalleryImage);
  //Define image carousels
  customElements.define('image-carousel', CarouselList);
  //Define OG Image
  customElements.define('og-image', OGImage);
  //Define show-password icons
  customElements.define('password-show', PasswordShow);
  //Define password strength fields
  customElements.define('password-requirements', PasswordRequirements);
  //Define password strength fields
  customElements.define('password-strength', PasswordStrength);
  //Define block for (dis)likes
  customElements.define('like-dis', Likedis);
  //Define vertical tabs
  customElements.define('tab-menu', TabMenu);
  //Define image upload blocks
  customElements.define('image-upload', ImageUpload);
  //Define custom select blocks
  customElements.define('select-custom', SelectCustom);
  //Define post form
  customElements.define('post-form', PostForm);
  //Create observer to react to new elements
  const newNodesObserver = new MutationObserver((mutations_list) => {
    mutations_list.forEach((mutation) => {
      mutation.addedNodes.forEach((added_node) => {
        customizeNewElements(added_node);
      });
    });
  });
  newNodesObserver.observe(document, {
    'attributes': false,
    'characterData': false,
    'childList': true,
    'subtree': true
  });
  //Process URL
  cleanGET();
  hashCheck();
  router();
}

//Stuff to do on load
const configUrlElement = document.querySelector('head > link[rel="preload"][as="fetch"]');
let sharedWithPHP = {};
if (configUrlElement && configUrlElement.getAttribute('href')) {
  const configUrl = configUrlElement.getAttribute('href');
  fetch(configUrl as string)
    .then(response => response.json())
    .then(config => {
      sharedWithPHP = config;
    })
    .catch(() => {
      sharedWithPHP = {};
    })
    .finally(() => {
      sharedWithPHP = Object.freeze(sharedWithPHP);
    });
}
document.addEventListener('DOMContentLoaded', init);
window.addEventListener('hashchange', () => {
  hashCheck();
});
