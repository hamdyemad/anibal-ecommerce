@props([
    'id' => 'video',
    'name' => 'video',
    'label' => 'Video',
    'required' => false,
    'existingVideo' => null,
    'placeholder' => 'Click to upload video',
    'recommendedSize' => 'Max size: 50MB',
    'accept' => 'video/mp4,video/mov,video/avi,video/wmv,video/flv,video/webm',
    'containerClass' => '',
])

@php
    $uniqueId = $id . '-' . uniqid();
@endphp
<style>
    .video-upload-wrapper {
        position: relative;
    }

    .video-preview-container {
        position: relative;
        width: 100%;
        min-height: 200px;
        border: 2px dashed #ddd;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }

    .video-preview-container:hover {
        border-color: #0056B7;
        background: #e7f3ff;
    }

    .preview-video {
        width: 100%;
        max-height: 300px;
        background: black;
        object-fit: cover;
    }

    .video-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 200px;
        padding: 20px;
        color: #8c90a4;
    }

    .video-placeholder i {
        font-size: 48px;
        margin-bottom: 10px;
        color: #c4c4c4;
    }

    .video-placeholder p {
        margin: 0;
        font-size: 14px;
        font-weight: 500;
    }

    .video-placeholder small {
        color: #adb5bd;
        font-size: 12px;
    }

    .video-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .video-preview-container:hover .video-overlay {
        opacity: 1;
    }

    .btn-change-video, .btn-remove-video {
        background: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-change-video:hover {
        background: #0056B7;
        color: white;
    }

    .btn-remove-video {
        background: #ff4757;
        color: white;
    }

    .btn-remove-video:hover {
        background: #e84118;
    }
</style>
<div class="form-group {{ $containerClass }}">
    <label class="il-gray fs-14 fw-500 mb-10">
        {{ $label }} @if($required)<span class="text-danger">*</span>@endif
    </label>
    <div class="video-upload-wrapper">
        <div class="video-preview-container" id="{{ $uniqueId }}-preview-container" data-target="{{ $id }}">
            @if($existingVideo)
                <video autoplay loop muted playsinline class="preview-video" id="{{ $uniqueId }}-preview-video">
                    <source src="{{ asset('storage/' . $existingVideo) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            @endif
            <div class="video-placeholder"
                 id="{{ $uniqueId }}-placeholder"
                 style="{{ $existingVideo ? 'display: none;' : '' }}">
                <i class="uil uil-video"></i>
                <p>{{ $placeholder }}</p>
                <small>{{ $recommendedSize }}</small>
                <small class="mt-1">Supported formats: MP4, MOV, AVI, WMV, FLV, WEBM</small>
            </div>
            <div class="video-overlay">
                <button type="button" class="btn-change-video" data-target="{{ $id }}">
                    <i class="uil uil-video"></i> {{ trans('common.change') ?? 'Change' }}
                </button>
                <button type="button"
                        class="btn-remove-video"
                        data-target="{{ $id }}"
                        style="{{ $existingVideo ? 'display: inline-flex;' : 'display: none;' }}">
                    <i class="uil uil-trash-alt"></i> {{ trans('common.remove') ?? 'Remove' }}
                </button>
            </div>
        </div>
        <input type="file"
               class="d-none video-file-input"
               id="{{ $id }}"
               name="{{ $name }}"
               accept="{{ $accept }}"
               data-preview="{{ $uniqueId }}">
    </div>
    @error($name)
        <div class="invalid-feedback d-block" style="display: block !important;">{{ $message }}</div>
    @enderror
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Video upload functionality - prevent duplicate initialization
    const videoInputs = document.querySelectorAll('.video-file-input:not([data-initialized])');

    videoInputs.forEach(input => {
        // Mark as initialized to prevent duplicate handlers
        input.setAttribute('data-initialized', 'true');

        const previewId = input.dataset.preview;
        const container = document.getElementById(previewId + '-preview-container');
        const placeholder = document.getElementById(previewId + '-placeholder');
        const changeBtn = container.querySelector('.btn-change-video');
        const removeBtn = container.querySelector('.btn-remove-video');

        // Click on container to select file (but not on buttons)
        container.addEventListener('click', (e) => {
            // Only open file dialog if clicking directly on container or placeholder
            if (!e.target.closest('.btn-change-video') && !e.target.closest('.btn-remove-video')) {
                input.click();
            }
        });

        if (changeBtn) {
            changeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                e.preventDefault();
                input.click();
            });
        }

        // Handle file selection
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    // Get the current preview video (it might have been created dynamically)
                    let previewVideo = document.getElementById(previewId + '-preview-video');

                    if (!previewVideo) {
                        // Create new video element
                        const video = document.createElement('video');
                        video.id = previewId + '-preview-video';
                        video.className = 'preview-video';
                        video.autoplay = true;
                        video.loop = true;
                        video.muted = true;
                        video.playsInline = true;
                        
                        const source = document.createElement('source');
                        source.src = event.target.result;
                        source.type = file.type;
                        
                        video.appendChild(source);
                        container.insertBefore(video, placeholder);
                        
                        // Ensure video plays
                        video.play().catch(err => console.log('Autoplay prevented:', err));
                    } else {
                        // Update existing video
                        const source = previewVideo.querySelector('source');
                        if (source) {
                            source.src = event.target.result;
                            source.type = file.type;
                        }
                        previewVideo.load();
                        previewVideo.play().catch(err => console.log('Autoplay prevented:', err));
                    }

                    if (placeholder) placeholder.style.display = 'none';
                    if (removeBtn) removeBtn.style.display = 'inline-flex';
                };
                reader.readAsDataURL(file);
            }
        });

        // Remove video
        if (removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                input.value = '';

                // Get the current preview video
                const currentPreviewVideo = document.getElementById(previewId + '-preview-video');
                if (currentPreviewVideo) {
                    currentPreviewVideo.remove();
                }

                if (placeholder) placeholder.style.display = 'flex';
                removeBtn.style.display = 'none';
            });
        }
    });
});
</script>
@endpush
