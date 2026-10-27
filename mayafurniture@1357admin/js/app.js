// app.js


if (typeof jQuery === 'undefined') {
    console.error('DEBUG: jQuery is required for app.js to function correctly.');
}

document.addEventListener("DOMContentLoaded", function () {
    console.log("DEBUG: DOMContentLoaded event fired.");

    try {
        document.querySelectorAll(".dropdown-menu.stop").forEach(e => e.addEventListener("click", ev => ev.stopPropagation()));
    } catch (e) { console.error("DEBUG: Error with dropdown menus:", e); }

    try {
        const themeColorToggle = document.getElementById("light-dark-mode");
        if (themeColorToggle) {
            themeColorToggle.addEventListener("click", function () {
                const currentTheme = document.documentElement.getAttribute("data-bs-theme");
                const newTheme = currentTheme === "light" ? "dark" : "light";
                console.log(`DEBUG: Theme change requested from '${currentTheme}' to '${newTheme}'.`);
                document.documentElement.setAttribute("data-bs-theme", newTheme);
                
                // Save to localStorage (primary)
                try {
                    localStorage.setItem('adminTheme', newTheme);
                    console.log(`DEBUG: Theme saved to localStorage: '${newTheme}'.`);
                } catch (e) {
                    console.warn("DEBUG: localStorage not available, falling back to cookie");
                }
                
                // Save to cookie (fallback)
                const expiryDate = new Date();
                expiryDate.setFullYear(expiryDate.getFullYear() + 1);
                document.cookie = `theme=${newTheme};path=/;expires=${expiryDate.toUTCString()};SameSite=Lax`;
                console.log(`DEBUG: Cookie 'theme' has been set to '${newTheme}'.`);
            });
        }
    } catch (e) { console.error("DEBUG: Error with theme toggle:", e); }

    try {
        const collapsedToggle = document.getElementById("togglemenu") || document.querySelector(".mobile-menu-btn");
        const startbarOverlay = document.querySelector(".startbar-overlay");

        const handleSidebarToggle = (e) => {
            if (e) e.preventDefault();
            const currentState = document.body.getAttribute("data-sidebar-size") || 'default';
            const newState = currentState === "collapsed" ? "default" : "collapsed";
            
            document.body.setAttribute("data-sidebar-size", newState);
            document.documentElement.setAttribute("data-sidebar-size", newState);
            localStorage.setItem('sidebarState', newState);
            console.log(`DEBUG: Sidebar toggle clicked. New state '${newState}' set.`);
        };
        
        const handleOverlayClick = () => {
             document.body.setAttribute("data-sidebar-size", "collapsed");
             document.documentElement.setAttribute("data-sidebar-size", "collapsed");
             localStorage.setItem('sidebarState', 'collapsed');
        };

        if (collapsedToggle) {
            collapsedToggle.addEventListener("click", handleSidebarToggle);
            console.log("DEBUG: Sidebar toggle event listener attached to:", collapsedToggle);
        }
        if (startbarOverlay) {
            startbarOverlay.addEventListener("click", handleOverlayClick);
        }
        window.addEventListener("resize", () => {
             if (window.innerWidth <= 1024) {
                 document.body.setAttribute("data-sidebar-size", "collapsed");
                 document.documentElement.setAttribute("data-sidebar-size", "collapsed");
             }
        });

    } catch (e) { console.error("DEBUG: Error with sidebar management:", e); }

    try {
        [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(el => new bootstrap.Tooltip(el));
        [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]')).map(el => new bootstrap.Popover(el));
    } catch (e) { console.error("DEBUG: Error initializing tooltips/popovers:", e); }

    initVerticalMenu();
});

function windowScroll() {
    const topbar = document.getElementById("topbar-custom");
    if (topbar) {
        topbar.classList.toggle("nav-sticky", window.scrollY >= 50);
    }
}
window.addEventListener("scroll", windowScroll);

function initVerticalMenu() {
    try {
        const currentUrl = window.location.href.split(/[?#]/)[0];
        document.querySelectorAll(".navbar-nav a").forEach(element => {
            if (element.href === currentUrl) {
                element.classList.add("active");
                let parent = element.parentNode;
                if (parent) parent.classList.add("active");
                let collapse = element.closest(".collapse");
                while (collapse) {
                    collapse.classList.add("show");
                    const parentLink = collapse.parentElement.querySelector("[data-bs-toggle='collapse']");
                    if (parentLink) {
                        parentLink.classList.add("active");
                        parentLink.setAttribute("aria-expanded", "true");
                    }
                    collapse = collapse.parentElement.closest(".collapse");
                }
            }
        });
    } catch (e) { console.error("DEBUG: Error initializing vertical menu:", e); }
}


function setupPublishWidget() {
    ['status', 'visibility', 'publish_date'].forEach(field => {
        const idSelector = field.replace('_', '-');
        const displayEl = $(`#${idSelector}-display`);
        const editEl = $(`#${idSelector}-edit`);
        const textEl = $(`#${idSelector}-text`);
        const inputEl = $(`#${field}`);
        const updateText = () => {
            let text;
            if (field === 'publish_date') {
                const dateVal = inputEl.val();
                if (dateVal) {
                    text = new Date(dateVal).toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true });
                } else { text = "Immediately"; }
            } else { text = inputEl.find('option:selected').text(); }
            textEl.text(text);
        };
        $(`#edit-${idSelector}-link`).on('click', e => { e.preventDefault(); displayEl.hide(); editEl.show(); });
        $(`#cancel-${idSelector}-link`).on('click', e => { e.preventDefault(); editEl.hide(); displayEl.show(); });
        $(`#ok-${idSelector}-btn`).on('click', () => { updateText(); editEl.hide(); displayEl.show(); });
        updateText();
    });
}


function setupPublishWidget() {
    ['status', 'visibility', 'publish_date'].forEach(field => {
        const idSelector = field.replace('_', '-');
        const displayEl = $(`#${idSelector}-display`);
        const editEl = $(`#${idSelector}-edit`);
        const textEl = $(`#${idSelector}-text`);
        const inputEl = $(`#${field}`);

        const updateText = () => {
            let text;
            if (field === 'publish_date') {
                const dateVal = inputEl.val();
                if (dateVal) {
                    const d = new Date(dateVal);
                    text = d.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true });
                } else { text = "Immediately"; }
            } else { text = inputEl.find('option:selected').text(); }
            textEl.text(text);
        };

        $(`#edit-${idSelector}-link`).on('click', e => { e.preventDefault(); displayEl.hide(); editEl.show(); });
        $(`#cancel-${idSelector}-link`).on('click', e => { e.preventDefault(); editEl.hide(); displayEl.show(); });
        $(`#ok-${idSelector}-btn`).on('click', () => { updateText(); editEl.hide(); displayEl.show(); });
        updateText();
    });
}


