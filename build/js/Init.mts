/**
 * @file Initialization of the frontend.
 */
import { Sanitize } from 'Common/Sanitize.mts';
import DOMPurify from 'dompurify';
import { cleanGET, hashCheck } from './Common/Helpers.mts';
import { BackToTop } from './CustomElements/BackToTop.mts';
import { CarouselList } from './CustomElements/CarouselList.mts';
import { GalleryOverlay } from './CustomElements/GalleryOverlay.mts';
import { ImageUpload } from './CustomElements/ImageUpload.mts';
import { LikeDis } from './CustomElements/LikeDis.mts';
import { LoginForm } from './CustomElements/LoginForm.mts';
import { NavHide } from './CustomElements/NavHide.mts';
import { NavShow } from './CustomElements/NavShow.mts';
import { OgImage } from './CustomElements/OgImage.mts';
import { PasswordField } from './CustomElements/PasswordField.mts';
import { PostForm } from './CustomElements/PostForm.mts';
import { SelectCustom } from './CustomElements/SelectCustom.mts';
import { SideHide } from './CustomElements/SideHide.mts';
import { SideShow } from './CustomElements/SideShow.mts';
import { TabMenu } from './CustomElements/TabMenu.mts';
import { TimeR } from './CustomElements/TimeR.mts';
import { ToolTip } from './CustomElements/ToolTip.mts';
import { WebShare } from './CustomElements/WebShare.mts';
import { Anchor } from './NativeElements/Anchor.mts';
import { Button } from './NativeElements/Button.mts';
import { CodeQuote } from './NativeElements/CodeQuote.mts';
import { Details } from './NativeElements/Details.mts';
import { Dialog } from './NativeElements/Dialog.mts';
import { Form } from './NativeElements/Form.mts';
import { Heading } from './NativeElements/Heading.mts';
import { Image } from './NativeElements/Img.mts';
import { Input } from './NativeElements/Input.mts';
import { Textarea } from './NativeElements/Textarea.mts';
import { Contacts } from './Pages/about/Contacts.mts';
import { Keying } from './Pages/bictracker/Keying.mts';
import { Refresh } from './Pages/bictracker/Refresh.mts';
import { Crests } from './Pages/fftracker/Crests.mts';
import { Entity } from './Pages/fftracker/Entity.mts';
import { Track } from './Pages/fftracker/Track.mts';
import { Games } from './Pages/games/Games.mts';
import { Posts } from './Pages/talks/Posts.mts';
import { Sections } from './Pages/talks/Sections.mts';
import { Threads } from './Pages/talks/Threads.mts';
import { EditAvatars } from './Pages/uc/Avatars.mts';
import { EditFFLinks } from './Pages/uc/EditFFLinks.mts';
import { EditProfile } from './Pages/uc/EditProfile.mts';
import { EditSessions } from './Pages/uc/EditSessions.mts';
import { Emails } from './Pages/uc/Emails.mts';
import { PasswordChange } from './Pages/uc/PasswordChange.mts';
import { RemoveProfile } from './Pages/uc/RemoveProfile.mts';

/**
 * Function to apply custom initializers from the observer.
 * @param new_node - New node that is being added.
 */
function customizeNewElements(new_node: Node): void {
  if (new_node.nodeType === 1) {
    const node_name = new_node.nodeName.toLowerCase();
    switch (node_name) {
      case 'a':
        Anchor.init(new_node as HTMLAnchorElement);
        break;
      case 'blockquote':
        CodeQuote.blockquote(new_node as HTMLElement);
        break;
      case 'button':
        Button.init(new_node as HTMLButtonElement);
        break;
      case 'code':
        CodeQuote.code(new_node as HTMLElement);
        break;
      case 'details':
        Details.init(new_node as HTMLDetailsElement);
        break;
      case 'dialog':
        Dialog.init(new_node as HTMLDialogElement);
        break;
      case 'form':
        Form.init(new_node as HTMLFormElement);
        break;
      case 'h1':
      case 'h2':
      case 'h3':
      case 'h4':
      case 'h5':
      case 'h6':
        Heading.init(new_node as HTMLHeadingElement);
        break;
      case 'img':
        Image.init(new_node as HTMLImageElement);
        break;
      case 'input':
        Input.init(new_node as HTMLInputElement);
        break;
      case 'q':
        CodeQuote.quote(new_node as HTMLQuoteElement);
        break;
      case 'var':
        CodeQuote.var(new_node as HTMLElement);
        break;
      case 'samp':
        CodeQuote.samp(new_node as HTMLElement);
        break;
      case 'textarea':
        Textarea.init(new_node as HTMLTextAreaElement);
        break;
      default:
        // Do nothing
        break;
    }
  }
}

