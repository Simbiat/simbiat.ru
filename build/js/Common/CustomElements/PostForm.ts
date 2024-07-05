class PostForm extends HTMLElement
{
    private readonly textarea: HTMLTextAreaElement | null = null;
    private readonly replyToInput: HTMLInputElement | null = null;
    private readonly label: HTMLLabelElement | null = null;
    
    public constructor() {
        super();
        this.textarea = this.querySelector('textarea');
        this.replyToInput = this.querySelector('#replyingTo');
        this.label = this.querySelector('.label_for_tinymce');
        if (this.textarea && !empty(this.textarea.id)) {
            loadTinyMCE(this.textarea.id, false, true);
        }
    }
    
    public replyTo(postId: string): void
    {
        if (this.replyToInput && !((/^\s*$/ui).exec(postId))) {
            //Update value
            this.replyToInput.value = postId;
            if (this.label) {
                this.label.innerHTML = `Replying to post #${postId}`;
            }
        }
        //Scroll to form
        window.location.href = '#postForm';
    }
}
