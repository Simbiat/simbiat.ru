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
    'color_map': Object.keys(customColorMap)
                       .map((key) => {
                           return [key, customColorMap[key]];
                       })
                       .flat(),
    'content_css': '/css/tinymce.css',
    'content_security_policy': "default-src 'self'",
    'contextmenu': 'emoticons link image',
    'custom_colors': false,
    'default_link_target': '_blank',
    'document_base_url': `${window.location.protocol}//${window.location.hostname}/`,
    'emoticons_database': 'emojis',
    'entity_encoding': 'named',
    'file_picker_types': 'image',
    'font_formats': '',
    'fontsize_formats': '',
    'formats': {
        'aligncenter': {
            'classes': 'tiny-align-center',
            'remove': 'none',
            'selector': 'strong,em,sub,sup,s,a,time,p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,audio,video,blockquote',
        },
        'alignjustify': {
            'classes': 'tiny-align-justify',
            'remove': 'none',
            'selector': 'strong,em,sub,sup,s,a,time,p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,audio,video,blockquote',
        },
        'alignleft': {
            'classes': 'tiny-align-left',
            'remove': 'none',
            'selector': 'strong,em,sub,sup,s,a,time,p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,audio,video,blockquote',
        },
        'alignright': {
            'classes': 'tiny-align-right',
            'remove': 'none',
            'selector': 'strong,em,sub,sup,s,a,time,p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,audio,video,blockquote',
        },
        'forecolor': {
            'attributes': {
                // @ts-expect-error: I do not remember where I've taken this code example and what type `value` is supposed to be
                'class': (value): string => {
                    // eslint-disable-next-line @typescript-eslint/no-unsafe-member-access
                    return `tiny-color-${String(customColorMap[value.value])}`;
                },
            },
            'inline': 'span',
            'remove': 'none',
        },
        'hilitecolor': {
            'attributes': {
                // @ts-expect-error: I do not remember where I've taken this code example and what type `value` is supposed to be
                'class': (value): string => {
                    // eslint-disable-next-line @typescript-eslint/no-unsafe-member-access
                    return `tiny-bg-color-${String(customColorMap[value.value])}`;
                },
            },
            'inline': 'span',
            'remove': 'none',
        },
        'list-circle': {
            'classes': 'tiny-list-circle',
            'remove': 'none',
            'selector': 'ul,ul>li'
        },
        'list-decimal': {
            'classes': 'tiny-list-decimal',
            'remove': 'none',
            'selector': 'ol,ol>li'
        },
        'list-decimal-leading-zero': {
            'classes': 'tiny-list-decimal-leading-zero',
            'remove': 'none',
            'selector': 'ol,ol>li'
        },
        'list-disc': {
            'classes': 'tiny-list-disc',
            'remove': 'none',
            'selector': 'ul,ul>li'
        },
        'list-disclosure-closed': {
            'classes': 'tiny-list-disclosure-closed',
            'remove': 'none',
            'selector': 'ul,ul>li'
        },
        'list-disclosure-open': {
            'classes': 'tiny-list-disclosure-open',
            'remove': 'none',
            'selector': 'ul,ul>li'
        },
        'list-lower-alpha': {
            'classes': 'tiny-list-lower-alpha',
            'remove': 'none',
            'selector': 'ol,ol>li'
        },
        'list-lower-greek': {
            'classes': 'tiny-list-lower-greek',
            'remove': 'none',
            'selector': 'ol,ol>li'
        },
        'list-lower-roman': {
            'classes': 'tiny-list-lower-roman',
            'remove': 'none',
            'selector': 'ol,ol>li'
        },
        'list-square': {
            'classes': 'tiny-list-square',
            'remove': 'none',
            'selector': 'ul,ul>li'
        },
        'list-upper-alpha': {
            'classes': 'tiny-list-upper-alpha',
            'remove': 'none',
            'selector': 'ol,ol>li'
        },
        'list-upper-roman': {
            'classes': 'tiny-list-upper-roman',
            'remove': 'none',
            'selector': 'ol,ol>li'
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
    'image_caption': false,
    'image_class_list': [
        {
            'title': 'Default',
            'value': 'w50pc middle block galleryZoom'
        },
        {
            'menu': [
                {
                    'title': 'Quarter width',
                    'value': 'w25pc middle block galleryZoom'
                },
                {
                    'title': 'Half width',
                    'value': 'w50pc middle block galleryZoom'
                },
                {
                    'title': '3 quarters width',
                    'value': 'w75pc middle block galleryZoom'
                },
                {
                    'title': 'Full width',
                    'value': 'w100pc middle block galleryZoom'
                }
            ],
            'title': 'Block'
        },
        {
            'menu': [
                {
                    'title': 'Quarter width',
                    'value': 'w25pc middle galleryZoom'
                },
                {
                    'title': 'Half width',
                    'value': 'w50pc middle galleryZoom'
                },
                {
                    'title': '3 quarters width',
                    'value': 'w75pc middle galleryZoom'
                },
                {
                    'title': 'Full width',
                    'value': 'w100pc middle galleryZoom'
                }
            ],
            'title': 'Inline'
        },
        {
            'title': 'Icon',
            'value': 'linkIcon'
        }
    ],
    'image_description': true,
    'image_dimensions': false,
    'image_title': false,
    'image_uploadtab': true,
    'images_file_types': 'jpeg,jpg,png,gif,bmp,webp,svg',
    'images_reuse_filename': true,
    'images_upload_credentials': true,
    'images_upload_url': '/api/upload/',
    'insertdatetime_element': true,
    'invalid_elements': 'acronym,applet,area,aside,base,basefont,bgsound,big,blink,body,button,canvas,center,content,datalist,dialog,dir,embed,fieldset,figure,figcaption,font,footer,form,frame,frameset,head,header,hgroup,html,iframe,input,image,keygen,legend,link,main,map,marquee,menuitem,meter,nav,nobr,noembed,noframes,noscript,object,optgroup,option,param,picture,plaintext,portal,pre,progress,rb,rp,rt,rtc,ruby,script,select,selectmenu,shadow,slot,strike,style,spacer,template,textarea,title,tt,xmp',
    'invalid_styles': 'font-size line-height',
    'license_key': 'gpl',
    'lineheight_formats': '',
    'link_assume_external_targets': 'https',
    'link_context_toolbar': true,
    'link_default_protocol': 'https',
    'link_target_list': [
        {
            'title': 'New window',
            'value': '_blank'
        },
        {
            'title': 'Current window',
            'value': '_self'
        }
    ],
    'link_title': false,
    'lists_indent_on_tab': true,
    'menu': {
        'edit': {
            'items': 'undo redo | cut copy paste pastetext | selectall | searchreplace',
            'title': 'Edit',
        },
        'file': {
            'items': 'newdocument restoredraft',
            'title': 'File',
        },
        'format': {
            'items': 'underline strikethrough superscript subscript | align | styles',
            'title': 'Format',
        },
        'help': {
            'items': 'help wordcount',
            'title': 'Help',
        },
        'insert': {
            'items': 'link image codeformat | emoticons charmap hr | insertdatetime',
            'title': 'Insert',
        },
        'table': {
            'items': 'inserttable | cell row column | deletetable',
            'title': 'Table',
        },
        'view': {
            'items': 'code preview | visualaid visualchars visualblocks | fullscreen',
            'title': 'View',
        },
    },
    'menubar': 'file edit view format insert table help',
    'object_resizing': false,
    'paste_block_drop': true,
    'paste_data_images': false,
    'paste_remove_styles_if_webkit': true,
    'paste_webkit_styles': 'none',
    'plugins': 'autolink autosave charmap code emoticons fullscreen help image insertdatetime link lists preview quickbars searchreplace table visualblocks visualchars wordcount',
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
    'style_formats': [
        {
            'items': [
                {
                    'format': 'list-decimal',
                    'title': 'Decimal (default)'
                },
                {
                    'format': 'list-decimal-leading-zero',
                    'title': 'Decimal, leading zero'
                },
                {
                    'format': 'list-lower-alpha',
                    'title': 'Lower Latin'
                },
                {
                    'format': 'list-lower-greek',
                    'title': 'Lower Greek'
                },
                {
                    'format': 'list-lower-roman',
                    'title': 'Lower Roman'
                },
                {
                    'format': 'list-upper-alpha',
                    'title': 'Upper Latin'
                },
                {
                    'format': 'list-upper-roman',
                    'title': 'Upper Roman'
                },
            ],
            'title': 'Ordered lists'
        },
        {
            'items': [
                {
                    'format': 'list-circle',
                    'title': 'Circle'
                },
                {
                    'format': 'list-disc',
                    'title': 'Disc (default)'
                },
                {
                    'format': 'list-disclosure-closed',
                    'title': 'Disclosure closed'
                },
                {
                    'format': 'list-disclosure-open',
                    'title': 'Disclosure open'
                },
                {
                    'format': 'list-square',
                    'title': 'Square'
                },
            ],
            'title': 'Unordered lists'
        },
    ],
    'style_formats_autohide': true,
    'table_advtab': false,
    'table_appearance_options': false,
    'table_border_styles': [
        {
            'title': 'Solid',
            'value': 'solid'
        },
    ],
    'table_border_widths': [
        {
            'title': 'default',
            'value': '0.125rem'
        },
    ],
    'table_cell_advtab': false,
    'table_default_attributes': {},
    'table_header_type': 'sectionCells',
    'table_resize_bars': false,
    'table_row_advtab': false,
    'table_sizing_mode': 'relative',
    'table_style_by_css': false,
    'table_toolbar': 'tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol | tabledelete',
    'theme_advanced_default_foreground_color': "#F5F0F0",
    'toolbar': 'undo redo | blocks | bold italic | forecolor backcolor | blockquote bullist numlist | removeformat',
    'toolbar_mode': 'wrap',
    'valid_styles': {},
    'visual': true,
    'visualblocks_default_state': false,
};

// @ts-expect-error: I am unable to get proper types for TinyMCE, so suppressing complains about `any`
function tinyMCEtoTextarea(textarea: HTMLTextAreaElement, tinyInstance): void
{
    // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
    textarea.value = String(tinyInstance.getContent());
    textarea.dispatchEvent(new Event('input'));
}

function tinyMCEHideInputs(): void
{
    //Get dialog
    const dialog: HTMLDivElement | null = document.querySelector('div[role=dialog].tox-dialog');
    if (dialog) {
        //Get title
        const title: HTMLDivElement | null = dialog.querySelector('div.tox-dialog__title');
        if (title) {
            //Get the labels
            const labels = dialog.querySelectorAll('label');
            //Get name of the dialog
            const titleText = String(title.textContent)
                .toLowerCase();
            if (titleText === 'insert/edit image') {
                labels.forEach((item) => {
                    //Hide div containing elements for URL source
                    if (String(item.textContent)
                        .toLowerCase() === 'source' && item.parentElement) {
                        item.parentElement.classList.add('hidden');
                    }
                });
            } else if (titleText === 'cell properties' || titleText === 'row properties') {
                labels.forEach((item) => {
                    //Hide div containing elements for width, height and scope
                    const itemText = String(item.textContent)
                        .toLowerCase();
                    if ((itemText === 'height' || itemText === 'width' || itemText === 'scope') && item.parentElement) {
                        item.parentElement.classList.add('hidden');
                    }
                });
            }
        }
    }
}

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
            //Remove plugins that allow upload of images
            settings.plugins = String(settings.plugins)
                .replace('image ', '');
            settings.images_upload_url = '';
            settings.menu.insert.items = settings.menu.insert.items.replace('image ', '');
        }
        if (noRestoreOnEmpty) {
            settings.autosave_restore_when_empty = false;
        }
        void import('/js/tinymce/tinymce.min.js').then(() => {
            // @ts-expect-error: I can't make TS see tinymce object without turning the file into a module, which does not suit current structure
            // As such I am suppressing a bunch of linters' errors here
            // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
            void tinymce.init(settings)
                // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
                        .then(() => {
                            // @ts-expect-error: I can't make TS see tinymce object without turning the file into a module, which does not suit current structure
                            // As such I am suppressing a bunch of linters' errors here
                            // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access,@typescript-eslint/no-unsafe-assignment
                            const tinyInstance = tinymce.get(id);
                            if (tinyInstance !== null) {
                                // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
                                tinyInstance.on('OpenWindow', () => {
                                    tinyMCEHideInputs();
                                });
                                [
                                    //Editor events
                                    'CloseWindow',
                                    'FormatApply',
                                    'FormatRemove',
                                    'ObjectResized',
                                    'NewBlock',
                                    'Undo',
                                    'Redo',
                                    'SetAttrib',
                                    'NewRow',
                                    'NewCell',
                                    'TableModified',
                                    'Change',
                                    //Plugins' events
                                    'RestoreDraft',
                                    'CommentChange',
                                    'ListMutation',
                                    //Browser events
                                    'input',
                                    'paste',
                                    'cut',
                                    'reset'
                                ].forEach((eventType: string) => {
                                    // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
                                    tinyInstance.on(eventType, () => {
                                        //This is an attempt to ensure we have up-to-date data after modifying source code
                                        tinyMCEtoTextarea(textarea as HTMLTextAreaElement, tinyInstance);
                                    });
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
                    tinyMCEtoTextarea(textarea as HTMLTextAreaElement, tinyInstance);
                } else {
                    // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
                    tinyInstance.save();
                }
            }
        });
    }
}
