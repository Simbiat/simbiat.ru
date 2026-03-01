import { getSearchParam, cleanGET, hashCheck, router } from './Common/Url.ts';
import {
  customizeNewElements,
  inputInit,
  textareaInit,
  anchorInit,
  headingInit,
  formInit,
  detailsInit,
  sampInit,
  codeInit,
  blockquoteInit,
  qInit,
  varInit,
  dialogInit,
  imgInit,
} from './Common/CustomInits.ts';
import { NavShow, NavHide, SideShow, SideHide } from './CustomElements/Nav.ts';
import { BackToTop } from './CustomElements/BackToTop.ts';
import { Gallery, GalleryClose, GalleryPrev, GalleryNext, GalleryImage, CarouselList } from './CustomElements/Gallery.ts';
import { ImageUpload } from './CustomElements/ImageUpload.ts';
import { Likedis } from './CustomElements/Likedis.ts';
import { LoginForm } from './CustomElements/LoginForm.ts';
import { OGImage } from './CustomElements/OGImage.ts';
import { PasswordShow, PasswordRequirements, PasswordStrength } from './CustomElements/Password.ts';
import { PostForm } from './CustomElements/PostForm.ts';
import { SelectCustom } from './CustomElements/SelectCustom.ts';
import { SnackbarClose } from './CustomElements/Snackbar.ts';
import { TabMenu } from './CustomElements/TabMenu.ts';
import { Timer } from './CustomElements/Timer.ts';
import { Tooltip } from './CustomElements/Tooltip.ts';
import { WebShare } from './CustomElements/WebShare.ts';

function builtInInits(): void {
//Input tags standardization
  for (const input of document.querySelectorAll('input')) {
    inputInit(input);
  }
  //Minor standardization of the textarea elements
  for (const textarea of document.querySelectorAll('textarea')) {
    textareaInit(textarea);
  }
  //Icons for new tabs
  for (const anchor of document.querySelectorAll('a')) {
    anchorInit(anchor);
  }
  //Allow copying anchor links from Heading
  for (const heading of document.querySelectorAll('h1:not(#h1_title), h2, h3, h4, h5, h6')) {
    headingInit(heading as HTMLHeadingElement);
  }
  //Customization of forms
  for (const form of document.querySelectorAll('form')) {
    formInit(form);
  }
  //Customization for details tags
  for (const details of document.querySelectorAll('details')) {
    detailsInit(details);
  }
  //Customization for code and quote blocks
  for (const samp of document.querySelectorAll('samp')) {
    sampInit(samp);
  }
  for (const code of document.querySelectorAll('code')) {
    codeInit(code);
  }
  for (const blockquote of document.querySelectorAll('blockquote')) {
    blockquoteInit(blockquote);
  }
  for (const quote of document.querySelectorAll('q')) {
    qInit(quote);
  }
  //Handle var tags
  for (const variable of document.querySelectorAll('var')) {
    varInit(variable);
  }
  //Handle dialog closure when clicking outside dialog content
  for (const dialog of document.querySelectorAll('dialog')) {
    dialogInit(dialog);
  }
  //Add images to the gallery
  for (const image of document.querySelectorAll('img')) {
    imgInit(image);
  }
}

function customElementsInits(): void {
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
  //Define the post form
  customElements.define('post-form', PostForm);
}

//Runs initialization routines
function init(): void {
  builtInInits();
  customElementsInits();
  //Create the observer to react to new elements
  const new_nodes_observer = new MutationObserver((mutations_list) => {
    for (const mutation of mutations_list) {
      for (const added_node of mutation.addedNodes) {
        customizeNewElements(added_node);
      }
    }
  });
  new_nodes_observer.observe(document, {
    attributes: false,
    characterData: false,
    childList: true,
    subtree: true,
  });
  //Process URL
  cleanGET();
  hashCheck();
  router();
}

//Stuff to do on the initial load
document.addEventListener('DOMContentLoaded', init);
window.addEventListener('hashchange', () => {
  hashCheck();
});
