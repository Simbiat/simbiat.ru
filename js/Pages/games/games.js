export class Games {
    button = null;
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
            if ((gmDiv?.hasAttribute('data-js')) === true) {
                this.jsPath = gmDiv.getAttribute('data-js');
            }
        }
    }
    startGame() {
        if (!empty(this.jsPath)) {
            const tag = document.createElement('script');
            tag.type = 'text/javascript';
            tag.src = String(this.jsPath);
            tag.onload = () => {
                GameMaker_Init();
            };
            tag.onerror = () => {
                addSnackbar(`Failed to load \`${String(this.jsPath)}\` script.`, 'failure');
            };
            document.head.appendChild(tag);
        }
        else {
            addSnackbar(`No GameMaker JavaScript file provided.`, 'failure');
        }
    }
}
//# sourceMappingURL=games.js.map