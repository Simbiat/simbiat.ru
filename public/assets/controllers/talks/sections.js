export class Sections {
    add_section_form = null;
    add_thread_form = null;
    edit_section_form = null;
    private_section_form = null;
    closed_section_form = null;
    move_section_form = null;
    delete_section_form = null;
    constructor() {
        this.add_section_form = document.querySelector('#add_section_form');
        this.add_thread_form = document.querySelector('#thread_form');
        this.edit_section_form = document.querySelector('#edit_section_form');
        this.private_section_form = document.querySelector('#section_private_form');
        this.closed_section_form = document.querySelector('#section_closed_form');
        this.move_section_form = document.querySelector('#section_move_form');
        this.delete_section_form = document.querySelector('#section_delete_form');
        if (this.add_section_form) {
            submitIntercept(this.add_section_form, this.addSection.bind(this));
        }
        if (this.add_thread_form) {
            submitIntercept(this.add_thread_form, this.addThread.bind(this));
        }
        if (this.edit_section_form) {
            submitIntercept(this.edit_section_form, this.edit.bind(this));
        }
        if (this.private_section_form) {
            submitIntercept(this.private_section_form, this.makePrivate.bind(this));
        }
        if (this.closed_section_form) {
            submitIntercept(this.closed_section_form, this.close.bind(this));
        }
        if (this.move_section_form) {
            submitIntercept(this.move_section_form, this.move.bind(this));
        }
        if (this.delete_section_form) {
            submitIntercept(this.delete_section_form, this.delete.bind(this));
        }
    }
    makePrivate() {
        if (this.private_section_form) {
            const button = this.private_section_form.querySelector('input[type=submit]');
            const form_data = new FormData(this.private_section_form);
            let verb = form_data.get('verb') ?? 'mark_private';
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/sections/${String(form_data.get('section_data[section_id]') ?? '0')}`, form_data, 'json', 'PATCH', AJAX_TIMEOUT, true)
                .then((response) => {
                const data = response;
                if (data.data === true) {
                    if (verb === 'mark_public') {
                        addSnackbar('Section marked as public', 'success');
                    }
                    else {
                        addSnackbar('Section marked as private', 'success');
                    }
                    pageRefresh();
                }
                else {
                    addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                }
                buttonToggle(button);
            });
        }
    }
    move() {
        if (this.move_section_form) {
            const button = this.move_section_form.querySelector('input[type=submit]');
            const form_data = new FormData(this.move_section_form);
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/sections/${String(form_data.get('section_data[section_id]') ?? '0')}`, form_data, 'json', 'PATCH', AJAX_TIMEOUT, true)
                .then((response) => {
                const data = response;
                if (data.data === true) {
                    addSnackbar('Section moved', 'success');
                    pageRefresh();
                }
                else {
                    addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                }
                buttonToggle(button);
            });
        }
    }
    close() {
        if (this.closed_section_form) {
            const button = this.closed_section_form.querySelector('input[type=submit]');
            const form_data = new FormData(this.closed_section_form);
            let verb = form_data.get('verb') ?? 'close';
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/sections/${String(form_data.get('section_data[section_id]') ?? '0')}`, form_data, 'json', 'PATCH', AJAX_TIMEOUT, true)
                .then((response) => {
                const data = response;
                if (data.data === true) {
                    if (verb === 'open') {
                        addSnackbar('Section marked as open', 'success');
                    }
                    else {
                        addSnackbar('Section marked as closed', 'success');
                    }
                    pageRefresh();
                }
                else {
                    addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                }
                buttonToggle(button);
            });
        }
    }
    addSection() {
        if (this.add_section_form) {
            const button = this.add_section_form.querySelector('input[type=submit]');
            const form_data = new FormData(this.add_section_form);
            const icon = this.add_section_form.querySelector('input[type=file]');
            if (icon?.files?.[0]) {
                form_data.append('section_data[icon]', 'true');
            }
            else {
                form_data.append('section_data[icon]', 'false');
            }
            form_data.append('section_data[timezone]', TIMEZONE);
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/sections`, form_data, 'json', 'POST', AJAX_TIMEOUT, true)
                .then((response) => {
                const data = response;
                if (data.data === true) {
                    addSnackbar('Section created. Reloading...', 'success');
                    pageRefresh();
                }
                else {
                    if (data.location) {
                        addSnackbar(data.reason + ` View the section <a href="${data.location}" target="_blank" rel="noopener noreferrer">here</a>.`, 'failure', 0);
                    }
                    else {
                        addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                    }
                }
                buttonToggle(button);
            });
        }
    }
    edit() {
        if (this.edit_section_form) {
            const button = this.edit_section_form.querySelector('input[type=submit]');
            const form_data = new FormData(this.edit_section_form);
            const icon = this.edit_section_form.querySelector('input[type=file]');
            if (icon?.files?.[0]) {
                form_data.append('section_data[icon]', 'true');
            }
            else {
                form_data.append('section_data[icon]', 'false');
            }
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/sections/${String(form_data.get('section_data[section_id]') ?? '0')}`, form_data, 'json', 'PATCH', AJAX_TIMEOUT, true)
                .then((response) => {
                const data = response;
                if (data.data === true) {
                    addSnackbar('Section updated. Reloading...', 'success');
                    pageRefresh();
                }
                else {
                    if (data.location) {
                        addSnackbar(data.reason + ` View the section <a href="${data.location}" target="_blank" rel="noopener noreferrer">here</a>.`, 'failure', 0);
                    }
                    else {
                        addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                    }
                    buttonToggle(button);
                }
            });
        }
    }
    delete() {
        if (this.delete_section_form) {
            if (confirm('This is the last chance to back out.\nIf you press \'OK\' this section will be permanently deleted.\nPress \'Cancel\' to cancel the action.')) {
                const button = this.delete_section_form.querySelector('input[type=submit]');
                const form_data = new FormData(this.delete_section_form);
                buttonToggle(button);
                ajax(`${location.protocol}//${location.host}/api/talks/sections/${String(form_data.get('section_data[section_id]') ?? '0')}`, form_data, 'json', 'DELETE', AJAX_TIMEOUT, true)
                    .then((response) => {
                    const data = response;
                    if (data.data === true) {
                        addSnackbar('Section removed. Redirecting to parent...', 'success');
                        pageRefresh(data.location);
                    }
                    else {
                        addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                    }
                    buttonToggle(button);
                });
            }
        }
    }
    addThread() {
        if (this.add_thread_form) {
            const button = this.add_thread_form.querySelector('input[type=submit]');
            const form_data = new FormData(this.add_thread_form);
            const og_image = this.add_thread_form.querySelector('input[type=file]');
            if (og_image?.files?.[0]) {
                form_data.append('thread_data[og_image]', 'true');
            }
            else {
                form_data.append('thread_data[og_image]', 'false');
            }
            form_data.append('thread_data[timezone]', TIMEZONE);
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/threads`, form_data, 'json', 'POST', AJAX_TIMEOUT, true)
                .then((response) => {
                const data = response;
                if (data.data === true) {
                    if (this.add_thread_form) {
                        const textarea = this.add_thread_form.querySelector('textarea');
                        if (textarea && !empty(textarea.id)) {
                            saveTinyMCE(textarea.id);
                        }
                    }
                    pageRefresh(data.location);
                }
                else {
                    if (data.location) {
                        addSnackbar(data.reason + ` View the thread <a href="${data.location}" target="_blank" rel="noopener noreferrer">here</a>.`, 'failure', 0);
                    }
                    else {
                        addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                    }
                }
                buttonToggle(button);
            });
        }
    }
}
//# sourceMappingURL=sections.js.map