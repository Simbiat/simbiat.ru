class Details
{
    public static list: HTMLDetailsElement[];
    private static _instance: null | Details = null;

    constructor()
    {
        if (Details._instance) {
            return Details._instance;
        }
        Details.list = Array.from(document.getElementsByTagName('details'));
        //Close all details except currently selected one
        Details.list.forEach((item,_,list)=>{
            item.ontoggle =_=> {
                if(item.open && !item.classList.contains('persistent')) {
                    list.forEach(tag =>{
                        if(tag !== item && !tag.classList.contains('persistent')) {
                            tag.open=false;
                        }
                    });
                }
            };
        });
        //Attach listener for clicks. Technically we can (and probably should) use 'toggle', but I was not able to achieve consistent behavior with it.
        Details.list.forEach((item) => {
            item.addEventListener('click', (event) => {this.reset(event.target as HTMLDetailsElement)});
        });
        Details._instance = this;
    }

    public reset(target: HTMLDetailsElement)
    {
        Details.list.forEach((details: HTMLDetailsElement)=>{
            if(details.open && details !== target && !details.contains(target as HTMLDetailsElement)) {
                details.open=false;
            }
        });
    }
}
