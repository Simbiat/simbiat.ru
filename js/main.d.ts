declare function ajax(url: string, formData?: FormData | null, type?: string, method?: string, timeout?: number, skipError?: boolean): Promise<any>;
declare function getMeta(metaName: string): string | null;
declare function updateHistory(newUrl: string, title: string): void;
declare function submitIntercept(form: HTMLFormElement, callable: Function): void;
declare const pageTitle = " on Simbiat Software";
declare const emailRegex = "[\\p{L}\\d.!#$%&'*+\\/=?^_`{|}~-]+@[a-zA-Z\\d](?:[a-zA-Z\\d\\-]{0,61}[a-zA-Z\\d])?(?:\\.[a-zA-Z\\d](?:[a-zA-Z\\d\\-]{0,61}[a-zA-Z\\d])?)*";
declare const userRegex = "^[\\p{L}\\d.!#$%&'*+\\\\/=?^_`{|}~\\- ]{1,64}$";
declare function init(): void;
declare function cleanGET(): void;
declare function hashCheck(): void;
declare function router(): void;
declare class BackToTop extends HTMLElement {
    private static content;
    private static BTTs;
    constructor();
    toggleButtons(): void;
}
declare class Gallery extends HTMLElement {
    private _current;
    images: Array<HTMLElement>;
    get current(): number;
    set current(value: number);
    constructor();
    open(): void;
    close(): void;
    previous(): void;
    next(): void;
    private keyNav;
    private history;
}
declare class GalleryImage extends HTMLElement {
    private image;
    constructor();
    private checkZoom;
    zoom(): void;
}
declare class GalleryPrev extends HTMLElement {
    private overlay;
    constructor();
}
declare class GalleryNext extends HTMLElement {
    private overlay;
    constructor();
}
declare class GalleryClose extends HTMLElement {
    constructor();
}
declare class CarouselList extends HTMLElement {
    private readonly list;
    private readonly next;
    private readonly previous;
    private readonly maxScroll;
    constructor();
    toScroll(event: Event): void;
    disableScroll(): void;
}
declare class PasswordShow extends HTMLElement {
    private passwordInput;
    constructor();
    private toggle;
}
declare class PasswordRequirements extends HTMLElement {
    private passwordInput;
    constructor();
    private validate;
    private show;
    private hide;
}
declare class PasswordStrength extends HTMLElement {
    private passwordInput;
    private strengthSpan;
    constructor();
    private calculate;
    private show;
    private hide;
}
declare class Snackbar {
    private readonly snacks;
    private static notificationIndex;
    constructor(text: string, color?: string, milliseconds?: number);
}
declare class SnackbarClose extends HTMLElement {
    private readonly snackbar;
    private readonly snack;
    constructor();
    close(): void;
}
declare class Timer extends HTMLElement {
    private readonly interval;
    constructor();
}
declare class Tooltip extends HTMLElement {
    private x;
    private y;
    constructor();
    onMouseMove(event: MouseEvent): void;
    onFocus(event: Event): void;
    private tooltipCursor;
    private update;
}
declare class WebShare extends HTMLElement {
    constructor();
    share(): Promise<void>;
}
declare class A {
    private static _instance;
    constructor();
    newTabStyle(anchor: HTMLAnchorElement): void;
}
declare class Aside {
    private static _instance;
    private sidebarDiv;
    private readonly loginForm;
    constructor();
    singInUpSubmit(): void;
    loginRadioCheck(): void;
}
declare class Details {
    static list: HTMLDetailsElement[];
    private static _instance;
    constructor();
    reset(target: HTMLDetailsElement): void;
    clickOutsideDetails(event: MouseEvent, details: HTMLDetailsElement): void;
}
declare class Form {
    private static _instance;
    constructor();
    formEnter(event: KeyboardEvent): void | boolean;
    searchAction(event: Event): void;
    rawurlencode(str: string): string;
    private inputBackSpace;
    private autoNext;
    nextInput(initial: HTMLInputElement, reverse?: boolean): HTMLInputElement | boolean;
    private pasteSplit;
}
declare class Headings {
    private static _instance;
    constructor();
    copyLink(target: HTMLHeadingElement): string;
}
declare class Input {
    private static _instance;
    constructor();
    init(inputElement: HTMLInputElement): void;
    ariaNation(inputElement: HTMLInputElement): void;
}
declare class Nav {
    private static _instance;
    private navDiv;
    constructor();
}
declare class Quotes {
    private static _instance;
    constructor();
    copy(node: HTMLElement): string;
}
declare class Textarea {
    private static _instance;
    constructor();
}
