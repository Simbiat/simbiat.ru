class PostForm extends HTMLElement
{
    private readonly textarea: HTMLTextAreaElement | null = null;
    private readonly reply_to_input: HTMLInputElement | null = null;
    private readonly label: HTMLLabelElement | null = null;

    public constructor() {
        super();
        this.textarea = this.querySelector('textarea');
        this.reply_to_input = this.querySelector('#replying_to');
        this.label = this.querySelector('.label_for_tinymce');
        if (this.textarea && !empty(this.textarea.id)) {
            loadTinyMCE(this.textarea.id, false, true);
        }
    }

    public replyTo(post_id: string): void
    {
        if (this.reply_to_input && !((/^\s*$/ui).exec(post_id))) {
            //Update value
            this.reply_to_input.value = post_id;
            if (this.label) {
                this.label.innerHTML = `Replying to post #${post_id}`;
            }
        }
        //Scroll to form
        window.location.assign(encodeURI('#post_form'));
    }
}
