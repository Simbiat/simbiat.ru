/**
 * @file Preparing and customizing TinyMCE forms.
 */
import { Input } from '../NativeElements/Input.mts';
import { empty } from './Helpers.mts';
import type { TinyMCE, Editor, RawEditorOptions } from 'tinymce/tinymce.d.ts';

declare const tinymce: TinyMCE;

const CUSTOM_COLOR_MAP = new Map<string, string>([
  ['#17141F', 'body'],
  ['#19424D', 'dark_border'],
  ['#231F2E', 'block'],
  ['#266373', 'light_border'],
  ['#2E293D', 'article'],
  ['#808080', 'disabled'],
  ['#8AE59C', 'success'],
  ['#9AD4EA', 'interactive'],
  ['#E6B63D', 'warning'],
  ['#F3A0B6', 'failure'],
  ['#F5F0F0', 'text'],
]);

const TINY_SETTINGS: RawEditorOptions = {
  automatic_uploads: true,
  autosave_ask_before_unload: true,
  autosave_interval: '5s',
  autosave_restore_when_empty: true,
  base_url: '/tinymce/',
  block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6;',
  block_unsupported_drop: true,
  branding: true,
  browser_spellcheck: true,
  color_map: [...CUSTOM_COLOR_MAP.entries()].flat(),
  content_css: '/assets/styles/tinymce.css',
  content_security_policy: 'default-src \'self\'',
  contextmenu: 'emoticons link image',
  custom_colors: false,
  default_link_target: '_blank',
  document_base_url: `${window.location.protocol}//${window.location.hostname}/`,
  emoticons_database: 'emojis',
  entity_encoding: 'raw',
  file_picker_types: 'image',
  font_formats: '',
  fontsize_formats: '',
  formats: {
    aligncenter: {
      classes: 'tiny_align_center',
      remove: 'none',
      selector: 'strong,em,sub,sup,s,a,time,p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,audio,video,blockquote',
    },
    alignjustify: {
      classes: 'tiny_align_justify',
      remove: 'none',
      selector: 'strong,em,sub,sup,s,a,time,p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,audio,video,blockquote',
    },
    alignleft: {
      classes: 'tiny_align_left',
      remove: 'none',
      selector: 'strong,em,sub,sup,s,a,time,p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,audio,video,blockquote',
    },
    alignright: {
      classes: 'tiny_align_right',
      remove: 'none',
      selector: 'strong,em,sub,sup,s,a,time,p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,audio,video,blockquote',
    },
    forecolor: {
      attributes: {
        /**
         * Converting TinyMCE formats to respective classes.
         * @param vars - Format to convert.
         */
        class: (vars?): string => {
          const color_key = vars?.['value'];
          if (color_key !== null && typeof color_key !== 'undefined') {
            const mapped = CUSTOM_COLOR_MAP.get(color_key) ?? '';
            return mapped === '' ? '' : `tiny_color_${String(mapped ?? color_key)}`;
          }
          return '';
        },
      },
      inline: 'span',
      remove: 'none',
    },
    hilitecolor: {
      attributes: {
        /**
         * Converting TinyMCE formats to respective classes.
         * @param vars - Format to convert.
         */
        class: (vars?): string => {
          const color_key = vars?.['value'];
          if (color_key !== null && typeof color_key !== 'undefined') {
            const mapped = CUSTOM_COLOR_MAP.get(color_key) ?? '';
            return mapped === '' ? '' : `tiny_bg_color_${String(mapped ?? color_key)}`;
          }
          return '';
        },
      },
      inline: 'span',
      remove: 'none',
    },
    list_circle: {
      classes: 'tiny_list_circle',
      remove: 'none',
      selector: 'ul,ul>li',
    },
    list_decimal: {
      classes: 'tiny_list_decimal',
      remove: 'none',
      selector: 'ol,ol>li',
    },
    list_decimal_leading_zero: {
      classes: 'tiny_list_decimal_leading_zero',
      remove: 'none',
      selector: 'ol,ol>li',
    },
    list_disc: {
      classes: 'tiny_list_disc',
      remove: 'none',
      selector: 'ul,ul>li',
    },
    list_disclosure_closed: {
      classes: 'tiny_list_disclosure_closed',
      remove: 'none',
      selector: 'ul,ul>li',
    },
    list_disclosure_open: {
      classes: 'iny_list_disclosure_open',
      remove: 'none',
      selector: 'ul,ul>li',
    },
    list_lower_alpha: {
      classes: 'tiny_list_lower_alpha',
      remove: 'none',
      selector: 'ol,ol>li',
    },
    list_lower_greek: {
      classes: 'tiny_list_lower_greek',
      remove: 'none',
      selector: 'ol,ol>li',
    },
    list_lower_roman: {
      classes: 'tiny_list_lower_roman',
      remove: 'none',
      selector: 'ol,ol>li',
    },
    list_square: {
      classes: 'tiny_list_square',
      remove: 'none',
      selector: 'ul,ul>li',
    },
    list_upper_alpha: {
      classes: 'tiny_list_upper_alpha',
      remove: 'none',
      selector: 'ol,ol>li',
    },
    list_upper_roman: {
      classes: 'tiny_list_upper_roman',
      remove: 'none',
      selector: 'ol,ol>li',
    },
    underline: {
      classes: 'tiny_underline',
      inline: 'span',
      remove: 'none',
    },
    valignbottom: {
      classes: 'tiny_valign_bottom',
      remove: 'none',
      selector: 'td,th,table',
    },
    valignmiddle: {
      classes: 'tiny_valign_middle',
      remove: 'none',
      selector: 'td,th,table',
    },
    valigntop: {
      classes: 'tiny_valign_top',
      remove: 'none',
      selector: 'td,th,table',
    },
  },
  hidden_input: false,
  image_advtab: false,
  image_caption: false,
  image_class_list: [
    {
      title: 'Default',
      value: 'w50pc middle block gallery_zoom',
    },
    {
      menu: [
        {
          title: 'Quarter width',
          value: 'w25pc middle block gallery_zoom',
        },
        {
          title: 'Half width',
          value: 'w50pc middle block gallery_zoom',
        },
        {
          title: '3 quarters width',
          value: 'w75pc middle block gallery_zoom',
        },
        {
          title: 'Full width',
          value: 'w100pc middle block gallery_zoom',
        },
      ],
      title: 'Block',
    },
    {
      menu: [
        {
          title: 'Quarter width',
          value: 'w25pc middle gallery_zoom',
        },
        {
          title: 'Half width',
          value: 'w50pc middle gallery_zoom',
        },
        {
          title: '3 quarters width',
          value: 'w75pc middle gallery_zoom',
        },
        {
          title: 'Full width',
          value: 'w100pc middle gallery_zoom',
        },
      ],
      title: 'Inline',
    },
    {
      title: 'Icon',
      value: 'link_icon',
    },
  ],
  image_description: true,
  image_dimensions: false,
  image_title: false,
  image_uploadtab: true,
  images_file_types: 'jpeg,jpg,png,gif,bmp,webp,svg',
  images_reuse_filename: true,
  images_upload_credentials: true,
  images_upload_url: '/api/upload/',
  insertdatetime_element: true,
  invalid_elements: 'acronym,applet,area,aside,base,basefont,bgsound,big,blink,body,button,canvas,center,content,datalist,dialog,dir,embed,fieldset,figure,figcaption,font,footer,form,frame,frameset,head,header,hgroup,html,iframe,input,image,keygen,legend,link,main,map,marquee,menuitem,meter,nav,nobr,noembed,noframes,noscript,object,optgroup,option,param,picture,plaintext,portal,pre,progress,rb,rp,rt,rtc,ruby,script,select,selectmenu,shadow,slot,strike,style,spacer,template,textarea,title,tt,xmp',
  invalid_styles: 'font-size line-height',
  license_key: 'gpl',
  lineheight_formats: '',
  link_assume_external_targets: 'https',
  link_context_toolbar: true,
  link_default_protocol: 'https',
  link_target_list: [
    {
      title: 'New window',
      value: '_blank',
    },
    {
      title: 'Current window',
      value: '_self',
    },
  ],
  link_title: false,
  lists_indent_on_tab: true,
  menu: {
    edit: {
      items: 'undo redo | cut copy paste pastetext | selectall | searchreplace',
      title: 'Edit',
    },
    file: {
      items: 'newdocument restoredraft',
      title: 'File',
    },
    format: {
      items: 'underline strikethrough superscript subscript | align | styles',
      title: 'Format',
    },
    help: {
      items: 'help wordcount',
      title: 'Help',
    },
    insert: {
      items: 'link image codeformat | emoticons charmap hr | insertdatetime',
      title: 'Insert',
    },
    table: {
      items: 'inserttable | cell row column | deletetable',
      title: 'Table',
    },
    view: {
      items: 'code preview | visualaid visualchars visualblocks | fullscreen',
      title: 'View',
    },
  },
  menubar: 'file edit view format insert table help',
  object_resizing: false,
  paste_block_drop: true,
  paste_data_images: false,
  paste_remove_styles_if_webkit: true,
  paste_webkit_styles: 'none',
  plugins: 'autolink autosave charmap code emoticons fullscreen help image insertdatetime link lists preview quickbars searchreplace table visualblocks visualchars wordcount',
  promotion: false,
  quickbars_insert_toolbar: false,
  readonly: false,
  referrer_policy: 'no-referrer',
  relative_urls: false,
  remove_script_host: true,
  remove_trailing_brs: true,
  resize_img_proportional: true,
  schema: 'html5-strict',
  selector: 'textarea.tinymce',
  skin: 'oxide-dark',
  style_formats: [
    {
      items: [
        {
          format: 'list_decimal',
          title: 'Decimal (default)',
        },
        {
          format: 'list_decimal_leading_zero',
          title: 'Decimal, leading zero',
        },
        {
          format: 'list_lower_alpha',
          title: 'Lower Latin',
        },
        {
          format: 'list_lower_greek',
          title: 'Lower Greek',
        },
        {
          format: 'list_lower_roman',
          title: 'Lower Roman',
        },
        {
          format: 'list_upper_alpha',
          title: 'Upper Latin',
        },
        {
          format: 'list_upper_roman',
          title: 'Upper Roman',
        },
      ],
      title: 'Ordered lists',
    },
    {
      items: [
        {
          format: 'list_circle',
          title: 'Circle',
        },
        {
          format: 'list_disc',
          title: 'Disc (default)',
        },
        {
          format: 'list_disclosure_closed',
          title: 'Disclosure closed',
        },
        {
          format: 'list_disclosure_open',
          title: 'Disclosure open',
        },
        {
          format: 'list_square',
          title: 'Square',
        },
      ],
      title: 'Unordered lists',
    },
  ],
  style_formats_autohide: true,
  table_advtab: false,
  table_appearance_options: false,
  table_border_styles: [
    {
      title: 'Solid',
      value: 'solid',
    },
  ],
  table_border_widths: [
    {
      title: 'default',
      value: '0.125rem',
    },
  ],
  table_cell_advtab: false,
  table_default_attributes: {},
  table_header_type: 'sectionCells',
  table_resize_bars: false,
  table_row_advtab: false,
  table_sizing_mode: 'relative',
  table_style_by_css: false,
  table_toolbar: 'tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol | tabledelete',
  theme_advanced_default_foreground_color: '#F5F0F0',
  toolbar: 'undo redo | blocks | bold italic | forecolor backcolor | blockquote bullist numlist | removeformat',
  toolbar_mode: 'wrap',
  valid_styles: {},
  visual: true,
  visualblocks_default_state: false,
};

