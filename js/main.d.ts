declare function ajax(url: string, formData?: FormData | null, type?: string, method?: string, timeout?: number, skipError?: boolean): Promise<any>;
declare function getMeta(metaName: string): string | null;
declare function updateHistory(newUrl: string, title: string): void;
declare function submitIntercept(form: HTMLFormElement, callable: Function): void;
declare function deleteRow(element: HTMLElement): boolean;
declare function basename(text: string): string;
declare function buttonToggle(button: HTMLInputElement, enable?: boolean): void;
declare function rawurlencode(str: string): string;
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
    autosave_ask_before_unload: boolean;
    autosave_restore_when_empty: boolean;
    autosave_interval: string;
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
declare function loadTinyMCE(id: string, noMedia?: boolean, noRestoreOnEmpty?: boolean): void;
declare function saveTinyMCE(id: string): void;
declare function cleanGET(): void;
declare function hashCheck(): void;
declare function router(): void;
declare class BackToTop extends HTMLElement {
    private static content;
    private static BTTs;
    constructor();
    private toggleButtons;
}
declare class Gallery extends HTMLElement {
    private _current;
    images: Array<HTMLElement>;
    get current(): number;
    set current(value: number);
    constructor();
    private open;
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
    private zoom;
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
    private toScroll;
    private disableScroll;
}
declare class ImageUpload extends HTMLElement {
    private readonly preview;
    private readonly file;
    private readonly label;
    constructor();
    private update;
}
declare class Likedis extends HTMLElement {
    private readonly postId;
    private likeValue;
    private likesCount;
    private dislikesCount;
    private likeButton;
    private dislikeButton;
    constructor();
    private like;
    private updateCounts;
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
declare class SelectCustom extends HTMLElement {
    private readonly icon;
    private readonly select;
    private readonly label;
    private readonly description;
    constructor();
    private update;
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
    private close;
}
declare class Timer extends HTMLElement {
    private readonly interval;
    constructor();
}
declare class Tooltip extends HTMLElement {
    private x;
    private y;
    constructor();
    private onMouseMove;
    private onFocus;
    private tooltipCursor;
    private update;
}
declare class VerticalTabs extends HTMLElement {
    private readonly tabs;
    private readonly contents;
    private readonly wrapper;
    private currentTab;
    constructor();
    private tabSwitch;
}
declare class WebShare extends HTMLElement {
    constructor();
    private share;
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
    private singInUpSubmit;
    private loginRadioCheck;
}
declare class Details {
    static list: HTMLDetailsElement[];
    private static _instance;
    constructor();
    private reset;
    private clickOutsideDetails;
}
declare class Form {
    private static _instance;
    constructor();
    private formEnter;
    private inputBackSpace;
    private autoNext;
    private nextInput;
    private pasteSplit;
}
declare class Headings {
    private static _instance;
    constructor();
    private copyLink;
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
    private copy;
}
declare class Textarea {
    private static _instance;
    constructor();
    countCharacters(textarea: HTMLTextAreaElement): void;
}
declare class PostForm extends HTMLElement {
    private readonly textarea;
    private readonly replyToInput;
    private readonly label;
    constructor();
    replyTo(postId: string): void;
}
