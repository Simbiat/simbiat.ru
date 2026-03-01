import { empty } from './Helpers.ts';

//Functions to handle closure of all details tags except the one, that we click to open
//Tags with "persistent" class is excluded, since they are meant to be open indefinitely, unless user clicks to close
//Tags with "spoiler" and "adult" classes are excluded, since once you click to reveal them the summary tag is hidden to prevent closure
export function getAllDetailsTags(): NodeListOf<HTMLDetailsElement> {
  return document.querySelectorAll('details:not(.persistent):not(.spoiler):not(.adult)');
}

export function closeAllDetailsTags(target: HTMLDetailsElement): void {
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

export function clickOutsideDetailsTags(initialEvent: MouseEvent, details: HTMLDetailsElement): void {
  if (details !== initialEvent.target && !details.contains(initialEvent.target as HTMLElement)) {
    details.open = false;
    document.removeEventListener('click', (event: MouseEvent) => {
      clickOutsideDetailsTags(event, details);
    });
  }
}

export function resetDetailsTags(target: HTMLDetailsElement): void {
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

export function toggleDetailsButton(input: HTMLInputElement): void {
  const details_id = input.getAttribute('data-details-id');
  if (details_id !== null && !empty(details_id)) {
    const details = document.getElementById(details_id);
    if (details) {
      (details as HTMLDetailsElement).open = !(details as HTMLDetailsElement).open;
    }
  }
  //Needed to unfocus button, because otherwise when mouse is moved away it is still highlighted
  input.blur();
}