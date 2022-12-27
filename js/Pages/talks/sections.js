export class Sections {
    addSectionForm = null;
    addThreadForm = null;
    editSectionForm = null;
    sectionsList = null;
    deleteSectionButton = null;
    constructor() {
        this.sectionsList = document.getElementById('sections_list');
        this.addSectionForm = document.getElementById('addSectionForm');
        this.addThreadForm = document.getElementById('addThreadForm');
        this.editSectionForm = document.getElementById('editSectionForm');
        this.deleteSectionButton = document.getElementById('delete_section');
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
            document.querySelectorAll('.section_private[id^=section_private_checkbox_]').forEach(item => {
                item.addEventListener('click', (event) => {
                    this.makeSectionPrivate(event);
                });
            });
            document.querySelectorAll('.section_closed[id^=section_closed_checkbox_]').forEach(item => {
                item.addEventListener('click', (event) => {
                    this.closeSection(event);
                });
            });
            document.querySelectorAll('.section_sequence[id^=section_sequence_]').forEach(item => {
                item.addEventListener('change', (event) => {
                    this.orderSection(event);
                });
            });
        }
        if (document.getElementById('threads_list')) {
            document.querySelectorAll('.thread_private[id^=thread_private_checkbox_]').forEach(item => {
                item.addEventListener('click', (event) => {
                    this.makeThreadPrivate(event);
                });
            });
            document.querySelectorAll('.thread_pin[id^=thread_pin_checkbox_]').forEach(item => {
                item.addEventListener('click', (event) => {
                    this.pinThread(event);
                });
            });
        }
    }
    makeSectionPrivate(event) {
        event.preventDefault();
        event.stopPropagation();
        let checkbox = event.target;
        let verb;
        if (checkbox.checked) {
            verb = 'private';
        }
        else {
            verb = 'public';
        }
        buttonToggle(checkbox);
        let sectionId = checkbox.getAttribute('data-section') ?? '';
        ajax(location.protocol + '//' + location.host + '/api/talks/sections/' + sectionId + '/mark' + verb + '/', null, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                if (checkbox.checked) {
                    checkbox.checked = false;
                    new Snackbar('Section marked as public', 'success');
                }
                else {
                    checkbox.checked = true;
                    new Snackbar('Section marked as private', 'success');
                }
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(checkbox);
        });
    }
    closeSection(event) {
        event.preventDefault();
        event.stopPropagation();
        let checkbox = event.target;
        let verb;
        if (checkbox.checked) {
            verb = 'close';
        }
        else {
            verb = 'open';
        }
        buttonToggle(checkbox);
        let sectionId = checkbox.getAttribute('data-section') ?? '';
        ajax(location.protocol + '//' + location.host + '/api/talks/sections/' + sectionId + '/' + verb + '/', null, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                if (checkbox.checked) {
                    checkbox.checked = false;
                    new Snackbar('Section opened', 'success');
                }
                else {
                    checkbox.checked = true;
                    new Snackbar('Section closed', 'success');
                }
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(checkbox);
        });
    }
    orderSection(event) {
        event.preventDefault();
        event.stopPropagation();
        let orderInput = event.target;
        let initialValue = orderInput.getAttribute('data-initial') ?? '0';
        let newValue = orderInput.value ?? '0';
        if (initialValue !== newValue) {
            buttonToggle(orderInput);
            let sectionId = orderInput.getAttribute('data-section') ?? '';
            let formData = new FormData();
            formData.append('order', newValue);
            ajax(location.protocol + '//' + location.host + '/api/talks/sections/' + sectionId + '/order/', formData, 'json', 'PATCH', 60000, true).then(data => {
                if (data.data === true) {
                    orderInput.setAttribute('data-initial', newValue);
                    this.sort();
                    new Snackbar('Order changed. Refresh the page to see changes.', 'success');
                }
                else {
                    orderInput.value = initialValue;
                    new Snackbar(data.reason, 'failure', 10000);
                }
                buttonToggle(orderInput);
            });
        }
    }
    sort() {
        let tbody = this.sectionsList.querySelector('tbody');
        if (tbody) {
            let newBody = tbody.cloneNode();
            let rows = Array.prototype.slice.call(tbody.rows, 0);
            rows = rows.sort(function (a, b) {
                let order = b.querySelector('.section_sequence').value.localeCompare(a.querySelector('.section_sequence').value, undefined, { numeric: true });
                if (order === 0) {
                    return a.querySelector('.section_name a').textContent.localeCompare(b.querySelector('.section_name a').textContent);
                }
                else {
                    return order;
                }
            });
            for (let i = 0; i < rows.length; ++i) {
                newBody.appendChild(rows[i]);
            }
            tbody.parentNode.replaceChild(newBody, tbody);
        }
    }
    addSection() {
        if (this.addSectionForm) {
            let button = this.addSectionForm.querySelector('input[type=submit]');
            let formData = new FormData(this.addSectionForm);
            let icon = this.addSectionForm.querySelector('input[type=file]');
            if (icon && icon.files && icon.files[0]) {
                formData.append('newSection[icon]', 'true');
            }
            else {
                formData.append('newSection[icon]', 'false');
            }
            formData.append('newSection[timezone]', Intl.DateTimeFormat().resolvedOptions().timeZone);
            buttonToggle(button);
            ajax(location.protocol + '//' + location.host + '/api/talks/sections/', formData, 'json', 'POST', 60000, true).then(data => {
                if (data.data === true) {
                    new Snackbar('Section created. Reloading...', 'success');
                    location.reload();
                }
                else {
                    new Snackbar(data.reason, 'failure', 10000);
                }
                buttonToggle(button);
            });
        }
    }
    editSection() {
        if (this.editSectionForm) {
            let button = this.editSectionForm.querySelector('input[type=submit]');
            let formData = new FormData(this.editSectionForm);
            let icon = this.editSectionForm.querySelector('input[type=file]');
            if (icon && icon.files && icon.files[0]) {
                formData.append('curSection[icon]', 'true');
            }
            else {
                formData.append('curSection[icon]', 'false');
            }
            buttonToggle(button);
            ajax(location.protocol + '//' + location.host + '/api/talks/sections/' + (formData.get('curSection[sectionid]') ?? '0') + '/edit/', formData, 'json', 'POST', 60000, true).then(data => {
                if (data.data === true) {
                    new Snackbar('Section updated. Reloading...', 'success');
                    location.reload();
                }
                else {
                    new Snackbar(data.reason, 'failure', 10000);
                    buttonToggle(button);
                }
            });
        }
    }
    deleteSection() {
        if (this.deleteSectionButton) {
            if (confirm('This is the last chance to back out.\nIf you press \'OK\' this section will be permanently deleted.\nPress \'Cancel\' to cancel the action.')) {
                let id = this.deleteSectionButton.getAttribute('data-section');
                if (id) {
                    buttonToggle(this.deleteSectionButton);
                    ajax(location.protocol + '//' + location.host + '/api/talks/sections/' + id + '/delete/', null, 'json', 'DELETE', 60000, true).then(data => {
                        if (data.data === true) {
                            new Snackbar('Section removed. Redirecting to parent...', 'success');
                            window.location.href = data.location;
                        }
                        else {
                            new Snackbar(data.reason, 'failure', 10000);
                        }
                        buttonToggle(this.deleteSectionButton);
                    });
                }
            }
        }
    }
    addThread() {
        if (this.addThreadForm) {
            let button = this.addThreadForm.querySelector('input[type=submit]');
            let formData = new FormData(this.addThreadForm);
            let ogimage = this.addThreadForm.querySelector('input[type=file]');
            if (ogimage && ogimage.files && ogimage.files[0]) {
                formData.append('newThread[ogimage]', 'true');
            }
            else {
                formData.append('newThread[ogimage]', 'false');
            }
            formData.append('newThread[timezone]', Intl.DateTimeFormat().resolvedOptions().timeZone);
            buttonToggle(button);
            ajax(location.protocol + '//' + location.host + '/api/talks/threads/', formData, 'json', 'POST', 60000, true).then(data => {
                if (data.data === true) {
                    let textarea = this.addThreadForm.querySelector('textarea');
                    if (textarea && textarea.id) {
                        saveTinyMCE(textarea.id);
                    }
                    new Snackbar('Thread created. Reloading...', 'success');
                    window.location.href = data.location;
                }
                else {
                    new Snackbar(data.reason, 'failure', 10000);
                }
                buttonToggle(button);
            });
        }
    }
    makeThreadPrivate(event) {
        event.preventDefault();
        event.stopPropagation();
        let checkbox = event.target;
        let verb;
        if (checkbox.checked) {
            verb = 'private';
        }
        else {
            verb = 'public';
        }
        buttonToggle(checkbox);
        let threadId = checkbox.getAttribute('data-thread') ?? '';
        ajax(location.protocol + '//' + location.host + '/api/talks/threads/' + threadId + '/mark' + verb + '/', null, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                if (checkbox.checked) {
                    checkbox.checked = false;
                    new Snackbar('Thread marked as public', 'success');
                }
                else {
                    checkbox.checked = true;
                    new Snackbar('Thread marked as private', 'success');
                }
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(checkbox);
        });
    }
    pinThread(event) {
        event.preventDefault();
        event.stopPropagation();
        let checkbox = event.target;
        let verb;
        if (checkbox.checked) {
            verb = 'pin';
        }
        else {
            verb = 'unpin';
        }
        buttonToggle(checkbox);
        let threadId = checkbox.getAttribute('data-thread') ?? '';
        ajax(location.protocol + '//' + location.host + '/api/talks/threads/' + threadId + '/' + verb + '/', null, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                if (checkbox.checked) {
                    checkbox.checked = false;
                    new Snackbar('Thread unpinned', 'success');
                }
                else {
                    checkbox.checked = true;
                    new Snackbar('Thread pinned', 'success');
                }
            }
            else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(checkbox);
        });
    }
}
//# sourceMappingURL=sections.js.map