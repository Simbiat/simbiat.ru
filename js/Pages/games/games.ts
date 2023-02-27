export class Games {
    //window.onload = GameMaker_Init;
    private readonly button: HTMLButtonElement | null = null;
    private readonly canvas: HTMLCanvasElement | null = null;
    private readonly jsPath: string | null = null;
    
    public constructor()
    {
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
    
    private startGame(): void
    {
        if (empty(this.jsPath)) {
            addSnackbar(`No GameMaker JavaScript file provided.`,'failure');
        } else if (this.canvas) {
            //Create tag
            const tag = document.createElement('script');
            tag.type = 'text/javascript';
            tag.src = String(this.jsPath);
            tag.onload = (): void => {
                this.canvas?.classList.remove('hidden');
                // @ts-expect-error: GameMaker files are not integrated into main codebase, so suppressing errors
                // eslint-disable-next-line @typescript-eslint/no-unsafe-call
                GameMaker_Init();
            };
            tag.onerror = (): void => {
                addSnackbar(`Failed to load \`${String(this.jsPath)}\` script.`, 'failure');
            };
            //Append the script
            document.head.appendChild(tag);
        } else {
            addSnackbar(`No GameMaker canvas provided.`,'failure');
        }
    }
}
