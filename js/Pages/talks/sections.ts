export class editSections
{
    private readonly addSectionForm: HTMLFormElement | null = null;
    private readonly editSectionForm: HTMLFormElement | null = null;
    private readonly sectionsList: HTMLTableElement | null = null;
    private readonly deleteButton: HTMLInputElement | null = null;
    
    constructor()
    {
        this.sectionsList = document.getElementById('sections_list') as HTMLTableElement;
        this.addSectionForm = document.getElementById('addSectionForm') as HTMLFormElement;
        this.editSectionForm = document.getElementById('editSectionForm') as HTMLFormElement;
        this.deleteButton = document.getElementById('delete_section') as HTMLInputElement;
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
            //Listener for marking sections private/public
            document.querySelectorAll('.section_private[id^=section_private_checkbox_]').forEach(item => {
                //Tracking click to be able to roll back change easily
                item.addEventListener('click', (event: Event) => {
                    this.makePrivate(event);
                });
            });
            //Listener for opening/closing sections
            document.querySelectorAll('.section_closed[id^=section_closed_checkbox_]').forEach(item => {
                //Tracking click to be able to roll back change easily
                item.addEventListener('click', (event: Event) => {
                    this.close(event);
                });
            });
            //Listener for ordering sections
            document.querySelectorAll('.section_sequence[id^=section_sequence_]').forEach(item => {
                //Tracking click to be able to roll back change easily
                item.addEventListener('change', (event: Event) => {
                    this.order(event);
                });
            });
        }
    }
    
    private makePrivate(event: Event)
    {
        event.preventDefault();
        event.stopPropagation();
        let checkbox = event.target as HTMLInputElement;
        //Get verb
        let verb;
        if (checkbox.checked) {
            verb = 'private';
        } else {
            verb = 'public';
        }
        buttonToggle(checkbox as HTMLInputElement);
        let sectionId = checkbox.getAttribute('data-section') ?? '';
        ajax(location.protocol+'//'+location.host+'/api/talks/sections/'+sectionId+'/mark'+verb+'/', null, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                if (checkbox.checked) {
                    checkbox.checked = false;
                    new Snackbar('Section marked as public', 'success');
                } else {
                    checkbox.checked = true;
                    new Snackbar('Section marked as private', 'success');
                }
            } else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(checkbox as HTMLInputElement);
        });
    }
    
    private close(event: Event)
    {
        event.preventDefault();
        event.stopPropagation();
        let checkbox = event.target as HTMLInputElement;
        //Get verb
        let verb;
        if (checkbox.checked) {
            verb = 'close';
        } else {
            verb = 'open';
        }
        buttonToggle(checkbox as HTMLInputElement);
        let sectionId = checkbox.getAttribute('data-section') ?? '';
        ajax(location.protocol+'//'+location.host+'/api/talks/sections/'+sectionId+'/'+verb+'/', null, 'json', 'PATCH', 60000, true).then(data => {
            if (data.data === true) {
                if (checkbox.checked) {
                    checkbox.checked = false;
                    new Snackbar('Section opened', 'success');
                } else {
                    checkbox.checked = true;
                    new Snackbar('Section closed', 'success');
                }
            } else {
                new Snackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(checkbox as HTMLInputElement);
        });
    }
    
    private order(event: Event)
    {
        event.preventDefault();
        event.stopPropagation();
        let orderInput = event.target as HTMLInputElement;
        let initialValue = orderInput.getAttribute('data-initial') ?? '0';
        let newValue = orderInput.value ?? '0';
        //Do anything only if new value is different from initial value. Not sure if change event can happen without change in the value, but better be safe and reduce potential calls
        if (initialValue !== newValue) {
            buttonToggle(orderInput as HTMLInputElement);
            //Generate form data
            let sectionId = orderInput.getAttribute('data-section') ?? '';
            let formData = new FormData();
            formData.append('order', newValue);
            ajax(location.protocol+'//'+location.host+'/api/talks/sections/'+sectionId+'/order/', formData, 'json', 'PATCH', 60000, true).then(data => {
                if (data.data === true) {
                    orderInput.setAttribute('data-initial', newValue);
                    this.sort();
                    new Snackbar('Order changed. Refresh the page to see changes.', 'success');
                } else {
                    orderInput.value = initialValue;
                    new Snackbar(data.reason, 'failure', 10000);
                }
                buttonToggle(orderInput as HTMLInputElement);
            });
        }
    }
    
    private sort()
    {
        let tbody = (this.sectionsList as HTMLTableElement).querySelector('tbody');
        if (tbody) {
            let newBody = tbody.cloneNode();
            let rows = Array.prototype.slice.call(tbody.rows, 0);
            rows = rows.sort(function (a, b) {
                //Get value of order inputs comparison. Important, that we compare "b" against "a" for descending order
                let order = b.querySelector('.section_sequence').value.localeCompare(a.querySelector('.section_sequence').value, undefined, {numeric: true});
                //If it's 0, means that order is the same, thus we need to check the names
                if (order === 0) {
                    //Here we are compare "a" against "b" for ascending order
                    return a.querySelector('.section_name a').textContent.localeCompare(b.querySelector('.section_name a').textContent);
                } else {
                    return order;
                }
            });
            for(let i = 0; i < rows.length; ++i) {
                newBody.appendChild(rows[i]);
            }
            (tbody.parentNode as HTMLTableElement).replaceChild(newBody, tbody);
        }
    }
    
    private add()
    {
        if (this.addSectionForm) {
            //Get submit button
            let button = this.addSectionForm.querySelector('input[type=submit]')
            //Get form data
            let formData = new FormData(this.addSectionForm);
            //Check if custom icon is being attached
            let icon = this.addSectionForm.querySelector('input[type=file]') as HTMLInputElement;
            if (icon && icon.files && icon.files[0]) {
                formData.append('newSection[icon]', 'true');
            } else {
                formData.append('newSection[icon]', 'false');
            }
            //Add timezone
            formData.append('newSection[timezone]', Intl.DateTimeFormat().resolvedOptions().timeZone);
            buttonToggle(button as HTMLInputElement);
            ajax(location.protocol + '//' + location.host + '/api/talks/sections/', formData, 'json', 'POST', 60000, true).then(data => {
                if (data.data === true) {
                    new Snackbar('Section created. Reloading...', 'success');
                    location.reload();
                } else {
                    new Snackbar(data.reason, 'failure', 10000);
                }
                buttonToggle(button as HTMLInputElement);
            });
        }
    }
    
    private edit()
    {
        if (this.editSectionForm) {
            //Get submit button
            let button = this.editSectionForm.querySelector('input[type=submit]')
            //Get form data
            let formData = new FormData(this.editSectionForm);
            //Check if custom icon is being attached
            let icon = this.editSectionForm.querySelector('input[type=file]') as HTMLInputElement;
            if (icon && icon.files && icon.files[0]) {
                formData.append('curSection[icon]', 'true');
            } else {
                formData.append('curSection[icon]', 'false');
            }
            buttonToggle(button as HTMLInputElement);
            ajax(location.protocol + '//' + location.host + '/api/talks/sections/'+(formData.get('curSection[sectionid]') ?? '0')+'/edit/', formData, 'json', 'POST', 60000, true).then(data => {
                if (data.data === true) {
                    new Snackbar('Section updated. Reloading...', 'success');
                    location.reload();
                } else {
                    new Snackbar(data.reason, 'failure', 10000);
                    buttonToggle(button as HTMLInputElement);
                }
            });
        }
    }
    
    private delete()
    {
        if (this.deleteButton) {
            let id = this.deleteButton.getAttribute('data-section');
            if (id) {
                buttonToggle(this.deleteButton as HTMLInputElement);
                ajax(location.protocol + '//' + location.host + '/api/talks/sections/'+id+'/delete/', null, 'json', 'DELETE', 60000, true).then(data => {
                    if (data.data === true) {
                        new Snackbar('Section removed. Redirecting to parent...', 'success');
                        window.location.href = data.location;
                    } else {
                        new Snackbar(data.reason, 'failure', 10000);
                    }
                    buttonToggle(this.deleteButton as HTMLInputElement);
                });
            }
        }
    }
}
