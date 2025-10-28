@props(['items' => []])

<div class="breadcrumb-main">
    <div class="breadcrumb-action justify-content-center flex-wrap">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                @foreach($items as $item)
                    @if($loop->last)
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ ucwords($item['title']) }}
                        </li>
                    @else
                        <li class="breadcrumb-item">
                            @if(isset($item['url']))
                                <a href="{{ $item['url'] }}">
                                    @if(isset($item['icon']))
                                        <i class="{{ $item['icon'] }}"></i>
                                    @endif
                                    {{ ucwords($item['title']) }}
                                </a>
                            @else
                                @if(isset($item['icon']))
                                    <i class="{{ $item['icon'] }}"></i>
                                @endif
                                {{ ucwords($item['title']) }}
                            @endif
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
    </div>
</div>
