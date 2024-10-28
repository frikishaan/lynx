<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $url = $getUrl();    
    @endphp
    
    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">

        <img 
            src="data:image/png;base64,{!! base64_encode(
                QrCode::format('png')
                    ->margin(2)
                    ->size(250)
                    ->merge('/public/images/qr-logo.png')
                    ->errorCorrection('M')
                    ->generate($url)
            ) !!}" 
            alt="{{ $url }}"
        />

    </div>
</x-dynamic-component>
