//Remove certain GET parameters, to avoid them being saved to favourites or shared
function cleanGET(): void {
  const url = new URL(document.location.href);
  //Flag for resetting cache on server side
  url.searchParams.delete('cache_reset');
  //Flag used to attempt to force proper reload (with cache clear) of a page.
  url.searchParams.delete('force_reload');
  //Access token needs to be removed to minimize potential for token leak
  url.searchParams.delete('access_token');
  //window.location.reload seems to always hit browser cache, which results in, for example, page showing you as logged in/out, when in fact it's the reverse.
  //Thus, I am using direct window.location.replace with a flag instead of reload, but we need to clear the flag itself
  window.history.replaceState(document.title, document.title, url.toString());
}

//URL decode pasted links and strip some common marketing GET parameters
async function urlClean(event: ClipboardEvent): Promise<void> {
  const original_string = event.clipboardData?.getData('text/plain');
  event.preventDefault();
  event.stopImmediatePropagation();
  const current = event.target;
  if (current === null) {
    //If somehow we got here - exit early
    return;
  }
  //Update the value
  pasteAndMove((current as HTMLInputElement), urlCleanString(original_string as string));
  current.dispatchEvent(new Event('input', {
    'bubbles': true,
    'cancelable': true,
  }));
}

function urlCleanString(url: string): string {
  const params_to_delete = sharedWithPHP?.tracking_query_parameters || [];
  const url_new = new URL(url);
  for (const param of params_to_delete) {
    url_new.searchParams.delete(param);
  }
  return decodeURI(url_new.toString());
}

//Special processing for special hash links
function hashCheck(): void {
  const url = new URL(document.location.href);
  const hash = url.hash;
  const gallery = document.querySelector('gallery-overlay');
  const gallery_link = /#gallery=\d+/ui;
  if (gallery) {
    if (gallery_link.test(hash)) {
      const image_id = Number(hash.replace(/(?<hash>#gallery=)(?<number>\d+)/ui, '$<number>'));
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
}

function getSearchParam(parameter: string): string | null {
  const query_string = window.location.search;
  const params = new URLSearchParams(query_string);
  return params.get(parameter);
}

//Function to initialize page-specific code
//I do not see a good alternative for using `new` keyword for the respective objects, so that everything would still be
// limited to respective classes, so suppressing `no-new` rule for the function
function router(): void {
  /* eslint-disable no-new */
  const url = new URL(document.location.href);
  const path = url.pathname.replace(/(?<startingSlash>\/)(?<url>.*)(?<endingSlash>\/)?/ui, '$<url>')
                  .toLowerCase()
                  .split('/');
  if (!empty(path[0])) {
    if (path[0] === 'bictracker') {
      if (!empty(path[1])) {
        if (path[1] === 'keying') {
          void import('/assets/controllers/bictracker/keying.js').then((module) => {
            void new module.bicKeying();
          });
        } else if (path[1] === 'search') {
          void import('/assets/controllers/bictracker/search.js').then((module) => {
            void new module.bicRefresh();
          });
        }
      }
    } else if (path[0] === 'fftracker') {
      if (!empty(path[1])) {
        if (path[1] === 'track') {
          void import('/assets/controllers/fftracker/track.js').then((module) => {
            void new module.ffTrack();
          });
        } else if (path[1] === 'crests') {
          void import('/assets/controllers/fftracker/crests.js').then((module) => {
            void new module.ffCrests();
          });
        } else if (['characters', 'freecompanies', 'linkshells', 'crossworldlinkshells', 'crossworld_linkshells', 'pvpteams',].includes(String(path[1]))) {
          void import('/assets/controllers/fftracker/entity.js').then((module) => {
            void new module.ffEntity();
          });
        }
      }
    } else if (path[0] === 'uc') {
      if (!empty(path[1])) {
        if (path[1] === 'emails') {
          void import('/assets/controllers/uc/emails.js').then((module) => {
            void new module.Emails();
          });
        } else if (path[1] === 'password') {
          void import('/assets/controllers/uc/password.js').then((module) => {
            void new module.PasswordChange();
          });
        } else if (path[1] === 'profile') {
          void import('/assets/controllers/uc/profile.js').then((module) => {
            void new module.EditProfile();
          });
        } else if (path[1] === 'avatars') {
          void import('/assets/controllers/uc/avatars.js').then((module) => {
            void new module.EditAvatars();
          });
        } else if (path[1] === 'sessions') {
          void import('/assets/controllers/uc/sessions.js').then((module) => {
            void new module.EditSessions();
          });
        } else if (path[1] === 'fftracker') {
          void import('/assets/controllers/uc/fftracker.js').then((module) => {
            void new module.EditFFLinks();
          });
        } else if (path[1] === 'removal') {
          void import('/assets/controllers/uc/removal.js').then((module) => {
            void new module.RemoveProfile();
          });
        }
      }
    } else if (path[0] === 'talks') {
      if (path[1] === 'edit') {
        if (path[2] === 'sections') {
          void import('/assets/controllers/talks/sections.js').then((module) => {
            void new module.Sections();
          });
        } else if (path[2] === 'posts') {
          void import('/assets/controllers/talks/posts.js').then((module) => {
            void new module.Posts();
          });
        }
      } else if (path[1] === 'sections') {
        void import('/assets/controllers/talks/sections.js').then((module) => {
          void new module.Sections();
        });
      } else if (path[1] === 'threads') {
        void import('/assets/controllers/talks/threads.js').then((module) => {
          void new module.Threads();
        });
      }
    } else if (path[0] === 'games') {
      void import('/assets/controllers/games/games.js').then((module) => {
        void new module.Games();
      });
    } else if (path[0] === 'about' && path[1] === 'contacts') {
      void import('/assets/controllers/about/contacts.js').then((module) => {
        void new module.Contacts();
      });
    }
  }
}
