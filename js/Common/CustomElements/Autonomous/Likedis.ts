class Likedis extends HTMLElement
{
    private readonly postId: number = 0;
    private likeValue: number = 0;
    private likesCount: HTMLSpanElement;
    private dislikesCount: HTMLSpanElement;
    private likeButton: HTMLInputElement;
    private dislikeButton: HTMLInputElement;
    
    constructor() {
        super();
        //Set initial values for the object
        this.likeValue = Number(this.getAttribute('data-liked') ?? 0);
        this.postId = Number(this.getAttribute('data-postid') ?? 0);
        this.likesCount = (this.querySelector('.likes_count') as HTMLSpanElement);
        this.dislikesCount = (this.querySelector('.dislikes_count') as HTMLSpanElement);
        this.likeButton = (this.querySelector('.like_button') as HTMLInputElement);
        this.dislikeButton = (this.querySelector('.dislike_button') as HTMLInputElement);
        //Attach listeners
        this.likeButton.addEventListener('click', this.like.bind(this));
        this.dislikeButton.addEventListener('click', this.like.bind(this));
    }
    
    //Function to update like/dislike on backend
    private like(event: Event)
    {
        let button = event.target as HTMLInputElement;
        let action: string;
        if (button.classList.contains('like_button')) {
            action = 'like';
        } else {
            action = 'dislike';
        }
        if (this.postId === 0) {
            new Snackbar('No post ID', 'failure', 10000);
            return;
        }
        buttonToggle(button as HTMLInputElement);
        ajax(location.protocol+'//'+location.host+'/api/talks/posts/'+this.postId+'/'+action+'/', null, 'json', 'PUT', 60000, true).then(data => {
            if (data.data === 0) {
                this.updateCounts(data.data);
            } else if (data.data === 1) {
                this.updateCounts(data.data);
            } else if (data.data === -1) {
                this.updateCounts(data.data);
            } else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(button as HTMLInputElement);
        });
    }
    
    //Function to update counts and styling in UI
    private updateCounts(newValue: number)
    {
        //Remove styling
        this.likesCount.classList.remove('success');
        this.dislikesCount.classList.remove('failure');
        if (newValue === 0) {
            //Update values depending on previous ones
            if (this.likeValue === 1) {
                this.likesCount.innerHTML = String(Number(this.likesCount.innerHTML) - 1)
            } else if (this.likeValue === -1) {
                this.dislikesCount.innerHTML = String(Number(this.dislikesCount.innerHTML) - 1)
            }
            //Update tooltips
            this.likeButton.setAttribute('data-tooltip', 'Like');
            this.dislikeButton.setAttribute('data-tooltip', 'Dislike');
        } else if (newValue === 1) {
            //Reduce dislikes
            if (this.likeValue === -1) {
                this.dislikesCount.innerHTML = String(Number(this.dislikesCount.innerHTML) - 1)
            }
            //Increase likes
            this.likesCount.innerHTML = String(Number(this.likesCount.innerHTML) + 1)
            //Style the span
            this.likesCount.classList.add('success');
            //Update tooltips
            this.likeButton.setAttribute('data-tooltip', 'Remove like');
            this.dislikeButton.setAttribute('data-tooltip', 'Dislike');
        } else if (newValue === -1) {
            //Reduce likes
            if (this.likeValue === 1) {
                this.likesCount.innerHTML = String(Number(this.likesCount.innerHTML) - 1)
            }
            //Increase dislikes
            this.dislikesCount.innerHTML = String(Number(this.dislikesCount.innerHTML) + 1)
            //Style the span
            this.dislikesCount.classList.add('failure');
            //Update tooltips
            this.likeButton.setAttribute('data-tooltip', 'Like');
            this.dislikeButton.setAttribute('data-tooltip', 'Remove dislike');
        }
        //Sanitize counts, just in case
        if (Number(this.likesCount.innerHTML) < 0) {
            this.likesCount.innerHTML = '0';
        }
        if (Number(this.dislikesCount.innerHTML) < 0) {
            this.dislikesCount.innerHTML = '0';
        }
        //Update pre-saved value of the (dis)like
        this.setAttribute('data-liked', String(newValue));
        this.likeValue = newValue;
    }
}
