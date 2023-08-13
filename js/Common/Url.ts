//Remove certain GET parameters, to avoid them being saved to favourites or shared
function cleanGET(): void
{
    const url = new URL(document.location.href);
    //Flag for resetting cache on server side
    url.searchParams.delete('cacheReset');
    //Flag used to attempt to force proper reload (with cache clear) of a page.
    //window.location.reload seems to always hit browser cache, which results in, for example, page showing you as logged in/out, when in fact it's the reverse.
    //Thus, I am using direct window.location.replace with a flag instead of reload, but we need to clear the flag itself
    url.searchParams.delete('forceReload');
    window.history.replaceState(document.title, document.title, url.toString());
}

//Special processing for special hash links
function hashCheck(): void
{
    const url = new URL(document.location.href);
    const hash = url.hash;
    const Gallery = document.querySelector('gallery-overlay');
    const galleryLink = /#gallery=\d+/ui;
    if (Gallery) {
        if (galleryLink.test(hash)) {
            const imageID = Number(hash.replace(/(?<hash>#gallery=)(?<number>\d+)/ui, '$<number>'));
            if (imageID) {
                if ((Gallery as Gallery).images[imageID - 1]) {
                    (Gallery as Gallery).current = imageID - 1;
                } else {
                    addSnackbar(`Image number ${imageID} not found on page`, 'failure');
                    window.history.replaceState(document.title, document.title, document.location.href.replace(hash, ''));
                }
            }
        } else {
            (Gallery as Gallery).close();
        }
    }
}

//Function to initialize page-specific code
//I do not see a good alternative for using `new` keyword for the respective objects, so that everything would still be
// limited to respective classes, so suppressing `no-new` rule for the function
function router(): void
{
    /* eslint-disable no-new */
    const url = new URL(document.location.href);
    const path = url.pathname.replace(/(?<startingSlash>\/)(?<url>.*)(?<endingSlash>\/)?/ui, '$<url>').
                                toLowerCase().
                                split('/');
    if (!empty(path[0])) {
        if (path[0] === 'bictracker') {
            if (!empty(path[1])) {
                if (path[1] === 'keying') {
                    void import('/js/Pages/bictracker/keying.js').then((module) => { new module.bicKeying(); });
                } else if (path[1] === 'search') {
                    void import('/js/Pages/bictracker/search.js').then((module) => { new module.bicRefresh(); });
                }
            }
        } else if (path[0] === 'fftracker') {
            if (!empty(path[1])) {
                if (path[1] === 'track') {
                    void import('/js/Pages/fftracker/track.js').then((module) => { new module.ffTrack(); });
                } else if (['characters', 'freecompanies', 'linkshells', 'crossworldlinkshells', 'crossworld_linkshells', 'pvpteams',].includes(String(path[1]))) {
                    void import('/js/Pages/fftracker/entity.js').then((module) => { new module.ffEntity(); });
                }
            }
        } else if (path[0] === 'uc') {
            if (!empty(path[1])) {
                if (path[1] === 'emails') {
                    void import('/js/Pages/uc/emails.js').then((module) => { new module.Emails(); });
                } else if (path[1] === 'password') {
                    void import('/js/Pages/uc/password.js').then((module) => { new module.PasswordChange(); });
                } else if (path[1] === 'profile') {
                    void import('/js/Pages/uc/profile.js').then((module) => { new module.EditProfile(); });
                } else if (path[1] === 'avatars') {
                    void import('/js/Pages/uc/avatars.js').then((module) => { new module.EditAvatars(); });
                } else if (path[1] === 'sessions') {
                    void import('/js/Pages/uc/sessions.js').then((module) => { new module.EditSessions(); });
                } else if (path[1] === 'fftracker') {
                    void import('/js/Pages/uc/fftracker.js').then((module) => { new module.EditFFLinks(); });
                } else if (path[1] === 'removal') {
                    void import('/js/Pages/uc/removal.js').then((module) => { new module.RemoveProfile(); });
                }
            }
        } else if (path[0] === 'talks') {
            if (path[1] === 'edit') {
                if (path[2] === 'sections') {
                    void import('/js/Pages/talks/sections.js').then((module) => { new module.Sections(); });
                } else if (path[2] === 'posts') {
                    void import('/js/Pages/talks/posts.js').then((module) => { new module.Posts(); });
                }
            } else if (path[1] === 'sections') {
                void import('/js/Pages/talks/sections.js').then((module) => { new module.Sections(); });
            } else if (path[1] === 'threads') {
                void import('/js/Pages/talks/threads.js').then((module) => { new module.Threads(); });
            }
        } else if (path[0] === 'games') {
            void import('/js/Pages/games/games.js').then((module) => { new module.Games(); });
        }
    }
}
