export class Sections {
    addSectionForm = null;
    addThreadForm = null;
    editSectionForm = null;
    sectionsList = null;
    deleteSectionButton = null;
    constructor() {
        this.sectionsList = document.querySelector('#sections_list');
        this.addSectionForm = document.querySelector('#addSectionForm');
        this.addThreadForm = document.querySelector('#addThreadForm');
        this.editSectionForm = document.querySelector('#editSectionForm');
        this.deleteSectionButton = document.querySelector('#delete_section');
        if (this.addSectionForm) {
            submitIntercept(this.addSectionForm, this.addSection.bind(this));
        }
        if (this.addThreadForm) {
            submitIntercept(this.addThreadForm, this.addThread.bind(this));
        }
        if (this.editSectionForm) {
            submitIntercept(this.editSectionForm, this.editSection.bind(this));
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
        const sectionId = checkbox.getAttribute('data-section') ?? '';
        ajax(`${location.protocol}//${location.host}/api/talks/sections/${sectionId}/mark${verb}`, null, 'json', 'PATCH', ajaxTimeout, true)
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
                addSnackbar(data.reason, 'failure', snackbarFailLife);
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
        const sectionId = checkbox.getAttribute('data-section') ?? '';
        ajax(`${location.protocol}//${location.host}/api/talks/sections/${sectionId}/${verb}`, null, 'json', 'PATCH', ajaxTimeout, true)
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
                addSnackbar(data.reason, 'failure', snackbarFailLife);
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
            const sectionId = orderInput.getAttribute('data-section') ?? '';
            const formData = new FormData();
            formData.append('order', newValue);
            ajax(`${location.protocol}//${location.host}/api/talks/sections/${sectionId}/order`, formData, 'json', 'PATCH', ajaxTimeout, true)
                .then((response) => {
                const data = response;
                if (data.data === true) {
                    orderInput.setAttribute('data-initial', newValue);
                    this.sort();
                    addSnackbar('Order updated', 'success');
                }
                else {
                    orderInput.value = initialValue;
                    addSnackbar(data.reason, 'failure', snackbarFailLife);
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
        if (this.addSectionForm) {
            const button = this.addSectionForm.querySelector('input[type=submit]');
            const formData = new FormData(this.addSectionForm);
            const icon = this.addSectionForm.querySelector('input[type=file]');
            if (icon?.files?.[0]) {
                formData.append('newSection[icon]', 'true');
            }
            else {
                formData.append('newSection[icon]', 'false');
            }
            formData.append('newSection[timezone]', timezone);
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/sections`, formData, 'json', 'POST', ajaxTimeout, true)
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
                        addSnackbar(data.reason, 'failure', snackbarFailLife);
                    }
                }
                buttonToggle(button);
            });
        }
    }
    editSection() {
        if (this.editSectionForm) {
            const button = this.editSectionForm.querySelector('input[type=submit]');
            const formData = new FormData(this.editSectionForm);
            const icon = this.editSectionForm.querySelector('input[type=file]');
            if (icon?.files?.[0]) {
                formData.append('curSection[icon]', 'true');
            }
            else {
                formData.append('curSection[icon]', 'false');
            }
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/sections/${String(formData.get('curSection[sectionid]') ?? '0')}/edit`, formData, 'json', 'POST', ajaxTimeout, true)
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
                        addSnackbar(data.reason, 'failure', snackbarFailLife);
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
                    ajax(`${location.protocol}//${location.host}/api/talks/sections/${id}/delete`, null, 'json', 'DELETE', ajaxTimeout, true)
                        .then((response) => {
                        const data = response;
                        if (data.data === true) {
                            addSnackbar('Section removed. Redirecting to parent...', 'success');
                            window.location.href = data.location;
                        }
                        else {
                            addSnackbar(data.reason, 'failure', snackbarFailLife);
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
        if (this.addThreadForm) {
            const button = this.addThreadForm.querySelector('input[type=submit]');
            const formData = new FormData(this.addThreadForm);
            const ogimage = this.addThreadForm.querySelector('input[type=file]');
            if (ogimage?.files?.[0]) {
                formData.append('newThread[ogimage]', 'true');
            }
            else {
                formData.append('newThread[ogimage]', 'false');
            }
            formData.append('newThread[timezone]', timezone);
            buttonToggle(button);
            ajax(`${location.protocol}//${location.host}/api/talks/threads`, formData, 'json', 'POST', ajaxTimeout, true)
                .then((response) => {
                const data = response;
                if (data.data === true) {
                    if (this.addThreadForm) {
                        const textarea = this.addThreadForm.querySelector('textarea');
                        if (textarea && !empty(textarea.id)) {
                            saveTinyMCE(textarea.id);
                        }
                    }
                    addSnackbar('Thread created. Reloading...', 'success');
                    window.location.href = data.location;
                }
                else {
                    if (data.location) {
                        addSnackbar(data.reason + ` View the thread <a href="${data.location}" target="_blank">here</a>.`, 'failure', 0);
                    }
                    else {
                        addSnackbar(data.reason, 'failure', snackbarFailLife);
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
        const threadId = checkbox.getAttribute('data-thread') ?? '';
        ajax(`${location.protocol}//${location.host}/api/talks/threads/${threadId}/mark${verb}`, null, 'json', 'PATCH', ajaxTimeout, true)
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
                addSnackbar(data.reason, 'failure', snackbarFailLife);
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
        const threadId = checkbox.getAttribute('data-thread') ?? '';
        ajax(`${location.protocol}//${location.host}/api/talks/threads/${threadId}/${verb}`, null, 'json', 'PATCH', ajaxTimeout, true)
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
                addSnackbar(data.reason, 'failure', snackbarFailLife);
            }
            buttonToggle(checkbox);
        });
    }
}
//# sourceMappingURL=sections.js.map