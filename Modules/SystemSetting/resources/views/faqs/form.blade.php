@extends('layout.app')

@section('title', isset($faq) ? __('systemsetting::faqs.edit_faq') : __('systemsetting::faqs.create_faq'))

@section('content')
<div class="container-fluid mb-3">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => __('systemsetting::faqs.faqs_management'), 'url' => route('admin.system-settings.faqs.index')],
                ['title' => isset($faq) ? __('systemsetting::faqs.edit_faq') : __('systemsetting::faqs.create_faq')]
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-default card-md mb-4">
                <div class="card-header">
                    <h6>{{ isset($faq) ? __('systemsetting::faqs.edit_faq') : __('systemsetting::faqs.create_faq') }}</h6>
                </div>
                <div class="card-body">
                    <div id="alertContainer"></div>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show d-block" role="alert">
                            <strong>Validation Errors</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form id="faqForm" method="POST" action="{{ isset($faq) ? route('admin.system-settings.faqs.update', $faq->id) : route('admin.system-settings.faqs.store') }}">
                        @csrf
                        @if(isset($faq))
                            @method('PUT')
                        @endif

                        <div class="card card-holder">
                            <div class="card-header">
                                <h3 class="fw-bold m-0">
                                    <i class="uil uil-info-circle me-1"></i>{{ __('systemsetting::faqs.basic_information') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Question (Multilingual) --}}
                                    <div class="col-md-12 mb-3">
                                        <x-multilingual-input
                                            name="question"
                                            label="Question"
                                            :labelAr="'السؤال'"
                                            :placeholder="'Question'"
                                            :placeholderAr="'السؤال'"
                                            type="text"
                                            :languages="$languages"
                                            :model="$faq ?? null"
                                            :required="true"
                                        />
                                    </div>

                                    {{-- Answer (Multilingual with CKEditor) --}}
                                    <div class="col-md-12 mb-3">
                                        <x-multilingual-input
                                            name="answer"
                                            label="{{ __('systemsetting::faqs.answer') }}"
                                            :labelAr="'الإجابة'"
                                            :placeholder="__('systemsetting::faqs.answer')"
                                            :placeholderAr="'الإجابة'"
                                            type="textarea"
                                            rows="6"
                                            :languages="$languages"
                                            :model="$faq ?? null"
                                            :required="true"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="button-group d-flex pt-25 justify-content-end">
                            <a href="{{ route('admin.system-settings.faqs.index') }}" class="btn btn-light btn-default btn-squared fw-400 text-capitalize me-2">
                                {{ __('systemsetting::faqs.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary btn-default btn-squared text-capitalize">
                                {{ __('systemsetting::faqs.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
$(document).ready(function() {
    // Initialize CKEditor for answer textareas
    const editors = {};

    setTimeout(function() {
        document.querySelectorAll('textarea[name*="answer"]').forEach(function(textarea) {
            const editorId = textarea.id;
            const lang = textarea.getAttribute('data-lang') || 'en';

            editors[editorId] = CKEDITOR.replace(editorId, {
                height: 250,
                language: lang === 'ar' ? 'ar' : 'en',
                contentsLangDirection: lang === 'ar' ? 'rtl' : 'ltr',
                toolbar: [
                    { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
                    { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'] },
                    { name: 'links', items: ['Link', 'Unlink'] },
                    { name: 'insert', items: ['Table'] },
                    { name: 'styles', items: ['Format'] },
                    { name: 'tools', items: ['Maximize'] }
                ]
            });
        });
    }, 500);

    const faqForm = document.getElementById('faqForm');

    if (faqForm) {
        faqForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Sync all CKEditor instances
            for (let editorId in editors) {
                if (editors[editorId] && editors[editorId].updateElement) {
                    editors[editorId].updateElement();
                }
            }

            for (let instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            const formData = new FormData(this);

            submitBtn.disabled = true;
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';

            if (window.LoadingOverlay) {
                window.LoadingOverlay.show();
            }

            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (window.LoadingOverlay) {
                        window.LoadingOverlay.showSuccess(
                            data.message || '{{ __("systemsetting::faqs.created_successfully") }}',
                            'Redirecting...'
                        );
                    }

                    setTimeout(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.reload();
                        }
                    }, 1000);
                } else {
                    if (window.LoadingOverlay) {
                        window.LoadingOverlay.hide();
                    }

                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;

                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || 'An error occurred');
                    } else {
                        alert(data.message || 'An error occurred');
                    }

                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            let input = document.querySelector(`[name="${key}"]`);

                            if (!input) {
                                const parts = key.split('.');
                                let bracketKey = parts[0];
                                for (let i = 1; i < parts.length; i++) {
                                    bracketKey += '[' + parts[i] + ']';
                                }
                                input = document.querySelector(`[name="${bracketKey}"]`);
                            }

                            if (input) {
                                input.classList.add('is-invalid');
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'invalid-feedback';
                                errorDiv.textContent = data.errors[key][0];
                                input.parentNode.appendChild(errorDiv);
                            }
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);

                if (window.LoadingOverlay) {
                    window.LoadingOverlay.hide();
                }

                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;

                if (typeof toastr !== 'undefined') {
                    toastr.error('An error occurred');
                } else {
                    alert('An error occurred');
                }
            });
        });

        document.querySelectorAll('input, select, textarea').forEach(input => {
            const clearError = () => {
                input.classList.remove('is-invalid');
                const feedback = input.parentNode.querySelector('.invalid-feedback');
                if (feedback) feedback.remove();
            };

            input.addEventListener('input', clearError);
            input.addEventListener('change', clearError);
        });
    }
});
</script>
@endpush
