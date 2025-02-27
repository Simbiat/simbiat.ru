//Functions to handle closure of all details tags except the one, that we click to open
//Tags with "persistent" class is excluded, since they are meant to be open indefinitely, unless user clicks to close
//Tags with "spoiler" and "adult" classes are excluded, since once you click to reveal them the summary tag is hidden to prevent closure
function getAllDetailsTags(): NodeListOf<HTMLDetailsElement>
{
    return document.querySelectorAll('details:not(.persistent):not(.spoiler):not(.adult)');
}

function closeAllDetailsTags(target: HTMLDetailsElement): void
{
    const details = target.parentElement;
    if (details) {
        if ((details as HTMLDetailsElement).open) {
            getAllDetailsTags().forEach((tag) => {
                    if (tag !== details) {
                        tag.open = false;
                    }
                });
        }
    }
}


function clickOutsideDetailsTags(initialEvent: MouseEvent, details: HTMLDetailsElement): void
{
    if (details !== initialEvent.target && !details.contains(initialEvent.target as HTMLElement)) {
        details.open = false;
        document.removeEventListener('click', (event: MouseEvent) => {
            clickOutsideDetailsTags(event, details);
        });
    }
}

function resetDetailsTags(target: HTMLDetailsElement): void
{
    const clickedDetails = target.parentElement;
    getAllDetailsTags().forEach((details: HTMLDetailsElement) => {
            if (details.open && details !== clickedDetails && !details.contains(clickedDetails)) {
                details.open = false;
                //If target is a "popup" details, we need to be able to close it when clicking outside it
                //Unfortunately, the only viable way seems to be to start listening for clicks on whole document
            } else if (details.classList.contains('popup')) {
                document.addEventListener('click', (event: MouseEvent) => {
                    clickOutsideDetailsTags(event, details);
                });
            }
        });
}

function toggleDetailsButton(input: HTMLInputElement): void
{
    const detailsId = input.getAttribute('data-details-id');
    if (detailsId) {
        let details = document.getElementById(detailsId);
        if (details) {
            if ((details as HTMLDetailsElement).open) {
                (details as HTMLDetailsElement).open = false;
            } else {
                (details as HTMLDetailsElement).open = true;
            }
        }
    }
    //Needed to un-focus button, because otherwise when mouse is moved away it is still highlighted
    input.blur();
}