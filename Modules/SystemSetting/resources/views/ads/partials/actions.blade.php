<div class="btn-group">
    <a href="{{ route('admin.system-settings.ads.show', $ad->id) }}" 
       class="btn btn-sm btn-info" 
       title="{{ __('systemsetting::ads.view_ad') }}">
        <i class="las la-eye"></i>
    </a>
    <a href="{{ route('admin.system-settings.ads.edit', $ad->id) }}" 
       class="btn btn-sm btn-warning" 
       title="{{ __('systemsetting::ads.edit_ad') }}">
        <i class="las la-edit"></i>
    </a>
    <button type="button" 
            class="btn btn-sm btn-danger delete-ad" 
            data-id="{{ $ad->id }}"
            title="{{ __('systemsetting::ads.delete_ad') }}">
        <i class="las la-trash"></i>
    </button>
</div>
