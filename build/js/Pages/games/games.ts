export class Games {
    private readonly wrapper: HTMLDivElement | null = null;
    private readonly jsPath: string | null = null;
    
    public constructor()
    {
        const wrapper = document.querySelector('game-canvas');
        if (wrapper) {
            const button = wrapper.querySelector('button');
            if (button) {
                button.addEventListener('click', () => {
                    this.startGame();
                });
                button.addEventListener('keypress', (event) => {
                    if (['Enter', 'NumpadEnter', 'Space'].includes(event.code)) {
                        this.startGame();
                    }
                });
            }
            const overlay = wrapper.querySelector('#play_overlay');
            if (overlay) {
                overlay.addEventListener('click', () => {
                    this.startGame();
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
    
    private startGame(): void
    {
        if (empty(this.jsPath)) {
            addSnackbar(`No GameMaker JavaScript file provided.`,'failure');
        } else if (this.wrapper) {
            //Check if we already have the canvas running
            if (document.getElementById('canvas')) {
                return;
            }
            //Create canvas
            const canvas = document.createElement('canvas');
            canvas.id = 'canvas';
            //Append canvas
            this.wrapper.appendChild(canvas);
            //Create tag
            const tag = document.createElement('script');
            tag.type = 'text/javascript';
            tag.src = String(this.jsPath);
            tag.onload = (): void => {
                canvas.classList.remove('hidden');
                // @ts-expect-error: GameMaker files are not integrated into main codebase, so suppressing errors
                // eslint-disable-next-line @typescript-eslint/no-unsafe-call
                GameMaker_Init();
                document.querySelector('#play_overlay')?.classList.add('hidden');
                document.querySelector('#gameStartButton')?.classList.add('hidden');
            };
            tag.onerror = (): void => {
                addSnackbar(`Failed to load \`${String(this.jsPath)}\` script.`, 'failure');
            };
            //Append the script
            document.head.appendChild(tag);
            //Create observer for the canvas size
            // Create a new MutationObserver
            const observer = new MutationObserver(() => {
                // Get the new dimensions of the element
                const { width, height } = canvas.getBoundingClientRect();
                // If both dimensions are zero, reload
                if (width === 0 && height === 0) {
                    window.location.reload();
                }
            });
            // Start observing the element
            observer.observe(canvas, {
                'attributes': true,
                'childList': true,
                'subtree': true
            });
        } else {
            addSnackbar(`No GameMaker canvas provided.`,'failure');
        }
    }
}
