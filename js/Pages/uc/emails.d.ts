export declare class Emails {
    private readonly addMailForm;
    constructor();
    add(): boolean | void;
    delete(button: HTMLInputElement): void;
    blockDelete(): void;
    subscribe(event: Event): void;
    activate(button: HTMLInputElement): void;
}
