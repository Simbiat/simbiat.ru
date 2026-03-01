import shared_with_php from 'shared_with_php.json';
import { pasteAndMove } from './Inputs.ts';
import { addSnackbar, empty } from './Helpers.ts';
import { BICKeying } from 'bictracker/BICKeying.ts';
import { BICRefresh } from 'bictracker/BICRefresh.ts';
import { Contacts } from 'about/Contacts.ts';
import { EditAvatars } from 'uc/Avatars.ts';
import { EditFFLinks } from 'uc/EditFFLinks.ts';
import { EditProfile } from 'uc/EditProfile.ts';
import { EditSessions } from 'uc/EditSessions.ts';
import { Emails } from 'uc/Emails.ts';
import { Games } from 'games/Games.ts';
import { PasswordChange } from 'uc/PasswordChange.ts';
import { RemoveProfile } from 'uc/RemoveProfile.ts';
import { FFCrests } from 'fftracker/FFCrests.ts';
import { FFEntity } from 'fftracker/FFEntity.ts';
import { FFTrack } from 'fftracker/FFTrack.ts';
import { Sections } from 'talks/Sections.ts';
import { Threads } from 'talks/Threads.ts';
import { Posts } from 'talks/Posts.ts';
import { TabMenu } from 'CustomElements/TabMenu.ts';
import { Gallery } from 'CustomElements/Gallery.ts';

//Remove certain GET parameters to avoid them being saved to favourites or shared
export function cleanGET(): void {
  const url = new URL(document.location.href, window.location.origin);
  //Flag for resetting cache on the server side
  url.searchParams.delete('cache_reset');
  //Flag used to attempt to force proper reload (with cache clear) of a page.
  url.searchParams.delete('force_reload');
  //Access token needs to be removed to minimize the potential for token leak
  url.searchParams.delete('access_token');
  //window.location.reload seems to always hit the browser cache, which results in, for example, a page showing you as logged in/out, when in fact it's the reverse.
  //Thus, I am using direct window.location.replace with a flag instead of reload, but we need to clear the flag itself
  window.history.replaceState(document.title, document.title, url.toString());
}

//URL decode pasted links and strip some common marketing GET parameters
export async function urlClean(event: ClipboardEvent): Promise<void> {
  const original_string = event.clipboardData?.getData('text/plain');
  event.preventDefault();
  event.stopImmediatePropagation();
  const current = event.target;
  if (current === null) {
    //If somehow we got here - exit early
    return;
  }
  //Update the value
  pasteAndMove((current as HTMLInputElement), urlCleanString(original_string!));
  current.dispatchEvent(new Event('input', {
    bubbles: true,
    cancelable: true,
  }));
}

export function urlCleanString(url: string): string {
  const params_to_delete = shared_with_php?.tracking_query_parameters || [];
  const url_new = new URL(url, window.location.origin);
  for (const param of params_to_delete) {
    url_new.searchParams.delete(param);
  }
  return decodeURI(url_new.toString());
}

//Special processing for special hash links
export function hashCheck(): void {
  const url = new URL(document.location.href, window.location.origin);
  const hash = url.hash;
  const gallery = document.querySelector('gallery-overlay');
  const gallery_link = /#gallery=\d+/iv;
  const tab_name = /#tab_name_.+/iv;
  if (gallery) {
    if (gallery_link.test(hash)) {
      const image_id = Number(hash.replace(/#gallery=(?=\d+)/iv, ''));
      if (image_id) {
        if ((gallery as Gallery).images[image_id - 1]) {
          (gallery as Gallery).current = image_id - 1;
        } else {
          addSnackbar(`Image number ${image_id} not found on page`, 'failure');
          window.history.replaceState(document.title, document.title, document.location.href.replace(hash, ''));
        }
      }
    } else {
      (gallery as Gallery).close();
    }
  }
  if (tab_name.test(hash)) {
    const tab_name_id = hash.replace(/#(?=tab_name_.+)/iv, '');
    const tab_element = document.getElementById(tab_name_id);
    if (tab_element?.tagName.toLowerCase() === 'a') {
      //Open respective tab
      const tab_menu = tab_element.parentElement;
      if (tab_menu?.tagName.toLowerCase() === 'tab-menu') {
        (tab_menu as TabMenu).tabSwitch(tab_element as HTMLAnchorElement);
      }
    } else {
      url.hash = '';
      window.history.replaceState(document.title, document.title, url.toString());
    }
  }
}

export function getSearchParam(parameter: string): string | null {
  const query_string = window.location.search;
  const params = new URLSearchParams(query_string);
  return params.get(parameter);
}

//Function to initialize page-specific code
//I do not see a good alternative for using `new` keyword for the respective objects, so that everything would still be
// limited to respective classes, so suppressing the `no-new` rule for the function
export function router(): void {
  const url = new URL(document.location.href);
  const path = url.pathname.replace(/(?<startingSlash>\/)(?<url>.*)(?<endingSlash>\/)?/v, '$<url>')
                  .toLowerCase()
                  .split('/');
  if (!empty(path[0])) {
    if (path[0] === 'bictracker') {
      if (!empty(path[1])) {
        if (path[1] === 'keying') {
          new BICKeying();
        } else if (path[1] === 'search') {
          new BICRefresh();
        }
      }
    } else if (path[0] === 'fftracker') {
      if (!empty(path[1])) {
        if (path[1] === 'track') {
          new FFTrack();
        } else if (path[1] === 'crests') {
          new FFCrests();
        } else if (['characters', 'freecompanies', 'linkshells', 'crossworldlinkshells', 'crossworld_linkshells', 'pvpteams', 'achievements'].includes(String(path[1]))) {
          new FFEntity();
        }
      }
    } else if (path[0] === 'uc') {
      if (!empty(path[1])) {
        if (path[1] === 'emails') {
          new Emails();
        } else if (path[1] === 'password') {
          new PasswordChange();
        } else if (path[1] === 'profile') {
          new EditProfile();
        } else if (path[1] === 'avatars') {
          new EditAvatars();
        } else if (path[1] === 'sessions') {
          new EditSessions();
        } else if (path[1] === 'fftracker') {
          new EditFFLinks();
        } else if (path[1] === 'removal') {
          new RemoveProfile();
        }
      }
    } else if (path[0] === 'talks') {
      if (path[1] === 'sections') {
        new Sections();
      } else if (path[1] === 'threads') {
        new Threads();
      } else if (path[1] === 'posts') {
        new Posts();
      }
    } else if (path[0] === 'games') {
      new Games();
    } else if (path[0] === 'about' && path[1] === 'contacts') {
      new Contacts();
    }
  }
}
