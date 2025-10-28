// Vendor Form - Multi-step wizard with review functionality
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 4;

    // Upload Functionality with File Type Support
    function setupImageUpload(inputId, containerId, previewId, placeholderId) {
        const input = document.getElementById(inputId);
        const container = document.getElementById(containerId);
        const placeholder = document.getElementById(placeholderId);
        
        if (!input || !container) {
            console.error('Upload elements not found:', { inputId, containerId });
            return;
        }
        
        console.log('File upload initialized for:', inputId);
        
        // Main container click
        container.addEventListener('click', function(e) {
            if (!e.target.closest('.btn-change-image') && !e.target.closest('.btn-remove-image')) {
                e.preventDefault();
                e.stopPropagation();
                input.click();
            }
        });
        
        // Placeholder click (additional for safety)
        if (placeholder) {
            placeholder.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                input.click();
            });
        }
        
        const changeBtn = container.querySelector('.btn-change-image');
        if (changeBtn) {
            changeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                input.click();
            });
        }
        
        const removeBtn = container.querySelector('.btn-remove-image');
        if (removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                input.value = '';
                const preview = document.getElementById(previewId);
                if (preview) {
                    preview.remove();
                }
                if (placeholder) {
                    placeholder.style.display = 'flex';
                }
                removeBtn.style.display = 'none';
                container.classList.remove('has-file');
            });
        }
        
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const fileType = file.type;
                const fileName = file.name;
                const fileExt = fileName.split('.').pop().toLowerCase();
                
                // Remove old preview if exists
                let preview = document.getElementById(previewId);
                if (preview) {
                    preview.remove();
                }
                
                // Check if it's an image
                if (fileType.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        preview = document.createElement('img');
                        preview.src = event.target.result;
                        preview.alt = 'Preview';
                        preview.className = inputId.includes('logo') ? 'logo-preview' : 'image-preview';
                        preview.id = previewId;
                        container.insertBefore(preview, container.firstChild);
                        
                        if (placeholder) {
                            placeholder.style.display = 'none';
                        }
                        if (removeBtn) {
                            removeBtn.style.display = 'inline-flex';
                        }
                        if (changeBtn) {
                            changeBtn.style.display = 'inline-flex';
                        }
                        // Add has-file class to show overlay on hover
                        container.classList.add('has-file');
                    };
                    reader.readAsDataURL(file);
                } else {
                    // For non-image files (PDF, DOC, etc.), show file info
                    preview = document.createElement('div');
                    preview.className = 'file-preview';
                    preview.id = previewId;
                    preview.style.cssText = 'display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; padding: 20px; text-align: center;';
                    
                    let icon = '';
                    let color = '';
                    if (fileExt === 'pdf') {
                        icon = 'uil-file-alt';
                        color = '#e74c3c';
                    } else if (['doc', 'docx'].includes(fileExt)) {
                        icon = 'uil-file-alt';
                        color = '#2980b9';
                    } else {
                        icon = 'uil-file';
                        color = '#95a5a6';
                    }
                    
                    preview.innerHTML = `
                        <i class="uil ${icon}" style="font-size: 48px; color: ${color}; margin-bottom: 10px;"></i>
                        <p style="margin: 0; font-weight: 600; color: #272b41; font-size: 14px; word-break: break-word;">${fileName}</p>
                        <small style="color: #9299b8; margin-top: 5px;">${(file.size / 1024).toFixed(2)} KB</small>
                    `;
                    
                    container.insertBefore(preview, container.firstChild);
                    
                    if (placeholder) {
                        placeholder.style.display = 'none';
                    }
                    if (removeBtn) {
                        removeBtn.style.display = 'inline-flex';
                    }
                    if (changeBtn) {
                        changeBtn.style.display = 'inline-flex';
                    }
                    // Add has-file class to show overlay on hover
                    container.classList.add('has-file');
                }
            }
        });
    }
    
    setupImageUpload('logo', 'logo-preview-container', 'logo-preview', 'logo-placeholder');
    setupImageUpload('banner', 'banner-preview-container', 'banner-preview', 'banner-placeholder');
    setupImageUpload('document-file-0', 'document-preview-container-0', 'document-preview-0', 'document-placeholder-0');
    
    // On page load, add has-file class if existing images are present (for update mode)
    const logoPreviewImg = document.getElementById('logo-preview-img');
    const logoContainer = document.getElementById('logo-preview-container');
    if (logoPreviewImg && logoContainer) {
        logoContainer.classList.add('has-file');
    }
    
    const bannerPreviewImg = document.getElementById('banner-preview-img');
    const bannerContainer = document.getElementById('banner-preview-container');
    if (bannerPreviewImg && bannerContainer) {
        bannerContainer.classList.add('has-file');
    }

    // Dynamic Document Rows
    let documentIndex = 0;
    const documentsContainer = document.getElementById('documentsContainer');
    const addDocumentBtn = document.getElementById('addDocumentRow');

    function updateRemoveButtons() {
        const rows = documentsContainer.querySelectorAll('.document-row');
        rows.forEach((row, index) => {
            const removeBtn = row.querySelector('.remove-document-row');
            if (rows.length > 1) {
                removeBtn.style.display = 'inline-block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }

    addDocumentBtn.addEventListener('click', function() {
        const newRow = document.createElement('div');
        newRow.className = 'document-row mb-25 pb-3';
        newRow.style.borderBottom = '1px solid #e3e6ef';
        
        // Get languages from window object
        const languages = window.vendorLanguages || [];
        
        // Generate name fields for each language
        let nameFieldsHTML = '';
        languages.forEach(lang => {
            const isRTL = lang.rtl;
            const labelText = lang.code === 'ar' ? 'اسم المستند' : 'Document Name';
            const placeholder = lang.code === 'ar' ? 'أدخل اسم المستند بالعربية' : `Enter document name in ${lang.name}`;
            
            nameFieldsHTML += `
                <div class="col-md-6 mb-3">
                    <label class="il-gray fs-14 fw-500 mb-10 ${isRTL ? 'w-100' : ''}" ${isRTL ? 'dir="rtl" style="text-align: right;"' : ''}>
                        ${labelText} (${lang.name}) <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                           name="documents[${documentIndex}][translations][${lang.id}][name]"
                           placeholder="${placeholder}"
                           ${isRTL ? 'dir="rtl"' : ''}>
                </div>
            `;
        });
        
        newRow.innerHTML = `
            <div class="row">
                ${nameFieldsHTML}
                <div class="col-md-6 mb-3">
                    <label class="il-gray fs-14 fw-500 mb-10">
                        Document File <span class="text-danger">*</span>
                    </label>
                    <div class="image-upload-wrapper">
                        <div class="image-preview-container document-preview" id="document-preview-container-${documentIndex}">
                            <div class="image-placeholder" id="document-placeholder-${documentIndex}">
                                <i class="uil uil-file-upload"></i>
                                <p>Click to upload document</p>
                                <small>PDF, JPG, PNG, DOC, DOCX</small>
                            </div>
                            <div class="image-overlay">
                                <button type="button" class="btn-change-image">
                                    <i class="uil uil-file-upload"></i> Change
                                </button>
                                <button type="button" class="btn-remove-image" style="display: none;">
                                    <i class="uil uil-trash-alt"></i> Remove
                                </button>
                            </div>
                        </div>
                        <input type="file" 
                               class="d-none" 
                               id="document-file-${documentIndex}"
                               name="documents[${documentIndex}][file]"
                               accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    </div>
                </div>
                <div class="col-md-6 mb-3 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-document-row w-100">
                        <i class="uil uil-trash-alt"></i> Remove Document
                    </button>
                </div>
            </div>
        `;
        
        documentsContainer.appendChild(newRow);
        
        // Setup upload functionality for the new row
        setupImageUpload(`document-file-${documentIndex}`, `document-preview-container-${documentIndex}`, `document-preview-${documentIndex}`, `document-placeholder-${documentIndex}`);
        
        documentIndex++;
        updateRemoveButtons();
        
        // Add animation
        newRow.style.opacity = '0';
        setTimeout(() => {
            newRow.style.transition = 'opacity 0.3s';
            newRow.style.opacity = '1';
        }, 10);
    });

    documentsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-document-row')) {
            const row = e.target.closest('.document-row');
            row.style.transition = 'opacity 0.3s';
            row.style.opacity = '0';
            setTimeout(() => {
                row.remove();
                updateRemoveButtons();
            }, 300);
        }
    });

    // Wizard Navigation
    function showStep(step) {
        document.querySelectorAll('.wizard-step').forEach(el => el.style.display = 'none');
        document.getElementById('step-' + step).style.display = 'block';
        
        document.querySelectorAll('.checkout-progress .step').forEach(function(stepEl) {
            const stepNum = parseInt(stepEl.getAttribute('data-step'));
            stepEl.classList.remove('current', 'completed');
            if (stepNum < step) stepEl.classList.add('completed');
            else if (stepNum === step) stepEl.classList.add('current');
        });
        
        // Populate review if moving to step 4
        if (step === 4) {
            populateReview();
        }
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    document.querySelectorAll('.next-step').forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        });
    });

    document.querySelectorAll('.prev-step').forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });
    });

    // Review Section - Edit Navigation
    document.querySelectorAll('.review-section').forEach(section => {
        section.addEventListener('click', function(e) {
            if (!e.target.closest('.edit-section-btn')) return;
            e.stopPropagation();
            
            const step = parseInt(this.getAttribute('data-edit-step'));
            currentStep = step;
            showStep(step);
        });
    });

    // Wizard Step Number Navigation
    document.querySelectorAll('.wizard-step-nav').forEach(stepNav => {
        stepNav.addEventListener('click', function() {
            const step = parseInt(this.getAttribute('data-step'));
            currentStep = step;
            showStep(step);
        });
    });

    // Populate Review Function
    function populateReview() {
        // Vendor Information - Get all translation fields dynamically
        const nameFields = document.querySelectorAll('input[name^="translations"][name$="[name]"]');
        const descFields = document.querySelectorAll('textarea[name^="translations"][name$="[description]"]');
        
        // Populate names
        let namesHTML = '';
        nameFields.forEach(field => {
            if (field.value) {
                const label = field.closest('.form-group').querySelector('label').textContent.replace('*', '').trim();
                namesHTML += `<p class="mb-1"><strong>${label}:</strong> ${field.value}</p>`;
            }
        });
        const reviewNamesContainer = document.getElementById('review-names');
        if (reviewNamesContainer) {
            reviewNamesContainer.innerHTML = namesHTML || '<p class="text-muted">-</p>';
        }
        
        // Populate descriptions
        let descsHTML = '';
        descFields.forEach(field => {
            if (field.value) {
                const label = field.closest('.form-group').querySelector('label').textContent.trim();
                descsHTML += `<p class="mb-1"><strong>${label}:</strong> ${field.value}</p>`;
            }
        });
        const reviewDescsContainer = document.getElementById('review-descriptions');
        if (reviewDescsContainer) {
            reviewDescsContainer.innerHTML = descsHTML || '<p class="text-muted">-</p>';
        }
        
        // Country and Activity
        const countrySelect = document.getElementById('country_id');
        const activitySelect = document.getElementById('activity_ids');
        const reviewCountry = document.getElementById('review-country');
        const reviewActivity = document.getElementById('review-activity');
        
        if (reviewCountry && countrySelect) {
            reviewCountry.textContent = countrySelect.options[countrySelect.selectedIndex]?.text || '-';
        }
        
        // Handle multiple activities
        if (reviewActivity && activitySelect) {
            const selectedActivities = Array.from(activitySelect.selectedOptions).map(option => option.text);
            if (selectedActivities.length > 0) {
                reviewActivity.innerHTML = selectedActivities.map(activity => 
                    `<span class="badge badge-round badge-primary badge-lg me-1">${activity}</span>`
                ).join('');
            } else {
                reviewActivity.textContent = '-';
            }
        }
        
        // Active Status
        const activeCheckbox = document.getElementById('active');
        const reviewActiveStatus = document.getElementById('review-active-status');
        if (reviewActiveStatus && activeCheckbox) {
            const isActive = activeCheckbox.checked;
            reviewActiveStatus.innerHTML = isActive 
                ? '<span class="badge badge-success">Active</span>' 
                : '<span class="badge badge-danger">Inactive</span>';
        }
        
        // Commission
        const commissionInput = document.getElementById('commission');
        const reviewCommission = document.getElementById('review-commission');
        if (reviewCommission && commissionInput) {
            reviewCommission.textContent = commissionInput.value ? commissionInput.value + '%' : '-';
        }
        
        // SEO
        const metaTitle = document.getElementById('meta_title');
        const metaDesc = document.getElementById('meta_description');
        const metaKeys = document.getElementById('meta_keywords');
        
        if (metaTitle) document.getElementById('review-meta-title').textContent = metaTitle.value || '-';
        if (metaDesc) document.getElementById('review-meta-description').textContent = metaDesc.value || '-';
        if (metaKeys) document.getElementById('review-meta-keywords').textContent = metaKeys.value || '-';
        
        // Logo Preview
        const logoInput = document.getElementById('logo');
        const logoReview = document.getElementById('review-logo');
        logoReview.innerHTML = ''; // Clear previous content
        
        // Check if new file is uploaded
        if (logoInput.files && logoInput.files[0]) {
            const logoPreview = document.getElementById('logo-preview');
            if (logoPreview && logoPreview.src) {
                const imgWrapper = document.createElement('div');
                imgWrapper.style.cssText = 'width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;';
                imgWrapper.innerHTML = `<img src="${logoPreview.src}" alt="Logo Preview" style="max-width: 150px; max-height: 150px; object-fit: contain; border-radius: 50%;">`;
                logoReview.appendChild(imgWrapper);
            } else {
                logoReview.innerHTML = `<span class="text-muted">${window.vendorTranslations?.noLogoUploaded || 'No logo uploaded'}</span>`;
            }
        } else {
            // Check if existing image is present (for update mode)
            const existingLogoImg = document.getElementById('logo-preview-img');
            if (existingLogoImg && existingLogoImg.src) {
                const imgWrapper = document.createElement('div');
                imgWrapper.style.cssText = 'width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;';
                imgWrapper.innerHTML = `<img src="${existingLogoImg.src}" alt="Logo Preview" style="max-width: 150px; max-height: 150px; object-fit: contain; border-radius: 50%;">`;
                logoReview.appendChild(imgWrapper);
            } else {
                logoReview.innerHTML = `<span class="text-muted">${window.vendorTranslations?.noLogoUploaded || 'No logo uploaded'}</span>`;
            }
        }
        
        // Banner Preview
        const bannerInput = document.getElementById('banner');
        const bannerReview = document.getElementById('review-banner');
        bannerReview.innerHTML = ''; // Clear previous content
        
        // Check if new file is uploaded
        if (bannerInput.files && bannerInput.files[0]) {
            const bannerPreview = document.getElementById('banner-preview');
            if (bannerPreview && bannerPreview.src) {
                const imgWrapper = document.createElement('div');
                imgWrapper.style.cssText = 'width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;';
                imgWrapper.innerHTML = `<img src="${bannerPreview.src}" alt="Banner Preview" style="max-width: 100%; max-height: 150px; object-fit: contain;">`;
                bannerReview.appendChild(imgWrapper);
            } else {
                bannerReview.innerHTML = `<span class="text-muted">${window.vendorTranslations?.noBannerUploaded || 'No banner uploaded'}</span>`;
            }
        } else {
            // Check if existing image is present (for update mode)
            const existingBannerImg = document.getElementById('banner-preview-img');
            if (existingBannerImg && existingBannerImg.src) {
                const imgWrapper = document.createElement('div');
                imgWrapper.style.cssText = 'width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;';
                imgWrapper.innerHTML = `<img src="${existingBannerImg.src}" alt="Banner Preview" style="max-width: 100%; max-height: 150px; object-fit: contain;">`;
                bannerReview.appendChild(imgWrapper);
            } else {
                bannerReview.innerHTML = `<span class="text-muted">${window.vendorTranslations?.noBannerUploaded || 'No banner uploaded'}</span>`;
            }
        }
        
        // Documents Review
        const documentsReview = document.getElementById('review-documents');
        const documentRows = document.querySelectorAll('.document-row');
        
        if (documentRows.length === 0) {
            documentsReview.innerHTML = `<p class="text-muted">${window.vendorTranslations?.noDocumentsUploaded || 'No documents uploaded'}</p>`;
        } else {
            let documentsHTML = '';
            const languages = window.vendorLanguages || [];
            
            documentRows.forEach((row, index) => {
                const fileInput = row.querySelector(`input[name="documents[${index}][file]"]`);
                
                if (fileInput && fileInput.files && fileInput.files[0]) {
                    const file = fileInput.files[0];
                    const fileName = file.name;
                    const fileExt = fileName.split('.').pop().toLowerCase();
                    
                    // Get all language translations
                    let translationsHTML = '';
                    languages.forEach(lang => {
                        const nameInput = row.querySelector(`input[name="documents[${index}][translations][${lang.id}][name]"]`);
                        const nameValue = nameInput ? nameInput.value : '';
                        const isRTL = lang.rtl;
                        const labelText = lang.code === 'ar' ? 'اسم المستند' : 'Document Name';
                        
                        if (nameValue) {
                            translationsHTML += `
                                <div class="mb-2" ${isRTL ? 'dir="rtl" style="text-align: right;"' : ''}>
                                    <strong>${labelText} (${lang.name}):</strong> <span class="text-dark">${nameValue}</span>
                                </div>
                            `;
                        }
                    });
                    
                    let iconClass = 'uil-file';
                    let iconColor = '#95a5a6';
                    if (fileExt === 'pdf') {
                        iconClass = 'uil-file-alt';
                        iconColor = '#e74c3c';
                    } else if (['doc', 'docx'].includes(fileExt)) {
                        iconClass = 'uil-file-alt';
                        iconColor = '#2980b9';
                    } else if (['jpg', 'jpeg', 'png'].includes(fileExt)) {
                        iconClass = 'uil-image';
                        iconColor = '#27ae60';
                    }
                    
                    documentsHTML += `
                        <div class="review-document-item d-flex align-items-start mb-3 p-3" style="background: #f8f9fa; border-radius: 8px; border: 1px solid #e3e6ef;">
                            <div class="review-document-preview me-3">
                                <i class="uil ${iconClass}" style="font-size: 32px; color: ${iconColor};"></i>
                            </div>
                            <div class="flex-grow-1">
                                ${translationsHTML}
                                <div>
                                    <strong>File:</strong> <span class="text-muted">${fileName}</span>
                                    <small class="text-muted ms-2">(${(file.size / 1024).toFixed(2)} KB)</small>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });
            
            documentsReview.innerHTML = documentsHTML || `<p class="text-muted">${window.vendorTranslations?.noDocumentsUploaded || 'No documents uploaded'}</p>`;
        }
        
        // Account Details
        document.getElementById('review-email').textContent = document.getElementById('email').value || '-';
    }

    // Check if there are validation errors and navigate to review step
    const hasErrors = document.querySelector('.alert-danger');
    if (hasErrors) {
        currentStep = 4;
        showStep(4);
    } else {
        showStep(1);
    }

    // AJAX Form Submission
    const vendorForm = document.getElementById('vendorForm');
    const submitBtn = document.getElementById('submitBtn');

    if (vendorForm && submitBtn) {
        vendorForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Disable submit button and show loading
            submitBtn.disabled = true;
            const btnIcon = submitBtn.querySelector('i');
            const btnText = submitBtn.querySelector('span:not(.spinner-border)');
            if (btnIcon) btnIcon.classList.add('d-none');
            if (btnText) btnText.classList.add('d-none');
            submitBtn.querySelector('.spinner-border').classList.remove('d-none');

            // Show loading overlay
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay && typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.show();
                
                // Start progress bar animation
                LoadingOverlay.animateProgressBar(30, 300).then(() => {
                    return submitForm();
                }).catch(handleError);
            } else {
                submitForm().catch(handleError);
            }

            function submitForm() {
                // Remove previous validation errors
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

                // Prepare form data
                const formData = new FormData(vendorForm);

                const data = Object.fromEntries(formData.entries());
                console.log('Form Data:', data);

                // Send AJAX request
                return fetch(vendorForm.action, {
                    method: vendorForm.method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    // Progress to 60%
                    if (typeof LoadingOverlay !== 'undefined') {
                        LoadingOverlay.animateProgressBar(60, 200);
                    }
                    
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw data;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    // Progress to 90%
                    if (typeof LoadingOverlay !== 'undefined') {
                        return LoadingOverlay.animateProgressBar(90, 200).then(() => data);
                    }
                    return data;
                })
                .then(data => {
                    // Complete progress bar
                    if (typeof LoadingOverlay !== 'undefined') {
                        return LoadingOverlay.animateProgressBar(100, 200).then(() => {
                            LoadingOverlay.showSuccess(
                                data.message || window.vendorTranslations?.vendorCreatedSuccessfully || 'Vendor created successfully!',
                                window.vendorTranslations?.redirecting || 'Redirecting...'
                            );
                            return data;
                        });
                    }
                    return data;
                })
                .then(data => {
                    // Redirect after 1.5 seconds
                    setTimeout(() => {
                        window.location.href = data.redirect || '/admin/vendors';
                    }, 1500);
                });
            }

            function handleError(error) {
                // Hide loading overlay
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.hide();
                }
                
                // Handle validation errors
                if (error.errors) {
                    // Clear previous error messages
                    document.querySelectorAll('.is-invalid').forEach(el => {
                        el.classList.remove('is-invalid');
                    });
                    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
                    
                    // Display errors inline on fields
                    Object.keys(error.errors).forEach(key => {
                        // Convert Laravel dot notation to bracket notation
                        // e.g., "translations.1.name" -> "translations[1][name]"
                        const bracketKey = key.replace(/\.(\d+)/g, '[$1]').replace(/\.([a-zA-Z_]+)/g, '[$1]');
                        
                        console.log('Error key conversion:', key, '=>', bracketKey);
                        
                        // Try to find the input with bracket notation
                        let input = document.querySelector(`[name="${bracketKey}"]`);
                        
                        // If not found, try exact match
                        if (!input) {
                            input = document.querySelector(`[name="${key}"]`);
                        }
                        
                        // For array fields like activity_ids, try with []
                        if (!input && !key.includes('[')) {
                            input = document.querySelector(`[name="${key}[]"]`);
                        }
                        
                        if (input) {
                            input.classList.add('is-invalid');
                            
                            // For select2 elements, find the parent container
                            let errorContainer = input.parentNode;
                            if (input.classList.contains('select2')) {
                                // Find the select2 container
                                const select2Container = input.nextElementSibling;
                                if (select2Container && select2Container.classList.contains('select2-container')) {
                                    errorContainer = select2Container.parentNode;
                                }
                            }
                            
                            // Remove existing error feedback if any
                            const existingFeedback = errorContainer.querySelector('.invalid-feedback');
                            if (existingFeedback) {
                                existingFeedback.remove();
                            }
                            
                            const feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback d-block';
                            feedback.textContent = error.errors[key][0];
                            errorContainer.appendChild(feedback);
                            
                            console.log('Added error for:', key, '->', bracketKey, error.errors[key][0]);
                        } else {
                            console.warn('Input not found for error key:', key, 'tried:', bracketKey);
                        }
                    });
                    
                    // Find which step has errors and navigate to it
                    const errorStep = findStepWithErrors();
                    if (errorStep) {
                        currentStep = errorStep;
                        showStep(errorStep);
                    }
                    
                    // Display errors under review fields in Step 4
                    displayErrorsInReview(error.errors);
                }
                
                // Re-enable submit button
                submitBtn.disabled = false;
                if (btnIcon) btnIcon.classList.remove('d-none');
                if (btnText) btnText.classList.remove('d-none');
                submitBtn.querySelector('.spinner-border').classList.add('d-none');
            }
            
            // Find which step contains fields with errors
            function findStepWithErrors() {
                const steps = document.querySelectorAll('.wizard-step');
                for (let i = 0; i < steps.length; i++) {
                    if (steps[i].querySelector('.is-invalid')) {
                        return i + 1; // Steps are 1-indexed
                    }
                }
                return null;
            }
        });
    }

    // Display errors under review fields
    function displayErrorsInReview(errors) {
        // Clear previous error messages in review
        document.querySelectorAll('.review-error-message').forEach(el => el.remove());
        
        // Map field names to review elements
        const fieldMapping = {
            'country_id': 'review-country',
            'activity_ids': 'review-activity',
            'activity_ids.*': 'review-activity',
            'email': 'review-email',
            'password': 'review-email', // Show password errors near email
            'password_confirmation': 'review-email',
            'logo': 'review-logo',
            'banner': 'review-banner',
        };
        
        // Display errors for mapped fields
        Object.keys(errors).forEach(key => {
            const reviewId = fieldMapping[key];
            if (reviewId) {
                const reviewElement = document.getElementById(reviewId);
                if (reviewElement) {
                    errors[key].forEach(errorMsg => {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'review-error-message alert alert-danger py-1 px-2 mt-2 mb-0';
                        errorDiv.style.fontSize = '13px';
                        errorDiv.style.display = 'flex';
                        errorDiv.style.alignItems = 'center';
                        errorDiv.innerHTML = `<i class="uil uil-exclamation-triangle me-1"></i>${errorMsg}`;
                        reviewElement.parentNode.appendChild(errorDiv);
                    });
                }
            }
            
            // Handle translation errors (name, description)
            if (key.includes('translations')) {
                const reviewNames = document.getElementById('review-names');
                const reviewDescs = document.getElementById('review-descriptions');
                
                if (key.includes('[name]') && reviewNames) {
                    errors[key].forEach(errorMsg => {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'review-error-message alert alert-danger py-1 px-2 mt-2 mb-0';
                        errorDiv.style.fontSize = '13px';
                        errorDiv.style.display = 'flex';
                        errorDiv.style.alignItems = 'center';
                        errorDiv.innerHTML = `<i class="uil uil-exclamation-triangle me-1"></i>${errorMsg}`;
                        reviewNames.appendChild(errorDiv);
                    });
                }
                
                if (key.includes('[description]') && reviewDescs) {
                    errors[key].forEach(errorMsg => {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'review-error-message alert alert-danger py-1 px-2 mt-2 mb-0';
                        errorDiv.style.fontSize = '13px';
                        errorDiv.style.display = 'flex';
                        errorDiv.style.alignItems = 'center';
                        errorDiv.innerHTML = `<i class="uil uil-exclamation-triangle me-1"></i>${errorMsg}`;
                        reviewDescs.appendChild(errorDiv);
                    });
                }
            }
            
            // Handle document errors
            if (key.includes('documents')) {
                const reviewDocs = document.getElementById('review-documents');
                if (reviewDocs) {
                    errors[key].forEach(errorMsg => {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'review-error-message alert alert-danger py-1 px-2 mt-2 mb-0';
                        errorDiv.style.fontSize = '13px';
                        errorDiv.style.display = 'flex';
                        errorDiv.style.alignItems = 'center';
                        errorDiv.innerHTML = `<i class="uil uil-exclamation-triangle me-1"></i>${errorMsg}`;
                        reviewDocs.appendChild(errorDiv);
                    });
                }
            }
        });
    }

});
