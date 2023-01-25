const customColorMap: Record<string, string> = {
    '#17141F': 'body',
    '#19424D': 'dark-border',
    '#231F2E': 'block',
    '#266373': 'light-border',
    '#2E293D': 'article',
    '#808080': 'disabled',
    '#8AE59C': 'success',
    '#9AD4EA': 'interactive',
    '#E6B63D': 'warning',
    '#F3A0B6': 'failure',
    '#F5F0F0': 'text',
};

const tinySettings = {
    'automatic_uploads': true,
    'autosave_ask_before_unload': true,
    'autosave_interval': '5s',
    'autosave_restore_when_empty': true,
    'base_url': '/js/tinymce/',
    'block_formats': 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6;',
    'block_unsupported_drop': true,
    'branding': true,
    'browser_spellcheck': true,
    'color_map': Object.keys(customColorMap).map((key) => { return [key, customColorMap[key]]; }).
                                                                                                    flat(),
    'content_css': '/css/tinymce.css',
    'content_security_policy': "default-src 'self'",
    'contextmenu': 'emoticons link image',
    'custom_colors': false,
    'default_link_target': '_blank',
    'document_base_url': `${window.location.protocol}//${window.location.hostname}/`,
    'emoticons_database': 'emojis',
    'entity_encoding': 'named',
    'file_picker_types': 'file image media',
    'font_formats': '',
    'fontsize_formats': '',
    'formats': {
        'aligncenter': {
            'classes': 'tiny-align-center',
            'remove': 'none',
            'selector': 'strong,em,sub,sup,s,a,time,p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,audio,video',
        },
        'alignjustify': {
            'classes': 'tiny-align-justify',
            'remove': 'none',
            'selector': 'strong,em,sub,sup,s,a,time,p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,audio,video',
        },
        'alignleft': {
            'classes': 'tiny-align-left',
            'remove': 'none',
            'selector': 'strong,em,sub,sup,s,a,time,p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,audio,video',
        },
        'alignright': {
            'classes': 'tiny-align-right',
            'remove': 'none',
            'selector': 'strong,em,sub,sup,s,a,time,p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,audio,video',
        },
        'forecolor': {
            'attributes': {
                // @ts-expect-error: I do not remember where I've taken this code example and what type `value` is supposed to be
                // eslint-disable-next-line @typescript-eslint/no-unsafe-member-access
                'class': (value): string => { return `tiny-color-${String(customColorMap[value.value])}`; },
                'inline': 'span',
            },
            'remove': 'none',
        },
        'hilitecolor': {
            'attributes': {
                // @ts-expect-error: I do not remember where I've taken this code example and what type `value` is supposed to be
                // eslint-disable-next-line @typescript-eslint/no-unsafe-member-access
                'class': (value): string => { return `tiny-bg-color-${String(customColorMap[value.value])}`; },
            },
            'inline': 'span',
            'remove': 'none',
        },
        'underline': {
            'classes': 'tiny-underline',
            'inline': 'span',
            'remove': 'none',
        },
        'valignbottom': {
            'classes': 'tiny-valign-bottom',
            'remove': 'none',
            'selector': 'td,th,table',
        },
        'valignmiddle': {
            'classes': 'tiny-valign-middle',
            'remove': 'none',
            'selector': 'td,th,table',
        },
        'valigntop': {
            'classes': 'tiny-valign-top',
            'remove': 'none',
            'selector': 'td,th,table',
        },
    },
    'hidden_input': false,
    'image_advtab': false,
    'image_caption': true,
    'image_class_list': [
        { 'title': 'None',
            'value': 'w25pc middle block galleryZoom' },
        { 'title': 'Fullwidth',
            'value': 'w100pc middle block galleryZoom' },
        { 'title': 'Icon',
            'value': 'linkIcon' }
    ],
    'image_description': true,
    'image_dimensions': false,
    'image_title': true,
    'image_uploadtab': true,
    'images_file_types': 'jpeg,jpg,png,gif,bmp,webp,svg',
    'images_reuse_filename': true,
    'images_upload_credentials': true,
    'images_upload_url': '/api/upload/',
    'insertdatetime_element': true,
    'invalid_styles': 'font-size line-height',
    'lineheight_formats': '',
    'link_assume_external_targets': 'https',
    'link_context_toolbar': true,
    'link_default_protocol': 'https',
    'link_target_list': [
        {'title': 'New window',
            'value': '_blank'},
        {'title': 'Current window',
            'value': '_self'}
    ],
    'link_title': false,
    'lists_indent_on_tab': true,
    'menu': {
        'edit': { 'items': 'undo redo | cut copy paste pastetext | selectall | searchreplace',
            'title': 'Edit', },
        'file': { 'items': 'newdocument restoredraft',
            'title': 'File', },
        'format': { 'items': 'underline strikethrough superscript subscript | align',
            'title': 'Format', },
        'help': { 'items': 'help wordcount',
            'title': 'Help', },
        'insert': { 'items': 'link image media codeformat | emoticons charmap hr | insertdatetime',
            'title': 'Insert', },
        'table': { 'items': 'inserttable | cell row column | deletetable',
            'title': 'Table', },
        'view': { 'items': 'code preview | visualaid visualchars visualblocks | fullscreen',
            'title': 'View', },
    },
    'menubar': 'file edit view format insert table help',
    'object_resizing': false,
    'paste_block_drop': false,
    'paste_data_images': false,
    'paste_remove_styles_if_webkit': true,
    'paste_webkit_styles': 'none',
    'plugins': 'autolink autosave charmap code emoticons fullscreen help image insertdatetime link lists media preview quickbars searchreplace table visualblocks visualchars wordcount',
    'promotion': false,
    'quickbars_insert_toolbar': false,
    'readonly': false,
    'referrer_policy': 'no-referrer',
    'relative_urls': false,
    'remove_script_host': true,
    'remove_trailing_brs': true,
    'resize_img_proportional': true,
    'schema': 'html5-strict',
    'selector': 'textarea.tinymce',
    'skin': 'oxide-dark',
    'style_formats': [],
    'table_advtab': false,
    'table_appearance_options': false,
    'table_border_styles': [
        { 'title': 'Solid',
            'value': 'solid' },
    ],
    'table_border_widths': [
        { 'title': 'default',
            'value': '0.125rem' },
    ],
    'table_cell_advtab': false,
    'table_row_advtab': false,
    'table_style_by_css': false,
    'table_toolbar': 'tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol | tabledelete',
    'theme_advanced_default_foreground_color': "#F5F0F0",
    'toolbar': 'undo redo | blocks | bold italic | forecolor backcolor | blockquote bullist numlist | removeformat',
    'toolbar_mode': 'wrap',
    'valid_styles': {},
    'visual': true,
    'visualblocks_default_state': false,
};

