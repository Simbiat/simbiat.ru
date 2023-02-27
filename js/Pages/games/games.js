export class Games {
    button = null;
    canvas = null;
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
            const gmDiv = wrapper.querySelector('#gm4html5_div_id');
            if (gmDiv) {
                this.canvas = gmDiv.querySelector('#canvas');
                if (gmDiv.hasAttribute('data-js')) {
                    this.jsPath = gmDiv.getAttribute('data-js');
                }
            }
        }
    }
    startGame() {
        if (empty(this.jsPath)) {
            addSnackbar(`No GameMaker JavaScript file provided.`, 'failure');
        }
        else if (this.canvas) {
            const tag = document.createElement('script');
            tag.type = 'text/javascript';
            tag.src = String(this.jsPath);
            tag.onload = () => {
                this.canvas?.classList.remove('hidden');
                GameMaker_Init();
            };
            tag.onerror = () => {
                addSnackbar(`Failed to load \`${String(this.jsPath)}\` script.`, 'failure');
            };
            document.head.appendChild(tag);
        }
        else {
            addSnackbar(`No GameMaker canvas provided.`, 'failure');
        }
    }
}
//# sourceMappingURL=games.js.map