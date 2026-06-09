/**
 * @file Handle scripts on the game pages.
 */
import { empty } from 'Common/Helpers.mts';
import { Snackbar } from 'Common/Snackbar.mts';

// noinspection FunctionNamingConventionJS
/**
 * This is GameMaker's function, no control over its name.
 */
declare function GameMaker_Init(): void;

/**
 * Handle scripts on the game pages.
 */
export class Games {
  private readonly wrapper: HTMLDivElement | null = null;
  private readonly js_path: string | null = null;

  public constructor() {
    const wrapper = document.querySelector<HTMLDivElement>('game-canvas');
    if (wrapper) {
      const button = wrapper.querySelector<HTMLButtonElement>('button');
      if (button) {
        button.addEventListener('click', () => {
          this.startGame();
        });
        button.addEventListener('keydown', (event) => {
          if (['Enter', 'NumpadEnter', 'Space'].includes(event.code)) {
            this.startGame();
          }
        });
      }
      const overlay = wrapper.querySelector<HTMLDivElement>('#play_overlay');
      if (overlay) {
        overlay.addEventListener('click', () => {
          this.startGame();
        });
      }
      this.wrapper = wrapper.querySelector<HTMLDivElement>('#gm4html5_div_id');
      if ((this.wrapper?.hasAttribute('data-js')) === true) {
        this.js_path = this.wrapper.getAttribute('data-js');
      }
    }
  }

  /**
   * Start game.
   */
  private startGame(): void {
    if (empty(this.js_path)) {
      void new Snackbar(`No GameMaker JavaScript file provided.`, 'failure');
    } else if (this.wrapper) {
      //Check if we already have the canvas running
      if (document.querySelector<HTMLCanvasElement>('#canvas')) {
        void new Snackbar(`GameMaker canvas already loaded. If game is not running, please, try to refresh the page.`, 'failure');
        return;
      }
      if (typeof GameMaker_Init !== 'function') {
        void new Snackbar(`GameMaker script is not loaded. Try again in a few seconds. If the issue persists, try to refresh the page.`, 'failure');
        return;
      }
      //Create canvas
      const canvas = document.createElement('canvas');
      canvas.id = 'canvas';
      //Append canvas
      this.wrapper.appendChild(canvas);
      canvas.classList.remove('hidden');
      GameMaker_Init();
      document.querySelector<HTMLDivElement>('#play_overlay')
              ?.classList
              .add('hidden');
      document.querySelector<HTMLButtonElement>('#game_start_button')
              ?.classList
              .add('hidden');
      // Create the observer for the canvas size
      // Create a new MutationObserver
      const observer = new MutationObserver(() => {
        // Get the new dimensions of the element
        const {
          width,
          height,
        } = canvas.getBoundingClientRect();
        // If both dimensions are zero, reload
        if (width === 0 && height === 0) {
          window.location.reload();
        }
      });
      // Start observing the element
      observer.observe(canvas, {
        attributes: true,
        childList: true,
        subtree: true,
      });
    } else {
      void new Snackbar(`No GameMaker canvas provided.`, 'failure');
    }
  }
}
