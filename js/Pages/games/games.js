export class Games {
    button = null;
    wrapper = null;
    jsPath = null;
    constructor() {
        const wrapper = document.querySelector('game-canvas');
        if (wrapper) {
            this.button = wrapper.querySelector('button');
            if (this.button) {
                this.button.addEventListener('click', () => {
                    this.startGame();
                });
                this.button.addEventListener('keypress', (event) => {
                    if (['Enter', 'NumpadEnter', 'Space'].includes(event.code)) {
                        this.startGame();
                    }
                });
            }
            this.wrapper = wrapper.querySelector('#gm4html5_div_id');
            if (this.wrapper) {
                if (this.wrapper.hasAttribute('data-js')) {
                    this.jsPath = this.wrapper.getAttribute('data-js');
                }
            }
        }
    }
    startGame() {
        if (empty(this.jsPath)) {
            addSnackbar(`No GameMaker JavaScript file provided.`, 'failure');
        }
        else if (this.wrapper) {
            const canvas = document.createElement('canvas');
            canvas.id = 'canvas';
            this.wrapper.appendChild(canvas);
            const tag = document.createElement('script');
            tag.type = 'text/javascript';
            tag.src = String(this.jsPath);
            tag.onload = () => {
                canvas.classList.remove('hidden');
                GameMaker_Init();
            };
            tag.onerror = () => {
                addSnackbar(`Failed to load \`${String(this.jsPath)}\` script.`, 'failure');
            };
            document.head.appendChild(tag);
            const observer = new MutationObserver(() => {
                const { width, height } = canvas.getBoundingClientRect();
                if (width === 0 && height === 0) {
                    window.location.reload();
                }
            });
            observer.observe(canvas, {
                'attributes': true,
                'childList': true,
                'subtree': true
            });
        }
        else {
            addSnackbar(`No GameMaker canvas provided.`, 'failure');
        }
    }
}
//# sourceMappingURL=games.js.map