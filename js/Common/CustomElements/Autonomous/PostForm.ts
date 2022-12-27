class PostForm extends HTMLElement
{
    private readonly textarea: HTMLTextAreaElement | null = null
    private readonly replyToInput: HTMLInputElement | null = null;
    private readonly label: HTMLLabelElement | null = null;
    
    constructor() {
        super();
        this.textarea = this.querySelector('textarea');
        this.replyToInput = this.querySelector('#replyingTo');
        this.label = this.querySelector('.label_for_tinymce');
        if (this.textarea && this.textarea.id) {
            loadTinyMCE(this.textarea.id, true, true);
        }
    }
    
    public replyTo(postId: string)
    {
        if (this.replyToInput && !postId.match(/^\s*$/ui)) {
            //Update value
            this.replyToInput.value = postId;
            if (this.label) {
                this.label.innerHTML = 'Replying to post #'+postId;
            }
        }
        //Scroll to form
        window.location.href = '#postForm';
    }
}