function loadTinyMCE(id: string, noMedia = true, noRestoreOnEmpty = false): void
{
    if ((/^\s*$/ui).exec(id)) {
        return;
    }
    const textarea = document.querySelector(`#${id}`);
    if (textarea) {
        const settings = tinySettings;
        settings.selector = `#${id}`;
        if (noMedia) {
            //Remove plugins that allow upload of media
            settings.plugins = String(settings.plugins).replace('image ', '').
                                                        replace('media ', '');
            settings.images_upload_url = '';
            settings.menu.insert.items = settings.menu.insert.items.replace('image ', '').replace('media ', '');
        }
        if (noRestoreOnEmpty) {
            settings.autosave_restore_when_empty = false;
        }
        void import('/js/tinymce/tinymce.min.js').then(() => {
            // @ts-expect-error: I can't make TS see tinymce object without turning the file into a module, which does not suit current structure
            // As such I am suppressing a bunch of linters' errors here
            // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
            void tinymce.init(settings).then(() => {
                // @ts-expect-error: I can't make TS see tinymce object without turning the file into a module, which does not suit current structure
                // As such I am suppressing a bunch of linters' errors here
                // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access,@typescript-eslint/no-unsafe-assignment
                const tinyInstance = tinymce.get(id);
                if (tinyInstance !== null) {
                    // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
                    tinyInstance.on('input', () => {
                        //We want the dump to textarea to be done on every change for maximum transparency and to avoid extra calls to TinyMCE from outside
                        // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
                        (textarea as HTMLTextAreaElement).value = String(tinyInstance.getContent());
                        textarea.dispatchEvent(new Event('input'));
                    });
                }
            });
        });
    }
}

function saveTinyMCE(id: string, textareaOnly = false): void
{
    if ((/^\s*$/ui).exec(id)) {
        return;
    }
    const textarea = document.querySelector(`#${id}`);
    if (textarea !== null) {
        void import('/js/tinymce/tinymce.min.js').then(() => {
            // @ts-expect-error: I can't make TS see tinymce object without turning the file into a module, which does not suit current structure
            // As such I am suppressing a bunch of linters' errors here
            // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment,@typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
            const tinyInstance = tinymce.get(id);
            if (tinyInstance !== null) {
                if (textareaOnly) {
                    // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
                    (textarea as HTMLTextAreaElement).value = String(tinyInstance.getContent());
                    textarea.dispatchEvent(new Event('input'));
                } else {
                    // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
                    tinyInstance.save();
                }
            }
        });
    }
}
