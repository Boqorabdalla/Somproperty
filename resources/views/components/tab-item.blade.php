<a @if ($active)
    {{ $attributes->merge(['class' => 'nav-item nav-link f-15 d-inline-flex align-items-center active']) }}
@else
    {{ $attributes->merge(['class' => 'nav-item nav-link f-15 d-inline-flex align-items-center']) }}
    @endif
    href="{{ $link }}" role="tab" aria-selected="true">
    {{ $slot }}
</a>
