declare const pageTitle = " on Simbiat Software";
declare function init(): void;
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
declare class Details {
    static list: HTMLDetailsElement[];
    private static _instance;
    constructor();
    reset(target: HTMLDetailsElement): void;
}
declare class Headings {
    private static _instance;
    constructor();
    copyLink(target: HTMLHeadingElement): string;
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
declare function getMeta(metaName: string): string | null;
declare function updateHistory(newUrl: string, title: string): void;
declare function cleanGET(): void;
declare function hashCheck(): void;
declare function rawurlencode(str: string): string;
declare function ajax(url: string, formData?: FormData | null, type?: string, method?: string, timeout?: number, skipError?: boolean): Promise<any>;
declare function bicInit(): void;
declare function bicCalc(): void | boolean;
declare function bicStyle(element: HTMLSpanElement, newClass: string, text?: string): void;
declare function bicRefresh(event: Event): void;
declare function fftrackerInit(): void;
declare function ffTrackAdd(): void;
declare function ffTrackTypeChange(target: HTMLSelectElement): void;
declare function ucInit(): void;
declare function addMail(): boolean | void;
declare function deleteMail(event: Event): void;
declare function blockDeleteMail(): void;
declare function subscribeMail(event: Event): void;
declare function activationMail(event: Event): void;
declare function singInUpSubmit(): void;
declare function passwordChange(): void;
declare const emailRegex = "[a-zA-Z0-9.!#$%&'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*";
declare const userRegex = "[^\\/\\\\\\[\\]:;|=$%#@&\\(\\)\\{\\}!,+*?<>\\0\\t\\r\\n\\x00-\\x1F\\x7F\\x0b\\f\\x85\\v\\cY\\b]{1,64}";
declare function showPassToggle(event: Event): void;
declare function passwordStrengthOnEvent(event: Event): void;
declare function passwordStrength(password: string): string;
declare function loginRadioCheck(): void;
declare class Timer extends HTMLElement {
    private readonly interval;
    constructor();
}
declare class Aside {
    private static _instance;
    private sidebarDiv;
    constructor();
}
declare class Nav {
    private static _instance;
    private navDiv;
    constructor();
}
declare const textInputTypes: string[];
declare const nonTextInputTypes: string[];
declare function formInit(): void;
declare const submitFunctions: {
    [key: string]: string;
};
declare function submitIntercept(formId: string): void;
declare function searchAction(event: Event): void;
declare function formEnter(event: KeyboardEvent): void | boolean;
declare function inputBackSpace(event: Event): void;
declare function autoNext(event: Event): void;
declare function pasteSplit(event: Event): Promise<void>;
declare function nextInput(initial: HTMLInputElement, reverse?: boolean): HTMLInputElement | boolean;
declare class Input {
    private static _instance;
    constructor();
}
declare function ariaInit(item: HTMLInputElement): void;
declare function ariaNation(inputElement: HTMLInputElement): void;
declare function ariaNationOnEvent(event: Event): void;
declare function colorValue(target: HTMLInputElement): void;
declare function colorValueOnEvent(event: Event): void;
declare class A {
    private static _instance;
    constructor();
}