function setupImageUploader(containerId) {
    const container = $(`#${containerId}`);
    if (!container.length) return;
    
    const fileInput = container.find('input[type="file"]');
    const previewImage = container.find('img');
    const removeBtn = container.find('.remove-btn');
    const removeImageInput = container.closest('form').find('input[name="remove_image"]');
    
    const hasImage = () => previewImage.attr('src') && previewImage.attr('src') !== '';
    
    const updateState = () => {
        const isImagePresent = hasImage();
        container.toggleClass('has-image', isImagePresent);
        previewImage.toggle(isImagePresent);
        removeBtn.toggle(isImagePresent);
        container.find('.image-placeholder').toggle(!isImagePresent);
    };
    
    container.on('dragenter dragover', () => container.addClass('is-dragging')).on('dragleave drop', () => container.removeClass('is-dragging'));
    
    container.on('click', e => {
        if (!$(e.target).is('input[type="file"]') && !$(e.target).closest('.remove-btn').length) {
            e.preventDefault();
            fileInput.click();
        }
    });
    
    removeBtn.on('click', e => {
        e.stopPropagation(); e.preventDefault();
        fileInput.val('');
        previewImage.attr('src', '').hide();
        if (removeImageInput.length) removeImageInput.val('1');
        updateState();
        fileInput.trigger('change');
        container.removeClass('error').next('.error').remove();
    });
    
    fileInput.on('change', function () {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                previewImage.attr('src', e.target.result).show();
                if (removeImageInput.length) removeImageInput.val('0');
                updateState();
            };
            reader.readAsDataURL(this.files[0]);
        } else {
            if (previewImage.attr('src')) { if (removeImageInput.length) removeImageInput.val('1'); }
        }
        if ($.data(this.form, 'validator')) { $(this.form).validate().element(this); }
        container.removeClass('error').next('.error').remove();
    });
    
    updateState();
}

