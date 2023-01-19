export class Sections
{
    private readonly addSectionForm: HTMLFormElement | null = null;
    private readonly addThreadForm: HTMLFormElement | null = null;
    private readonly editSectionForm: HTMLFormElement | null = null;
    private readonly sectionsList: HTMLTableElement | null = null;
    private readonly deleteSectionButton: HTMLInputElement | null = null;
    
    public constructor()
    {
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
            //Listener for marking sections private/public
            document.querySelectorAll('.section_private[id^=section_private_checkbox_]').forEach((item) => {
                //Tracking click to be able to roll back change easily
                item.addEventListener('click', (event: Event) => {
                    Sections.makeSectionPrivate(event);
                });
            });
            //Listener for opening/closing sections
            document.querySelectorAll('.section_closed[id^=section_closed_checkbox_]').forEach((item) => {
                //Tracking click to be able to roll back change easily
                item.addEventListener('click', (event: Event) => {
                    Sections.closeSection(event);
                });
            });
            //Listener for ordering sections
            document.querySelectorAll('.section_sequence[id^=section_sequence_]').forEach((item) => {
                //Tracking click to be able to roll back change easily
                item.addEventListener('change', (event: Event) => {
                    this.orderSection(event);
                });
            });
        }
        //Listeners if there are threads
        if (document.querySelector('#threads_list')) {
            //Listener for marking threads private/public
            document.querySelectorAll('.thread_private[id^=thread_private_checkbox_]').forEach((item) => {
                //Tracking click to be able to roll back change easily
                item.addEventListener('click', (event: Event) => {
                    Sections.makeThreadPrivate(event);
                });
            });
            //Listener for (un)pinning threads
            document.querySelectorAll('.thread_pin[id^=thread_pin_checkbox_]').forEach((item) => {
                //Tracking click to be able to roll back change easily
                item.addEventListener('click', (event: Event) => {
                    Sections.pinThread(event);
                });
            });
        }
    }
    
    private static makeSectionPrivate(event: Event): void
    {
        event.preventDefault();
        event.stopPropagation();
        const checkbox = event.target as HTMLInputElement;
        //Get verb
        let verb;
        if (checkbox.checked) {
            verb = 'private';
        } else {
            verb = 'public';
        }
        buttonToggle(checkbox);
        const sectionId = checkbox.getAttribute('data-section') ?? '';
        void ajax(`${location.protocol}//${location.host}/api/talks/sections/${sectionId}/mark${verb}/`, null, 'json', 'PATCH', 60000, true).then((response) => {
            const data = response as ajaxJSONResponse;
            if (data.data === true) {
                if (checkbox.checked) {
                    checkbox.checked = false;
                    addSnackbar('Section marked as public', 'success');
                } else {
                    checkbox.checked = true;
                    addSnackbar('Section marked as private', 'success');
                }
            } else {
                addSnackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(checkbox);
        });
    }
    
    private static closeSection(event: Event): void
    {
        event.preventDefault();
        event.stopPropagation();
        const checkbox = event.target as HTMLInputElement;
        //Get verb
        let verb;
        if (checkbox.checked) {
            verb = 'close';
        } else {
            verb = 'open';
        }
        buttonToggle(checkbox);
        const sectionId = checkbox.getAttribute('data-section') ?? '';
        void ajax(`${location.protocol}//${location.host}/api/talks/sections/${sectionId}/${verb}/`, null, 'json', 'PATCH', 60000, true).then((response) => {
            const data = response as ajaxJSONResponse;
            if (data.data === true) {
                if (checkbox.checked) {
                    checkbox.checked = false;
                    addSnackbar('Section opened', 'success');
                } else {
                    checkbox.checked = true;
                    addSnackbar('Section closed', 'success');
                }
            } else {
                addSnackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(checkbox);
        });
    }
    
    private orderSection(event: Event): void
    {
        event.preventDefault();
        event.stopPropagation();
        const orderInput = event.target as HTMLInputElement;
        const initialValue = orderInput.getAttribute('data-initial') ?? '0';
        const newValue = empty(orderInput.value) ? '0' : orderInput.value;
        //Do anything only if new value is different from initial value. Not sure if change event can happen without change in the value, but better be safe and reduce potential calls
        if (initialValue !== newValue) {
            buttonToggle(orderInput);
            //Generate form data
            const sectionId = orderInput.getAttribute('data-section') ?? '';
            const formData = new FormData();
            formData.append('order', newValue);
            void ajax(`${location.protocol}//${location.host}/api/talks/sections/${sectionId}/order/`, formData, 'json', 'PATCH', 60000, true).then((response) => {
                const data = response as ajaxJSONResponse;
                if (data.data === true) {
                    orderInput.setAttribute('data-initial', newValue);
                    this.sort();
                    addSnackbar('Order updated', 'success');
                } else {
                    orderInput.value = initialValue;
                    addSnackbar(data.reason, 'failure', 10000);
                }
                buttonToggle(orderInput);
            });
        }
    }
    
    private sort(): void
    {
        if (this.sectionsList) {
            const tbody = this.sectionsList.querySelector('tbody');
            if (tbody) {
                const newBody = tbody.cloneNode();
                let rows = Array.prototype.slice.call(tbody.rows, 0);
                rows = rows.sort((a: HTMLTableRowElement, b: HTMLTableRowElement): number => {
                    //Get sequences and text content elements
                    const aSequence = a.querySelector('.section_sequence');
                    const bSequence = b.querySelector('.section_sequence');
                    const aText = a.querySelector('.section_name a');
                    const bText = b.querySelector('.section_name a');
                    let order = 0;
                    //Get value of order inputs comparison. Important, that we compare "b" against "a" for descending order
                    if (aSequence && bSequence) {
                        //I do not see a way of "skip" an argument in function call without using `undefined`, so suppressing the check for the line
                        // eslint-disable-next-line no-undefined
                        order = (bSequence as HTMLInputElement).value.localeCompare((aSequence as HTMLInputElement).value, undefined, {'numeric': true});
                    }
                    //If it's 0, means that order is the same, thus we need to check the names
                    if (order === 0) {
                        //Here we are compare "a" against "b" for ascending order
                        if (aText && bText) {
                            return String(aText.textContent).localeCompare(String(bText.textContent));
                        }
                    }
                    return order;
                });
                for (const row of rows) {
                    newBody.appendChild(row);
                }
                (tbody.parentNode as HTMLTableElement).replaceChild(newBody, tbody);
            }
        }
    }
    
    private addSection(): void
    {
        if (this.addSectionForm) {
            //Get submit button
            const button = this.addSectionForm.querySelector('input[type=submit]');
            //Get form data
            const formData = new FormData(this.addSectionForm);
            //Check if custom icon is being attached
            const icon: HTMLInputElement | null = this.addSectionForm.querySelector('input[type=file]');
            if (icon?.files?.[0]) {
                formData.append('newSection[icon]', 'true');
            } else {
                formData.append('newSection[icon]', 'false');
            }
            //Add timezone
            formData.append('newSection[timezone]', Intl.DateTimeFormat().resolvedOptions().timeZone);
            buttonToggle(button as HTMLInputElement);
            void ajax(`${location.protocol}//${location.host}/api/talks/sections/`, formData, 'json', 'POST', 60000, true).then((response) => {
                const data = response as ajaxJSONResponse;
                if (data.data === true) {
                    addSnackbar('Section created. Reloading...', 'success');
                    pageRefresh();
                } else {
                    addSnackbar(data.reason, 'failure', 10000);
                }
                buttonToggle(button as HTMLInputElement);
            });
        }
    }
    
    private editSection(): void
    {
        if (this.editSectionForm) {
            //Get submit button
            const button = this.editSectionForm.querySelector('input[type=submit]');
            //Get form data
            const formData = new FormData(this.editSectionForm);
            //Check if custom icon is being attached
            const icon: HTMLInputElement | null = this.editSectionForm.querySelector('input[type=file]');
            if (icon?.files?.[0]) {
                formData.append('curSection[icon]', 'true');
            } else {
                formData.append('curSection[icon]', 'false');
            }
            buttonToggle(button as HTMLInputElement);
            void ajax(`${location.protocol}//${location.host}/api/talks/sections/${String(formData.get('curSection[sectionid]') ?? '0')}/edit/`, formData, 'json', 'POST', 60000, true).then((response) => {
                const data = response as ajaxJSONResponse;
                if (data.data === true) {
                    addSnackbar('Section updated. Reloading...', 'success');
                    pageRefresh();
                } else {
                    addSnackbar(data.reason, 'failure', 10000);
                    buttonToggle(button as HTMLInputElement);
                }
            });
        }
    }
    
    private deleteSection(): void
    {
        if (this.deleteSectionButton) {
            if (confirm('This is the last chance to back out.\nIf you press \'OK\' this section will be permanently deleted.\nPress \'Cancel\' to cancel the action.')) {
                const id = this.deleteSectionButton.getAttribute('data-section') ?? '';
                if (!empty(id)) {
                    buttonToggle(this.deleteSectionButton);
                    void ajax(`${location.protocol}//${location.host}/api/talks/sections/${id}/delete/`, null, 'json', 'DELETE', 60000, true).then((response) => {
                        const data = response as ajaxJSONResponse;
                        if (data.data === true) {
                            addSnackbar('Section removed. Redirecting to parent...', 'success');
                            window.location.href = data.location;
                        } else {
                            addSnackbar(data.reason, 'failure', 10000);
                        }
                        if (this.deleteSectionButton) {
                            buttonToggle(this.deleteSectionButton);
                        }
                    });
                }
            }
        }
    }
    
    private addThread(): void
    {
        if (this.addThreadForm) {
            //Get submit button
            const button = this.addThreadForm.querySelector('input[type=submit]');
            //Get form data
            const formData = new FormData(this.addThreadForm);
            //Check if custom icon is being attached
            const ogimage: HTMLInputElement | null = this.addThreadForm.querySelector('input[type=file]');
            if (ogimage?.files?.[0]) {
                formData.append('newThread[ogimage]', 'true');
            } else {
                formData.append('newThread[ogimage]', 'false');
            }
            //Add timezone
            formData.append('newThread[timezone]', Intl.DateTimeFormat().resolvedOptions().timeZone);
            buttonToggle(button as HTMLInputElement);
            void ajax(`${location.protocol}//${location.host}/api/talks/threads/`, formData, 'json', 'POST', 60000, true).then((response) => {
                const data = response as ajaxJSONResponse;
                if (data.data === true) {
                    if (this.addThreadForm) {
                        //Notify TinyMCE, that data was saved
                        const textarea = this.addThreadForm.querySelector('textarea');
                        if (textarea && !empty(textarea.id)) {
                            saveTinyMCE(textarea.id);
                        }
                    }
                    addSnackbar('Thread created. Reloading...', 'success');
                    window.location.href = data.location;
                } else {
                    addSnackbar(data.reason, 'failure', 10000);
                }
                buttonToggle(button as HTMLInputElement);
            });
        }
    }
    
    private static makeThreadPrivate(event: Event): void
    {
        event.preventDefault();
        event.stopPropagation();
        const checkbox = event.target as HTMLInputElement;
        //Get verb
        let verb;
        if (checkbox.checked) {
            verb = 'private';
        } else {
            verb = 'public';
        }
        buttonToggle(checkbox);
        const threadId = checkbox.getAttribute('data-thread') ?? '';
        void ajax(`${location.protocol}//${location.host}/api/talks/threads/${threadId}/mark${verb}/`, null, 'json', 'PATCH', 60000, true).then((response) => {
            const data = response as ajaxJSONResponse;
            if (data.data === true) {
                if (checkbox.checked) {
                    checkbox.checked = false;
                    addSnackbar('Thread marked as public', 'success');
                } else {
                    checkbox.checked = true;
                    addSnackbar('Thread marked as private', 'success');
                }
            } else {
                addSnackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(checkbox);
        });
    }
    
    private static pinThread(event: Event): void
    {
        event.preventDefault();
        event.stopPropagation();
        const checkbox = event.target as HTMLInputElement;
        //Get verb
        let verb;
        if (checkbox.checked) {
            verb = 'pin';
        } else {
            verb = 'unpin';
        }
        buttonToggle(checkbox);
        const threadId = checkbox.getAttribute('data-thread') ?? '';
        void ajax(`${location.protocol}//${location.host}/api/talks/threads/${threadId}/${verb}/`, null, 'json', 'PATCH', 60000, true).then((response) => {
            const data = response as ajaxJSONResponse;
            if (data.data === true) {
                if (checkbox.checked) {
                    checkbox.checked = false;
                    addSnackbar('Thread unpinned', 'success');
                } else {
                    checkbox.checked = true;
                    addSnackbar('Thread pinned', 'success');
                }
            } else {
                addSnackbar(data.reason, 'failure', 10000);
            }
            buttonToggle(checkbox);
        });
    }
}
