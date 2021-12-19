/*exported backToTop*/

function backToTop(event) {
    //Check position of the scroll
    if (event.target.scrollTop === 0) {
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
function scrollToTop() {
    document.getElementById('content').scrollTop = 0;
}
