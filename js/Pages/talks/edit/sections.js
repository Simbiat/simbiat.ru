export class editSections {
    addSectionForm = null;
    editSectionForm = null;
    sectionsList = null;
    deleteButton = null;
    constructor() {
        this.sectionsList = document.getElementById('sections_list');
        this.addSectionForm = document.getElementById('addSectionForm');
        this.editSectionForm = document.getElementById('editSectionForm');
        this.deleteButton = document.getElementById('delete_section');
        if (this.addSectionForm) {
            submitIntercept(this.addSectionForm, this.add.bind(this));
        }
        if (this.editSectionForm) {
            submitIntercept(this.editSectionForm, this.edit.bind(this));
        }
        if (this.deleteButton) {
            this.deleteButton.addEventListener('click', () => {
                this.delete();
            });
        }
        if (this.sectionsList) {
            document.querySelectorAll('.section_private[id^=section_private_checkbox_]').forEach(item => {
                item.addEventListener('click', (event) => {
                    this.makePrivate(event);
                });
            });
            document.querySelectorAll('.section_closed[id^=section_closed_checkbox_]').forEach(item => {
                item.addEventListener('click', (event) => {
                    this.close(event);
                });
            });
            document.querySelectorAll('.section_sequence[id^=section_sequence_]').forEach(item => {
                item.addEventListener('change', (event) => {
                    this.order(event);
                });
            });
        }
    }
    makePrivate(event) {
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
    close(event) {
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
    order(event) {
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
    add() {
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
    edit() {
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
    delete() {
        if (this.deleteButton) {
            let id = this.deleteButton.getAttribute('data-section');
            if (id) {
                buttonToggle(this.deleteButton);
                ajax(location.protocol + '//' + location.host + '/api/talks/sections/' + id + '/delete/', null, 'json', 'DELETE', 60000, true).then(data => {
                    if (data.data === true) {
                        new Snackbar('Section removed. Redirecting to parent...', 'success');
                        window.location.href = data.location;
                    }
                    else {
                        new Snackbar(data.reason, 'failure', 10000);
                    }
                    buttonToggle(this.deleteButton);
                });
            }
        }
    }
}
//# sourceMappingURL=sections.js.map