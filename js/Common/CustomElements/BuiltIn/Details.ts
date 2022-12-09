//Class to handle closure of all details tags except the one, that we click to open
//Tags with "persistent" class is excluded, since they are meant to be open indefinitely, unless user clicks to close
//Tags with "spoiler" and "adult" classes are excluded, since once you click to reveal them the summary tag is hidden to prevent closure
class Details
{
    public static list: HTMLDetailsElement[];
    private static _instance: null | Details = null;

    constructor()
    {
        if (Details._instance) {
            return Details._instance;
        }
        Details.list = Array.from(document.querySelectorAll('details:not(.persistent):not(.spoiler):not(.adult)'));
        //Close all details except currently selected one
        Details.list.forEach((item,_,list)=>{
            item.ontoggle = _ => {
                if (item.open) {
                    list.forEach(tag => {
                        if (tag !== item) {
                            tag.open = false;
                        }
                    });
                }
            };
        });
        //Attach listener for clicks. Technically we can (and probably should) use 'toggle', but I was not able to achieve consistent behavior with it.
        Details.list.forEach((item) => {
            item.addEventListener('click', (event) => {
                this.reset(event.target as HTMLDetailsElement)
            });
        });
        Details._instance = this;
    }

    public reset(target: HTMLDetailsElement)
    {
        Details.list.forEach((details: HTMLDetailsElement)=>{
            if (details.open && details !== target && !details.contains(target as HTMLDetailsElement)) {
                details.open = false;
            } else {
                //If target is a "popup" details, we need to be able to close it when clicking outside it
                //Unfortunately, the only viable way seems to be to start listening for clicks on whole document
                if (details.classList.contains('popup')) {
                    document.addEventListener('click', (event: MouseEvent) => {
                        this.clickOutsideDetails(event, details);
                    });
                }
            }
        });
    }

    public clickOutsideDetails(event: MouseEvent, details: HTMLDetailsElement)
    {
        if (details !== event.target && !details.contains(event.target as HTMLElement)) {
            details.open = false;
            document.removeEventListener('click', (event: MouseEvent) =>{
                this.clickOutsideDetails(event, details);
            });
        }
    }
}