function setupTagInput(wrapperId) {
    const wrapper = $(`#${wrapperId}`);
    if (!wrapper.length) return;
    const textInput = wrapper.find('.tag-input');
    const hiddenInput = wrapper.siblings('input[type="hidden"]');
    const keywords = new Set(hiddenInput.val() ? hiddenInput.val().split(',').map(k => k.trim()).filter(Boolean) : []);
    const renderKeywords = () => {
        wrapper.find('.tag-chip-input').remove();
        Array.from(keywords).forEach(k => wrapper.prepend($(`<span class="tag-chip-input">${k}<span class="remove-tag" data-keyword="${k}">&times;</span></span>`)));
        hiddenInput.val(Array.from(keywords).join(','));
    };
    textInput.on('keydown', e => {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            const keyword = textInput.val().trim();
            if (keyword) {
                keywords.add(keyword);
                renderKeywords();
                textInput.val('');
            }
        }
    });
    wrapper.on('click', '.remove-tag', function () {
        keywords.delete($(this).data('keyword'));
        renderKeywords();
    });
    renderKeywords();
}

function setupSidebarSelectionWidget(options) {
    const { widgetKey, initialData, initialSelection, onSave, onToggleStatus, validator } = options;
    let allItems = JSON.parse(JSON.stringify(initialData));
    let selectionState = new Set(initialSelection.map(String));
    let itemMap = new Map();

    const updateMap = () => {
        itemMap.clear();
        allItems.forEach(item => itemMap.set(String(item.id), item));
    };

    const createItemHTML = (item, isSelectedTab = false) => {
        const isChecked = selectionState.has(String(item.id));
        const isHidden = item.status != '1';
        const statusIconClass = isHidden ? 'la-eye-slash' : 'la-eye';
        const nameAttribute = isSelectedTab ? '' : `name="pr_${widgetKey}_ids[]"`;
        const uniqueId = `${widgetKey}-${item.id}${isSelectedTab ? '-selected' : ''}`;

        return `<div class="form-check mb-1 ${isHidden ? 'item-hidden' : ''}">
                    <input type="checkbox" class="form-check-input" ${nameAttribute} id="${uniqueId}" value="${item.id}" ${isChecked ? 'checked' : ''}>
                    <div class="form-check-label-wrapper">
                        <label class="form-check-label" for="${uniqueId}">${item.name}</label>
                        <span class="item-actions">
                            <i class="las ${statusIconClass} action-icon toggle-status-icon" data-type="${widgetKey}" data-id="${item.id}" title="Toggle Status"></i>
                            <i class="las la-pen action-icon edit-icon" data-type="${widgetKey}" data-id="${item.id}" title="Edit"></i>
                        </span>
                    </div>
                </div>`;
    };
    
    const rerender = () => {
        const allContainer = $(`#all-${widgetKey}-list`).empty();
        const selectedContainer = $(`#selected-${widgetKey}-list`).empty();
        
        allItems.sort((a, b) => a.name.localeCompare(b.name)).forEach(item => allContainer.append(createItemHTML(item, false)));
        
        const tabItem = $(`#selected-${widgetKey}-tab-item`);
        const tabButton = tabItem.find('button');
        
        if (selectionState.size > 0) {
            selectionState.forEach(id => {
                const item = itemMap.get(id);
                if (item) selectedContainer.append(createItemHTML(item, true));
            });
            tabButton.text(`Selected (${selectionState.size})`);
            tabItem.removeClass('d-none');
        } else {
            if (tabButton.hasClass('active')) {
                bootstrap.Tab.getOrCreateInstance($(`[data-bs-target="#all-${widgetKey}-pane"]`)[0]).show();
            }
            tabItem.addClass('d-none');
        }
    };

    $(`#${widgetKey}sCollapse`).on('change', `input[type="checkbox"]`, function () {
        const id = $(this).val();
        $(this).is(':checked') ? selectionState.add(id) : selectionState.delete(id);
        rerender();
        if (validator) {
            const validatorInput = $(`#${widgetKey}_validator`);
            if (validatorInput.length) {
                validatorInput.val(selectionState.size > 0 ? '1' : '');
                validator.element(validatorInput);
            }
        }
    });
    
    $(`#${widgetKey}-search`).on('input', function () {
        const searchTerm = $(this).val().toLowerCase();
        $(`#all-${widgetKey}-list .form-check`).each(function () {
            $(this).toggle($(this).find('label').text().toLowerCase().includes(searchTerm));
        });
    });

    const formConfig = {
        link: $(`#add-${widgetKey}-link`),
        form: $(`#add-${widgetKey}-form`),
        saveBtn: $(`#add-${widgetKey}-form .sidebar-save-btn`),
        cancelBtn: $(`#cancel-add-${widgetKey}-btn`),
        nameInput: $(`#new-${widgetKey}-name`),
        imageInput: $(`#new_${widgetKey}_image`),
        imageContainer: $(`#new-${widgetKey}-image-container`)
    };

    const resetSidebarForm = () => {
        formConfig.nameInput.val('').removeClass('error is-invalid').next('.error').remove();
        if (formConfig.imageContainer.length) {
            formConfig.imageContainer.removeClass('has-image error is-invalid').find('img').attr('src', '').hide();
            formConfig.imageContainer.find('.remove-btn').hide();
            formConfig.imageInput.val('');
            formConfig.imageContainer.next('.error').remove();
            formConfig.form.find('input[name="remove_image"]').val('0');
        }
        formConfig.form.data('editId', null).find('.sidebar-save-btn').text('Add');
    };

    formConfig.link.on('click', e => { e.preventDefault(); resetSidebarForm(); formConfig.form.removeClass('d-none'); formConfig.link.addClass('d-none'); });
    formConfig.cancelBtn.on('click', () => { formConfig.form.addClass('d-none'); formConfig.link.removeClass('d-none'); resetSidebarForm(); });

    $(document).on('click', `.edit-icon[data-type="${widgetKey}"]`, function (e) {
        e.stopPropagation();
        const id = $(this).data('id').toString();
        const item = itemMap.get(id);
        if (item) {
            formConfig.link.click();
            formConfig.form.data('editId', id);
            formConfig.nameInput.val(item.name);
            formConfig.saveBtn.text('Update');
            
            if (formConfig.imageContainer.length && item.image) {
                const preview = formConfig.imageContainer.find('img');
                preview.attr('src', '../' + item.image).show();
                formConfig.imageContainer.addClass('has-image');
                formConfig.imageContainer.find('.remove-btn').show();
                formConfig.form.find('input[name="remove_image"]').val('0');
            }
        }
    });

    $(document).on('click', `.toggle-status-icon[data-type="${widgetKey}"]`, function (e) {
        e.preventDefault(); e.stopPropagation();
        const icon = $(this);
        const id = icon.data('id').toString();
        if (typeof onToggleStatus !== 'function') return;

        icon.removeClass('la-eye la-eye-slash').addClass('la-spinner la-spin');
        onToggleStatus({ id: id }).done(res => {
            if (res.success) {
                toastr.success(res.message);
                const idx = allItems.findIndex(t => String(t.id) === id);
                if (idx > -1) allItems[idx] = res.data;
                updateMap();
                rerender();
            } else { toastr.error(res.message); rerender(); }
        }).fail(() => { toastr.error('Server error.'); rerender(); });
    });
    
    // ========== FIX START: Corrected validation and submission logic ==========
    formConfig.saveBtn.on('click', function (e) {
        e.preventDefault();
        const nameInput = formConfig.nameInput;
        const name = nameInput.val().trim();
        const id = formConfig.form.data('editId');
        let isValid = true;
        
        // Clear previous errors
        nameInput.removeClass('error').next('.error').remove();
        if (formConfig.imageContainer.length) {
            formConfig.imageContainer.removeClass('error').next('.error').remove();
        }

        // Validate name
        if (!name) {
            nameInput.addClass('error').after('<label class="error">This field is required.</label>');
            isValid = false;
        }

        // Validate image only for new Course Types
        if (widgetKey === 'course_type' && !id && formConfig.imageInput.length && formConfig.imageInput[0].files.length === 0) {
            formConfig.imageContainer.addClass('error').after('<label class="error">An image is required.</label>');
            isValid = false;
        }

        if (!isValid) return;

        if (typeof onSave !== 'function') return;

        const button = $(this);
        const originalText = button.text();
        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        const formData = new FormData();
        formData.append('name', name);
        if (id) {
            formData.append('id', id);
        }
        if (formConfig.imageInput.length && formConfig.imageInput[0].files[0]) {
             formData.append('image', formConfig.imageInput[0].files[0]);
        }
        if(formConfig.form.find('input[name="remove_image"]').length){
            formData.append('remove_image', formConfig.form.find('input[name="remove_image"]').val());
        }
        
        onSave(formData).done(res => {
            if (res.success) {
                toastr.success(res.message);
                const item = res.data;
                if (id) {
                    const idx = allItems.findIndex(t => String(t.id) === id);
                    if (idx > -1) allItems[idx] = item;
                } else {
                    allItems.push(item);
                    selectionState.add(String(item.id));
                }
                updateMap();
                rerender();
                formConfig.cancelBtn.click();
            } else {
                toastr.error(res.message);
            }
        }).fail(() => {
            toastr.error('Server communication error.');
        }).always(() => button.prop('disabled', false).html(originalText));
    });

    // Remove error on input change
    formConfig.nameInput.on('input', function() { $(this).removeClass('error').next('.error').remove(); });
    if(formConfig.imageInput.length) {
        formConfig.imageInput.on('change', function() { formConfig.imageContainer.removeClass('error').next('.error').remove(); });
    }
    // ========== FIX END ==========

    updateMap();
    rerender();
}