class Likedis extends HTMLElement
{
    private readonly post_id: number = 0;
    private like_value = 0;
    private readonly likesCount: HTMLSpanElement | null;
    private readonly dislikesCount: HTMLSpanElement | null;
    private readonly likeButton: HTMLInputElement | null;
    private readonly dislikeButton: HTMLInputElement | null;
    
    public constructor()
    {
        super();
        //Set initial values for the object
        this.like_value = Number(this.getAttribute('data-liked') ?? 0);
        this.post_id = Number(this.getAttribute('data-post_id') ?? 0);
        this.likesCount = this.querySelector('.likes_count');
        this.dislikesCount = this.querySelector('.dislikes_count');
        this.likeButton = this.querySelector('.like_button');
        this.dislikeButton = this.querySelector('.dislike_button');
        //Attach listeners
        if (this.likeButton) {
            this.likeButton.addEventListener('click', this.like.bind(this));
        }
        if (this.dislikeButton) {
            this.dislikeButton.addEventListener('click', this.like.bind(this));
        }
    }
    
    //Function to update like/dislike on backend
    private like(event: Event): void
    {
        const button = event.target as HTMLInputElement;
        let action: string;
        if (button.classList.contains('like_button')) {
            action = 'like';
        } else {
            action = 'dislike';
        }
        if (this.post_id === 0) {
            addSnackbar('No post ID', 'failure', SNACKBAR_FAIL_LIFE);
            return;
        }
        buttonToggle(button);
        ajax(`${location.protocol}//${location.host}/api/talks/posts/${this.post_id}/${action}`, null, 'json', 'PATCH', AJAX_TIMEOUT, true)
            .then((response) => {
                const data = response as ajaxJSONResponse;
                if (data.data === 0 || data.data === 1 || data.data === -1) {
                    this.updateCounts(data.data);
                } else {
                    addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                }
                buttonToggle(button);
            });
    }
    
    //Function to update counts and styling in UI
    private updateCounts(newValue: number): void
    {
        if (this.likesCount && this.dislikesCount && this.likeButton && this.dislikeButton) {
            //Remove styling
            this.likesCount.classList.remove('success');
            this.dislikesCount.classList.remove('failure');
            if (newValue === 0) {
                //Update values depending on previous ones
                if (this.like_value === 1 || this.like_value === -1) {
                    this.likesCount.innerHTML = String(Number(this.likesCount.innerHTML) + this.like_value);
                }
                //Update tooltips
                this.likeButton.setAttribute('data-tooltip', 'Like');
                this.dislikeButton.setAttribute('data-tooltip', 'Dislike');
            } else if (newValue === 1) {
                //Reduce dislikes
                if (this.like_value === -1) {
                    this.dislikesCount.innerHTML = String(Number(this.dislikesCount.innerHTML) - 1);
                }
                //Increase likes
                this.likesCount.innerHTML = String(Number(this.likesCount.innerHTML) + 1);
                //Style the span
                this.likesCount.classList.add('success');
                //Update tooltips
                this.likeButton.setAttribute('data-tooltip', 'Remove like');
                this.dislikeButton.setAttribute('data-tooltip', 'Dislike');
            } else if (newValue === -1) {
                //Reduce likes
                if (this.like_value === 1) {
                    this.likesCount.innerHTML = String(Number(this.likesCount.innerHTML) - 1);
                }
                //Increase dislikes
                this.dislikesCount.innerHTML = String(Number(this.dislikesCount.innerHTML) + 1);
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
            this.like_value = newValue;
        }
    }
}