/**
 * Initialize Web Components.
 */
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
  customElements.define('time-r', TimeR);
  //Web-share button
  customElements.define('web-share', WebShare);
  //Gallery overlay
  customElements.define('gallery-overlay', GalleryOverlay);
  //Define image carousels
  customElements.define('image-carousel', CarouselList);
  //Define OG Image
  customElements.define('og-image', OgImage);
  //Define password field
  customElements.define('password-field', PasswordField);
  //Define block for (dis)likes
  customElements.define('like-dis', LikeDis);
  //Define vertical tabs
  customElements.define('tab-menu', TabMenu);
  //Define image upload blocks
  customElements.define('image-upload', ImageUpload);
  //Define custom select blocks
  customElements.define('select-custom', SelectCustom);
  //Define the post form
  customElements.define('post-form', PostForm);
  //Floating tooltip
  customElements.define('tool-tip', ToolTip);
}

/**
 *Function to initialize page-specific code.
 */
function router(): void {
  const url = new URL(document.location.href, window.location.origin);
  const path = url.pathname.replace(/^\/+/v, '')
                  .toLowerCase()
                  .split('/');
  switch (path[0]) {
    case 'bictracker':
      if (path[1] === 'keying') {
        void new Keying();
      } else if (path[1] === 'search') {
        void new Refresh();
      }
      break;
    case 'fftracker':
      if (path[1] === 'track') {
        void new Track();
      } else if (path[1] === 'crests') {
        void new Crests();
      } else if (path[1] === 'search') {
        Form.searchForm();
      } else if (['characters', 'freecompanies', 'linkshells', 'crossworldlinkshells', 'crossworld_linkshells', 'pvpteams', 'achievements'].includes(String(path[1]))) {
        void new Entity();
      }
      break;
    case 'uc':
      if (path[1] === 'emails') {
        void new Emails();
      } else if (path[1] === 'password') {
        void new PasswordChange();
      } else if (path[1] === 'profile') {
        void new EditProfile();
      } else if (path[1] === 'avatars') {
        void new EditAvatars();
      } else if (path[1] === 'sessions') {
        void new EditSessions();
      } else if (path[1] === 'fftracker') {
        void new EditFFLinks();
      } else if (path[1] === 'removal') {
        void new RemoveProfile();
      }
      break;
    case 'talks':
      if (path[1] === 'sections') {
        void new Sections();
      } else if (path[1] === 'threads') {
        void new Threads();
      } else if (path[1] === 'posts') {
        void new Posts();
      }
      break;
    case 'games':
      void new Games();
      break;
    case 'about':
      if (path[1] === 'contacts') {
        void new Contacts();
      }
      break;
    default:
      break;
  }
}

/**
 * Runs initialization routines.
 */
function globalInit(): void {
  if (typeof window.trustedTypes !== 'undefined') {
    window.trustedTypes.createPolicy('default', {
      /**
       * Allow only TinyMCE scripts.
       * @param url - URL to process.
       * @throws {Error}
       */
      createScriptURL: (url: string): string => {
        const allowed = ['tinymce/', '/tinymce/'];
        // Normalize: get a pathname if it's an absolute URL
        let path = url;
        try {
          path = new URL(url, window.location.origin).pathname;
        } catch {
          // not a valid absolute URL, treat as-is
        }
        if (allowed.some((prefix) => {
          return path.startsWith(`${window.location.origin}${prefix}`);
        })) {
          return url;
        }
        throw new Error(`[TrustedTypes] Blocked script URL: ${url}`);
      },
      /**
       * Pass raw HTML through DOMPurify rather than blocking it.
       * @param html - HTML to process.
       */
      createHTML: (html: string): string => {
        return DOMPurify.sanitize(html, Sanitize.PURIFY_CONFIG);
      },
      /**
       * Block arbitrary script strings entirely.
       * @param _script - Script to process.
       * @throws {Error}
       */
      createScript: (_script: string): string => {
        throw new Error('[TrustedTypes] Blocked dynamic script evaluation');
      },
    });
  }
  // Customize native elements already in DOM
  for (const element of document.querySelectorAll<HTMLElement>('input, textarea, a, h1:not(#h1_title), h2, h3, h4, h5, h6, form, details, samp, code, blockquote, q, var, dialog, img, button')) {
    customizeNewElements(element);
  }
  customElementsInits();
  //Add tabindex to elements with a data-tooltip attribute, if missing
  for (const item of document.querySelectorAll<HTMLElement>('[data-tooltip]:not(dialog):not([tabindex])')) {
    item.setAttribute('tabindex', '0');
  }
  // Create the observer to react to new elements
  const new_nodes_observer = new MutationObserver((mutations_list) => {
    for (const mutation of mutations_list) {
      for (const added_node of mutation.addedNodes) {
        customizeNewElements(added_node);
        if (added_node instanceof Element) {
          for (const descendant of added_node.querySelectorAll('*')) {
            customizeNewElements(descendant);
          }
        }
      }
    }
  });
  new_nodes_observer.observe(document, {
    attributes: false,
    characterData: false,
    childList: true,
    subtree: true,
  });
  // Process URL
  cleanGET();
  hashCheck();
  router();
}

// Stuff to do on the initial load
document.addEventListener('DOMContentLoaded', globalInit);
window.addEventListener('hashchange', () => {
  hashCheck();
});
