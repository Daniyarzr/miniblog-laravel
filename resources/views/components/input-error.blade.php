@props(['messages'])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'mt-1 text-danger small']) }}>
        @foreach ((array) $messages as $message)
            <div>{{ $message }}</div>
        @endforeach
    </div>
@endif