/**
 * Save TinyMCE content to a textarea.
 * @param textarea - Textarea element.
 * @param tiny_instance - TinyMCE instance.
 */
function tinyMCEtoTextarea(textarea: HTMLTextAreaElement, tiny_instance: Editor): void {
  textarea.value = String(tiny_instance.getContent());
  textarea.dispatchEvent(new Event('input'));
}

/**
 * Customize image upload to prevent upload of files through URLs and some manual customizations.
 */
function tinyMCEHideInputs(): void {
  // Get dialog
  const dialog = document.querySelector<HTMLDivElement>('div[role=dialog].tox-dialog');
  if (dialog) {
    // Get title
    const title = dialog.querySelector<HTMLHeadingElement>('h1.tox-dialog__title');
    if (title) {
      // Get the labels
      const labels = dialog.querySelectorAll<HTMLLabelElement>('label');
      // Get the name of the dialog
      const title_text = String(title.textContent)
        .toLowerCase();
      if (title_text === 'insert/edit image') {
        // Input tags standardization
        const inputs = dialog.querySelectorAll<HTMLInputElement>('input[type="file"]');
        if (!empty(inputs)) {
          for (const input of inputs) {
            Input.init(input);
          }
        }
      } else if (title_text === 'cell properties' || title_text === 'row properties') {
        for (const item of labels) {
          // Hide div containing elements for width, height and scope
          const item_text = String(item.textContent)
            .toLowerCase();
          if ((item_text === 'height' || item_text === 'width' || item_text === 'scope') && item.parentElement) {
            item.parentElement.classList.add('hidden');
          }
        }
      }
    }
  }
}

