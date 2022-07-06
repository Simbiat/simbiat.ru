/*exported backToTop*/

function backToTop(event: Event): void
{
    //Check position of the scroll
    if ((event.target as HTMLElement).scrollTop === 0) {
        //Hide buttons
        document.querySelectorAll('.back-to-top').forEach(item => {
            item.classList.add('hidden');
            item.removeEventListener('click', scrollToTop);
        });
    } else {
        //Show buttons
        document.querySelectorAll('.back-to-top').forEach(item => {
            item.classList.remove('hidden');
            item.addEventListener('click', scrollToTop);
        });
    }
}

function scrollToTop(): void
{
    (document.getElementById('content') as HTMLElement).scrollTop = 0;
}
