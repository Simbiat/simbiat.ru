export class Games {
    //window.onload = GameMaker_Init;
    private readonly button: HTMLButtonElement | null = null;
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
            if ((gmDiv?.hasAttribute('data-js')) === true) {
                this.jsPath = gmDiv.getAttribute('data-js');
            }
        }
    }
    
    private startGame(): void
    {
        if (!empty(this.jsPath)) {
            //Create tag
            const tag = document.createElement('script');
            tag.type = 'text/javascript';
            tag.src = String(this.jsPath);
            tag.onload = (): void => {
                // @ts-expect-error: GameMaker files are not integrated into main codebase, so supressing errors
                // eslint-disable-next-line @typescript-eslint/no-unsafe-call
                GameMaker_Init();
            };
            tag.onerror = (): void => {
                addSnackbar(`Failed to load \`${String(this.jsPath)}\` script.`,'failure');
            };
            //Append the script
            document.head.appendChild(tag);
        } else {
            addSnackbar(`No GameMaker JavaScript file provided.`,'failure');
        }
    }
}
