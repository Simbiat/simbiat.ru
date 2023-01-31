declare module "composer/vendor/tinymce/tinymce/tinymce.min" {
    export = e;
}
interface ajaxJSONResponse extends JSON {
    status: number;
    data: boolean | number | string;
    location: string;
    reason: string;
}
declare function ajax(url: string, formData?: FormData | null, type?: string, method?: string, timeout?: number, skipError?: boolean): Promise<ajaxJSONResponse | ArrayBuffer | Blob | FormData | boolean | string>;
declare function inputInit(input: HTMLInputElement): void;
declare function textareaInit(textarea: HTMLTextAreaElement): void;
declare function headingInit(heading: HTMLHeadingElement): void;
declare function formInit(form: HTMLFormElement): void;
declare function sampInit(samp: HTMLElement): void;
declare function codeInit(code: HTMLElement): void;
declare function blockquoteInit(quote: HTMLElement): void;
declare function qInit(quote: HTMLQuoteElement): void;
declare function detailsInit(details: HTMLDetailsElement): void;
declare function imgInit(img: HTMLImageElement): void;
declare function customizeNewElements(newNode: Node): void;
declare function getAllDetailsTags(): NodeListOf<HTMLDetailsElement>;
declare function closeAllDetailsTags(target: HTMLDetailsElement): void;
declare function clickOutsideDetailsTags(initialEvent: MouseEvent, details: HTMLDetailsElement): void;
declare function resetDetailsTags(target: HTMLDetailsElement): void;
declare function addSnackbar(text: string, color?: string, milliseconds?: number): void;
declare function getMeta(metaName: string): string | null;
declare function updateHistory(newUrl: string, title: string): void;
declare function submitIntercept(form: HTMLFormElement, callable: () => void): void;
declare function deleteRow(element: HTMLElement): boolean;
declare function basename(text: string): string;
declare function rawurlencode(str: string): string;
declare function empty(variable: unknown): boolean;
declare function newTabStyle(anchor: HTMLAnchorElement): void;
declare function pageRefresh(): void;
declare function copyQuote(target: HTMLElement): string;
declare function init(): void;
declare function ariaNation(inputElement: HTMLInputElement): void;
declare function buttonToggle(button: HTMLInputElement, enable?: boolean): void;
declare function countInTextarea(textarea: HTMLTextAreaElement): void;
declare function nextInput(initial: HTMLInputElement, reverse?: boolean): HTMLInputElement | null;
declare function pasteSplit(event: Event): Promise<void>;
declare function formEnter(event: KeyboardEvent): boolean;
declare function inputBackSpace(event: Event): void;
declare function autoNext(event: Event): void;
declare const customColorMap: Record<string, string>;
declare const tinySettings: {
    automatic_uploads: boolean;
    autosave_ask_before_unload: boolean;
    autosave_interval: string;
    autosave_restore_when_empty: boolean;
    base_url: string;
    block_formats: string;
    block_unsupported_drop: boolean;
    branding: boolean;
    browser_spellcheck: boolean;
    color_map: (string | undefined)[];
    content_css: string;
    content_security_policy: string;
    contextmenu: string;
    custom_colors: boolean;
    default_link_target: string;
    document_base_url: string;
    emoticons_database: string;
    entity_encoding: string;
    file_picker_types: string;
    font_formats: string;
    fontsize_formats: string;
    formats: {
        aligncenter: {
            classes: string;
            remove: string;
            selector: string;
        };
        alignjustify: {
            classes: string;
            remove: string;
            selector: string;
        };
        alignleft: {
            classes: string;
            remove: string;
            selector: string;
        };
        alignright: {
            classes: string;
            remove: string;
            selector: string;
        };
        forecolor: {
            attributes: {
                class: (value: any) => string;
            };
            inline: string;
            remove: string;
        };
        hilitecolor: {
            attributes: {
                class: (value: any) => string;
            };
            inline: string;
            remove: string;
        };
        'list-circle': {
            classes: string;
            remove: string;
            selector: string;
        };
        'list-decimal': {
            classes: string;
            remove: string;
            selector: string;
        };
        'list-decimal-leading-zero': {
            classes: string;
            remove: string;
            selector: string;
        };
        'list-disc': {
            classes: string;
            remove: string;
            selector: string;
        };
        'list-disclosure-closed': {
            classes: string;
            remove: string;
            selector: string;
        };
        'list-disclosure-open': {
            classes: string;
            remove: string;
            selector: string;
        };
        'list-lower-alpha': {
            classes: string;
            remove: string;
            selector: string;
        };
        'list-lower-greek': {
            classes: string;
            remove: string;
            selector: string;
        };
        'list-lower-roman': {
            classes: string;
            remove: string;
            selector: string;
        };
        'list-square': {
            classes: string;
            remove: string;
            selector: string;
        };
        'list-upper-alpha': {
            classes: string;
            remove: string;
            selector: string;
        };
        'list-upper-roman': {
            classes: string;
            remove: string;
            selector: string;
        };
        underline: {
            classes: string;
            inline: string;
            remove: string;
        };
        valignbottom: {
            classes: string;
            remove: string;
            selector: string;
        };
        valignmiddle: {
            classes: string;
            remove: string;
            selector: string;
        };
        valigntop: {
            classes: string;
            remove: string;
            selector: string;
        };
    };
    hidden_input: boolean;
    image_advtab: boolean;
    image_caption: boolean;
    image_class_list: ({
        title: string;
        value: string;
        menu?: never;
    } | {
        menu: {
            title: string;
            value: string;
        }[];
        title: string;
        value?: never;
    })[];
    image_description: boolean;
    image_dimensions: boolean;
    image_title: boolean;
    image_uploadtab: boolean;
    images_file_types: string;
    images_reuse_filename: boolean;
    images_upload_credentials: boolean;
    images_upload_url: string;
    insertdatetime_element: boolean;
    invalid_elements: string;
    invalid_styles: string;
    lineheight_formats: string;
    link_assume_external_targets: string;
    link_context_toolbar: boolean;
    link_default_protocol: string;
    link_target_list: {
        title: string;
        value: string;
    }[];
    link_title: boolean;
    lists_indent_on_tab: boolean;
    menu: {
        edit: {
            items: string;
            title: string;
        };
        file: {
            items: string;
            title: string;
        };
        format: {
            items: string;
            title: string;
        };
        help: {
            items: string;
            title: string;
        };
        insert: {
            items: string;
            title: string;
        };
        table: {
            items: string;
            title: string;
        };
        view: {
            items: string;
            title: string;
        };
    };
    menubar: string;
    object_resizing: boolean;
    paste_block_drop: boolean;
    paste_data_images: boolean;
    paste_remove_styles_if_webkit: boolean;
    paste_webkit_styles: string;
    plugins: string;
    promotion: boolean;
    quickbars_insert_toolbar: boolean;
    readonly: boolean;
    referrer_policy: string;
    relative_urls: boolean;
    remove_script_host: boolean;
    remove_trailing_brs: boolean;
    resize_img_proportional: boolean;
    schema: string;
    selector: string;
    skin: string;
    style_formats: {
        items: {
            format: string;
            title: string;
        }[];
        title: string;
    }[];
    style_formats_autohide: boolean;
    table_advtab: boolean;
    table_appearance_options: boolean;
    table_border_styles: {
        title: string;
        value: string;
    }[];
    table_border_widths: {
        title: string;
        value: string;
    }[];
    table_cell_advtab: boolean;
    table_default_attributes: {};
    table_header_type: string;
    table_resize_bars: boolean;
    table_row_advtab: boolean;
    table_sizing_mode: string;
    table_style_by_css: boolean;
    table_toolbar: string;
    theme_advanced_default_foreground_color: string;
    toolbar: string;
    toolbar_mode: string;
    valid_styles: {};
    visual: boolean;
    visualblocks_default_state: boolean;
};
declare function tinyMCEtoTextarea(textarea: HTMLTextAreaElement, tinyInstance: any): void;
declare function tinyMCEHideInputs(): void;
declare function loadTinyMCE(id: string, noMedia?: boolean, noRestoreOnEmpty?: boolean): void;
declare function saveTinyMCE(id: string, textareaOnly?: boolean): void;
declare function cleanGET(): void;
declare function hashCheck(): void;
declare function router(): void;
declare class BackToTop extends HTMLElement {
    private readonly content;
    private readonly BTTs;
    constructor();
    private toggleButtons;
}
declare class Gallery extends HTMLElement {
    private _current;
    images: HTMLElement[];
    private readonly galleryName;
    private readonly galleryNameLink;
    private readonly galleryLoadedImage;
    private readonly galleryTotal;
    private readonly galleryCurrent;
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
    private readonly image;
    private readonly zoomListener;
    constructor();
    private checkZoom;
    private zoom;
}
declare class GalleryPrev extends HTMLElement {
    private readonly overlay;
    constructor();
}
declare class GalleryNext extends HTMLElement {
    private readonly overlay;
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
    private readonly likesCount;
    private readonly dislikesCount;
    private readonly likeButton;
    private readonly dislikeButton;
    constructor();
    private like;
    private updateCounts;
}
declare class LoginForm extends HTMLElement {
    private readonly userRegex;
    private readonly emailRegex;
    private readonly loginForm;
    private readonly existUser;
    private readonly newUser;
    private readonly forget;
    private readonly login;
    private readonly password;
    private readonly button;
    private readonly rememberme;
    private readonly username;
    constructor();
    private singInUpSubmit;
    private loginRadioCheck;
}
declare class NavShow extends HTMLElement {
    private readonly navDiv;
    constructor();
}
declare class NavHide extends HTMLElement {
    private readonly navDiv;
    constructor();
}
declare class SideShow extends HTMLElement {
    private readonly sidebar;
    constructor();
}
declare class SideHide extends HTMLElement {
    private readonly sidebar;
    constructor();
}
declare class OGImage extends HTMLElement {
    private readonly ogimage;
    private readonly hideBanner;
    constructor();
    private toggleBanner;
}
declare class PasswordShow extends HTMLElement {
    private readonly passwordInput;
    constructor();
    private toggle;
}
declare class PasswordRequirements extends HTMLElement {
    private readonly passwordInput;
    constructor();
    private validate;
    private show;
    private hide;
}
declare class PasswordStrength extends HTMLElement {
    private readonly passwordInput;
    private readonly strengthSpan;
    constructor();
    private calculate;
    private show;
    private hide;
}
declare class PostForm extends HTMLElement {
    private readonly textarea;
    private readonly replyToInput;
    private readonly label;
    constructor();
    replyTo(postId: string): void;
}
declare class SelectCustom extends HTMLElement {
    private readonly icon;
    private readonly select;
    private readonly label;
    private readonly description;
    constructor();
    private update;
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
    private width;
    private height;
    constructor();
    private onPointerMove;
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
    private updateCurrentTab;
}
declare class WebShare extends HTMLElement {
    constructor();
    private share;
}