/**
 * Load TinyMCE object.
 * @param id - ID of textarea to attach TinyMCE to.
 * @param no_media - Whether media is allowed in the editor.
 * @param no_restore_on_empty - Whether to allow restoration of previously saved data.
 */
export async function loadTinyMCE(id: string, no_media = true, no_restore_on_empty = false): Promise<void> {
  if ((/^\s*$/v).exec(id)) {
    return;
  }
  const textarea = document.querySelector<HTMLTextAreaElement>(`#${id}`);
  if (textarea) {
    const settings = TINY_SETTINGS;
    settings.selector = `#${id}`;
    if (no_media) {
      // Remove plugins that allow upload of images
      settings.plugins = String(settings.plugins)
        .replace('image ', '');
      settings.images_upload_url = '';
      if (settings.menu?.['insert']) {
        settings.menu['insert'].items = settings.menu['insert'].items.replace('image ', '');
      }
    }
    if (no_restore_on_empty) {
      settings['autosave_restore_when_empty'] = false;
    }
    // I have to use absolute path here due to how TinyMCE is served, and so that it's served "universally"
    // eslint-disable-next-line import/no-absolute-path
    await import('/tinymce/tinymce.min.js');
    const instances: Editor[] = await tinymce.init(settings);
    const tiny_instance = instances[0];
    if (!tiny_instance) {
      return;
    }
    tiny_instance.on('OpenWindow', () => {
      tinyMCEHideInputs();
    });
    for (const event_type of [
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
      // Plugins' events
      'RestoreDraft',
      'CommentChange',
      'ListMutation',
      // Browser events
      'input',
      'paste',
      'cut',
      'reset',
    ]) {
      tiny_instance.on(event_type, () => {
        //This is an attempt to ensure we have up-to-date data after modifying source code
        tinyMCEtoTextarea(textarea, tiny_instance);
      });
    }
  }
}

/**
 * Save the text in TinyMCE.
 * @param id - ID of the editor instance.
 * @param textarea_only - Whether to save only to textarea without saving to TinyMCE object.
 */
export async function saveTinyMCE(id: string, textarea_only = false): Promise<void> {
  if ((/^\s*$/v).exec(id)) {
    return;
  }
  const textarea = document.querySelector<HTMLTextAreaElement>(`#${id}`);
  if (textarea !== null) {
    // I have to use absolute path here due to how TinyMCE is served, and so that it's served "universally"
    // eslint-disable-next-line import/no-absolute-path
    await import('/tinymce/tinymce.min.js');
    const tiny_instance = tinymce.get(id);
    if (tiny_instance !== null) {
      if (textarea_only) {
        tinyMCEtoTextarea(textarea, tiny_instance);
      } else {
        tiny_instance.save();
      }
    }
  }
}
