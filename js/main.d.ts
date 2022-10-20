declare function ajax(url: string, formData?: FormData | null, type?: string, method?: string, timeout?: number, skipError?: boolean): Promise<any>;
declare function getMeta(metaName: string): string | null;
declare function updateHistory(newUrl: string, title: string): void;
declare function submitIntercept(form: HTMLFormElement, callable: Function): void;
declare function deleteRow(element: HTMLElement): boolean;
declare function basename(text: string): string;
declare const pageTitle = " on Simbiat Software";
declare const emailRegex = "[\\p{L}\\d.!#$%&'*+\\/=?^_`{|}~-]+@[a-zA-Z\\d](?:[a-zA-Z\\d\\-]{0,61}[a-zA-Z\\d])?(?:\\.[a-zA-Z\\d](?:[a-zA-Z\\d\\-]{0,61}[a-zA-Z\\d])?)*";
declare const userRegex = "^[\\p{L}\\d.!#$%&'*+\\\\/=?^_`{|}~\\- ]{1,64}$";
declare function init(): void;
declare const customColorMap: {
    [key: string]: string;
};
declare const tinySettings: {
    selector: string;
    relative_urls: boolean;
    remove_script_host: boolean;
    base_url: string;
    document_base_url: string;
    referrer_policy: string;
    content_security_policy: string;
    skin: string;
    content_css: string;
    hidden_input: boolean;
    readonly: boolean;
    block_formats: string;
    branding: boolean;
    plugins: string;
    contextmenu: string;
    table_toolbar: string;
    quickbars_insert_toolbar: boolean;
    font_formats: string;
    fontsize_formats: string;
    lineheight_formats: string;
    menu: {
        file: {
            title: string;
            items: string;
        };
        edit: {
            title: string;
            items: string;
        };
        view: {
            title: string;
            items: string;
        };
        format: {
            title: string;
            items: string;
        };
        insert: {
            title: string;
            items: string;
        };
        table: {
            title: string;
            items: string;
        };
        help: {
            title: string;
            items: string;
        };
    };
    valid_styles: {};
    menubar: string;
    toolbar: string;
    theme_advanced_default_foreground_color: string;
    style_formats: never[];
    toolbar_mode: string;
    custom_colors: boolean;
    color_map: (string | undefined)[];
    formats: {
        forecolor: {
            inline: string;
            attributes: {
                class: (value: any) => string;
            };
            remove: string;
        };
        hilitecolor: {
            inline: string;
            remove: string;
            attributes: {
                class: (value: any) => string;
            };
        };
        underline: {
            inline: string;
            classes: string;
            remove: string;
        };
        alignleft: {
            selector: string;
            classes: string;
            remove: string;
        };
        alignright: {
            selector: string;
            classes: string;
            remove: string;
        };
        aligncenter: {
            selector: string;
            classes: string;
            remove: string;
        };
        alignjustify: {
            selector: string;
            classes: string;
            remove: string;
        };
        valigntop: {
            selector: string;
            classes: string;
            remove: string;
        };
        valignmiddle: {
            selector: string;
            classes: string;
            remove: string;
        };
        valignbottom: {
            selector: string;
            classes: string;
            remove: string;
        };
    };
    visual: boolean;
    entity_encoding: string;
    invalid_styles: string;
    schema: string;
    browser_spellcheck: boolean;
    resize_img_proportional: boolean;
    link_default_protocol: string;
    autosave_restore_when_empty: boolean;
    emoticons_database: string;
    image_caption: boolean;
    image_advtab: boolean;
    image_title: boolean;
    image_description: boolean;
    image_uploadtab: boolean;
    images_file_types: string;
    images_upload_credentials: boolean;
    images_reuse_filename: boolean;
    images_upload_url: string;
    paste_data_images: boolean;
    paste_remove_styles_if_webkit: boolean;
    paste_webkit_styles: string;
    image_class_list: {
        title: string;
        value: string;
    }[];
    automatic_uploads: boolean;
    remove_trailing_brs: boolean;
    file_picker_types: string;
    block_unsupported_drop: boolean;
    image_dimensions: boolean;
    insertdatetime_element: boolean;
    link_target_list: {
        title: string;
        value: string;
    }[];
    default_link_target: string;
    link_assume_external_targets: string;
    link_context_toolbar: boolean;
    paste_block_drop: boolean;
    visualblocks_default_state: boolean;
    lists_indent_on_tab: boolean;
    promotion: boolean;
    table_appearance_options: boolean;
    table_border_widths: {
        title: string;
        value: string;
    }[];
    table_border_styles: {
        title: string;
        value: string;
    }[];
    table_advtab: boolean;
    table_cell_advtab: boolean;
    table_row_advtab: boolean;
    table_style_by_css: boolean;
    object_resizing: boolean;
    link_title: boolean;
};
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
    private readonly zoomListener;
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
