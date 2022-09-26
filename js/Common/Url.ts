//Remove cacheReset flag
function cleanGET(): void
{
    let url = new URL(document.location.href);
    let params = new URLSearchParams(url.search);
    params.delete('cacheReset');
    if (params.toString() === '') {
        window.history.replaceState(document.title, document.title, location.pathname + location.hash);
    } else {
        window.history.replaceState(document.title, document.title, '?' + params + location.hash);
    }
}

//Special processing for special hash links
function hashCheck(): void
{
    let url = new URL(document.location.href);
    let hash = url.hash;
    let Gallery = document.getElementsByTagName('gallery-overlay')[0] as Gallery;
    const galleryLink = new RegExp('#gallery=\\d+', 'ui');
    if (Gallery) {
        if (galleryLink.test(hash)) {
            let imageID = Number(hash.replace(/(#gallery=)(\d+)/ui, '$2'));
            if (imageID) {
                if (Gallery.images[imageID - 1]) {
                    Gallery.current = imageID - 1;
                } else {
                    new Snackbar('Image number ' + imageID + ' not found on page', 'failure');
                    window.history.replaceState(document.title, document.title, document.location.href.replace(hash, ''));
                }
            }
        } else {
            Gallery.close();
        }
    }
}

//Function to initialize page-specific code
function router(): void
{
    let url = new URL(document.location.href);
    let path = url.pathname.replace(/(\/)(.*)(\/)?/ui, '$2').toLowerCase().split('/');
    if (path[0]) {
        if (path[0] === 'bictracker') {
            if (path[1]) {
                if (path[1] === 'keying') {
                    import('/js/Pages/bictracker/keying.js').then((module) => {new module.bicKeying();});
                } else if (path[1] === 'search') {
                    import('/js/Pages/bictracker/search.js').then((module) => {new module.bicRefresh();});
                }
            }
        } else if (path[0] === 'fftracker') {
            if (path[1]) {
                if (path[1] === 'track') {
                    import('/js/Pages/fftracker/track.js').then((module) => {new module.ffTrack();});
                } else if (['character', 'freecompany', 'linkshell', 'crossworldlinkshell', 'pvpteam',].includes(path[1])) {
                    import('/js/Pages/fftracker/entity.js').then((module) => {new module.ffEntity();});
                }
            }
        } else if (path[0] === 'uc') {
            if (path[1]) {
                if (path[1] === 'emails') {
                    import('/js/Pages/uc/emails.js').then((module) => {new module.Emails();});
                } else if (path[1] === 'password') {
                    import('/js/Pages/uc/password.js').then((module) => {new module.PasswordChange();});
                } else if (path[1] === 'profile') {
                    import('/js/Pages/uc/profile.js').then((module) => {new module.EditProfile();});
                } else if (path[1] === 'avatars') {
                    import('/js/Pages/uc/avatars.js').then((module) => {new module.EditAvatars();});
                } else if (path[1] === 'sessions') {
                    import('/js/Pages/uc/sessions.js').then((module) => {new module.EditSessions();});
                } else if (path[1] === 'fftracker') {
                    import('/js/Pages/uc/fftracker.js').then((module) => {new module.EditFFLinks();});
                } else if (path[1] === 'removal') {
                    import('/js/Pages/uc/removal.js').then((module) => {new module.RemoveProfile();});
                }
            }
        }
    }
}
