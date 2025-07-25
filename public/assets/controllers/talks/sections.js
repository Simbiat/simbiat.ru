export class Sections {
    add_section_form = null;
    add_thread_form = null;
    edit_section_form = null;
    sectionsList = null;
    deleteSectionButton = null;
    constructor() {
        this.sectionsList = document.querySelector('#sections_list');
        this.add_section_form = document.querySelector('#add_section_form');
        this.add_thread_form = document.querySelector('#add_thread_form');
        this.edit_section_form = document.querySelector('#edit_section_form');
        this.deleteSectionButton = document.querySelector('#delete_section');
        if (this.add_section_form) {
            submitIntercept(this.add_section_form, this.addSection.bind(this));
        }
        if (this.add_thread_form) {
            submitIntercept(this.add_thread_form, this.addThread.bind(this));
        }
        if (this.edit_section_form) {
            submitIntercept(this.edit_section_form, this.editSection.bind(this));
        }
        if (this.deleteSectionButton) {
            this.deleteSectionButton.addEventListener('click', () => {
                this.deleteSection();
            });
        }
        if (this.sectionsList) {
            document.querySelectorAll('.section_private[id^=section_private_checkbox_]')
                .forEach((item) => {
                item.addEventListener('click', (event) => {
                    Sections.makeSectionPrivate(event);
                });
            });
            document.querySelectorAll('.section_closed[id^=section_closed_checkbox_]')
                .forEach((item) => {
                item.addEventListener('click', (event) => {
                    Sections.closeSection(event);
                });
            });
            document.querySelectorAll('.section_sequence[id^=section_sequence_]')
                .forEach((item) => {
                item.addEventListener('change', (event) => {
                    this.orderSection(event);
                });
            });
        }
        if (document.querySelector('#threads_list')) {
            document.querySelectorAll('.thread_private[id^=thread_private_checkbox_]')
                .forEach((item) => {
                item.addEventListener('click', (event) => {
                    Sections.makeThreadPrivate(event);
                });
            });
            document.querySelectorAll('.thread_pin[id^=thread_pin_checkbox_]')
                .forEach((item) => {
                item.addEventListener('click', (event) => {
                    Sections.pinThread(event);
                });
            });
        }
    }
    static makeSectionPrivate(event) {
        event.preventDefault();
        event.stopPropagation();
        const checkbox = event.target;
        let verb;
        if (checkbox.checked) {
            verb = 'private';
        }
        else {
            verb = 'public';
        }
        buttonToggle(checkbox);
        const section_id = checkbox.getAttribute('data-section') ?? '';
        ajax(`${location.protocol}//${location.host}/api/talks/sections/${section_id}/mark${verb}`, null, 'json', 'PATCH', AJAX_TIMEOUT, true)
            .then((response) => {
            const data = response;
            if (data.data === true) {
                if (checkbox.checked) {
                    checkbox.checked = false;
                    addSnackbar('Section marked as public', 'success');
                }
                else {
                    checkbox.checked = true;
                    addSnackbar('Section marked as private', 'success');
                }
            }
            else {
                addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
            }
            buttonToggle(checkbox);
        });
    }
    static closeSection(event) {
        event.preventDefault();
        event.stopPropagation();
        const checkbox = event.target;
        let verb;
        if (checkbox.checked) {
            verb = 'close';
        }
        else {
            verb = 'open';
        }
        buttonToggle(checkbox);
        const section_id = checkbox.getAttribute('data-section') ?? '';
        ajax(`${location.protocol}//${location.host}/api/talks/sections/${section_id}/${verb}`, null, 'json', 'PATCH', AJAX_TIMEOUT, true)
            .then((response) => {
            const data = response;
            if (data.data === true) {
                if (checkbox.checked) {
                    checkbox.checked = false;
                    addSnackbar('Section opened', 'success');
                }
                else {
                    checkbox.checked = true;
                    addSnackbar('Section closed', 'success');
                }
            }
            else {
                addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
            }
            buttonToggle(checkbox);
        });
    }
    orderSection(event) {
        event.preventDefault();
        event.stopPropagation();
        const orderInput = event.target;
        const initialValue = orderInput.getAttribute('data-initial') ?? '0';
        const newValue = empty(orderInput.value) ? '0' : orderInput.value;
        if (initialValue !== newValue) {
            buttonToggle(orderInput);
            const section_id = orderInput.getAttribute('data-section') ?? '';
            const formData = new FormData();
            formData.append('order', newValue);
            ajax(`${location.protocol}//${location.host}/api/talks/sections/${section_id}/order`, formData, 'json', 'PATCH', AJAX_TIMEOUT, true)
                .then((response) => {
                const data = response;
                if (data.data === true) {
                    orderInput.setAttribute('data-initial', newValue);
                    this.sort();
                    addSnackbar('Order updated', 'success');
                }
                else {
                    orderInput.value = initialValue;
                    addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                }
                buttonToggle(orderInput);
            });
        }
    }
    sort() {
        if (this.sectionsList) {
            const tbody = this.sectionsList.querySelector('tbody');
            if (tbody) {
                const newBody = tbody.cloneNode();
                let rows = Array.prototype.slice.call(tbody.rows, 0);
                rows = rows.sort((a, b) => {
                    const aSequence = a.querySelector('.section_sequence');
                    const bSequence = b.querySelector('.section_sequence');
                    const aText = a.querySelector('.section_name a');
                    const bText = b.querySelector('.section_name a');
                    let order = 0;
                    if (aSequence && bSequence) {
                        order = bSequence.value.localeCompare(aSequence.value, undefined, { 'numeric': true });
                    }
                    if (order === 0) {
                        if (aText && bText) {
                            return String(aText.textContent)
                                .localeCompare(String(bText.textContent));
                        }
                    }
                    return order;
                });
                for (const row of rows) {
                    newBody.appendChild(row);
                }
                tbody.parentNode.replaceChild(newBody, tbody);
            }
        }
    }
    addSection() {
        if (this.add_section_form) {
            const button = this.add_section_form.querySelector('input[type=submit]');
            const formData = new FormData(this.add_section_form);
            const icon = this.add_section_form.querySelector('input[type=file]');
            if (icon?.files?.[0]) {
                formData.append('new_section[icon]', 'true');
            }
            else {
                formData.append('new_section[icon]', 'false');
            }
            formData.append('new_section[timezone]', TIMEZONE);
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/sections`, formData, 'json', 'POST', AJAX_TIMEOUT, true)
                .then((response) => {
                const data = response;
                if (data.data === true) {
                    addSnackbar('Section created. Reloading...', 'success');
                    pageRefresh();
                }
                else {
                    if (data.location) {
                        addSnackbar(data.reason + ` View the section <a href="${data.location}" target="_blank">here</a>.`, 'failure', 0);
                    }
                    else {
                        addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                    }
                }
                buttonToggle(button);
            });
        }
    }
    editSection() {
        if (this.edit_section_form) {
            const button = this.edit_section_form.querySelector('input[type=submit]');
            const formData = new FormData(this.edit_section_form);
            const icon = this.edit_section_form.querySelector('input[type=file]');
            if (icon?.files?.[0]) {
                formData.append('cur_section[icon]', 'true');
            }
            else {
                formData.append('cur_section[icon]', 'false');
            }
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/sections/${String(formData.get('cur_section[section_id]') ?? '0')}/edit`, formData, 'json', 'POST', AJAX_TIMEOUT, true)
                .then((response) => {
                const data = response;
                if (data.data === true) {
                    addSnackbar('Section updated. Reloading...', 'success');
                    pageRefresh();
                }
                else {
                    if (data.location) {
                        addSnackbar(data.reason + ` View the section <a href="${data.location}" target="_blank">here</a>.`, 'failure', 0);
                    }
                    else {
                        addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                    }
                    buttonToggle(button);
                }
            });
        }
    }
    deleteSection() {
        if (this.deleteSectionButton) {
            if (confirm('This is the last chance to back out.\nIf you press \'OK\' this section will be permanently deleted.\nPress \'Cancel\' to cancel the action.')) {
                const id = this.deleteSectionButton.getAttribute('data-section') ?? '';
                if (!empty(id)) {
                    buttonToggle(this.deleteSectionButton);
                    ajax(`${location.protocol}//${location.host}/api/talks/sections/${id}/delete`, null, 'json', 'DELETE', AJAX_TIMEOUT, true)
                        .then((response) => {
                        const data = response;
                        if (data.data === true) {
                            addSnackbar('Section removed. Redirecting to parent...', 'success');
                            window.location.assign(encodeURI(data.location));
                        }
                        else {
                            addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                        }
                        if (this.deleteSectionButton) {
                            buttonToggle(this.deleteSectionButton);
                        }
                    });
                }
            }
        }
    }
    addThread() {
        if (this.add_thread_form) {
            const button = this.add_thread_form.querySelector('input[type=submit]');
            const formData = new FormData(this.add_thread_form);
            const og_image = this.add_thread_form.querySelector('input[type=file]');
            if (og_image?.files?.[0]) {
                formData.append('new_thread[og_image]', 'true');
            }
            else {
                formData.append('new_thread[og_image]', 'false');
            }
            formData.append('new_thread[timezone]', TIMEZONE);
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/threads`, formData, 'json', 'POST', AJAX_TIMEOUT, true)
                .then((response) => {
                const data = response;
                if (data.data === true) {
                    if (this.add_thread_form) {
                        const textarea = this.add_thread_form.querySelector('textarea');
                        if (textarea && !empty(textarea.id)) {
                            saveTinyMCE(textarea.id);
                        }
                    }
                    addSnackbar('Thread created. Reloading...', 'success');
                    window.location.assign(encodeURI(data.location));
                }
                else {
                    if (data.location) {
                        addSnackbar(data.reason + ` View the thread <a href="${data.location}" target="_blank">here</a>.`, 'failure', 0);
                    }
                    else {
                        addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                    }
                }
                buttonToggle(button);
            });
        }
    }
    static makeThreadPrivate(event) {
        event.preventDefault();
        event.stopPropagation();
        const checkbox = event.target;
        let verb;
        if (checkbox.checked) {
            verb = 'private';
        }
        else {
            verb = 'public';
        }
        buttonToggle(checkbox);
        const thread_id = checkbox.getAttribute('data-thread') ?? '';
        ajax(`${location.protocol}//${location.host}/api/talks/threads/${thread_id}/mark${verb}`, null, 'json', 'PATCH', AJAX_TIMEOUT, true)
            .then((response) => {
            const data = response;
            if (data.data === true) {
                if (checkbox.checked) {
                    checkbox.checked = false;
                    addSnackbar('Thread marked as public', 'success');
                }
                else {
                    checkbox.checked = true;
                    addSnackbar('Thread marked as private', 'success');
                }
            }
            else {
                addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
            }
            buttonToggle(checkbox);
        });
    }
    static pinThread(event) {
        event.preventDefault();
        event.stopPropagation();
        const checkbox = event.target;
        let verb;
        if (checkbox.checked) {
            verb = 'pin';
        }
        else {
            verb = 'unpin';
        }
        buttonToggle(checkbox);
        const thread_id = checkbox.getAttribute('data-thread') ?? '';
        ajax(`${location.protocol}//${location.host}/api/talks/threads/${thread_id}/${verb}`, null, 'json', 'PATCH', AJAX_TIMEOUT, true)
            .then((response) => {
            const data = response;
            if (data.data === true) {
                if (checkbox.checked) {
                    checkbox.checked = false;
                    addSnackbar('Thread unpinned', 'success');
                }
                else {
                    checkbox.checked = true;
                    addSnackbar('Thread pinned', 'success');
                }
            }
            else {
                addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
            }
            buttonToggle(checkbox);
        });
    }
}
//# sourceMappingURL=sections.js.map