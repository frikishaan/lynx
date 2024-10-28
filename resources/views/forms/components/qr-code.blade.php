<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">
        <!-- Interact with the `state` property in Alpine.js -->

        {{-- <div class="visible-print text-center"> --}}
            {!! QrCode::size(250)->generate($getUrl()); !!}
            {{-- <p>Scan me to return to the original page.</p> --}}
        {{-- </div> --}}

    </div>
</x-dynamic-component>
