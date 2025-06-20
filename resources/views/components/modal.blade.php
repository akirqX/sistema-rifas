@props(['id', 'maxWidth'])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth ?? '2xl'];
@endphp

<div
    x-data="{ show: @entangle($attributes->wire('model')).live }"
    x-show="show"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4"
    style="display: none;"
>
    <div x-show="show" x-trap.noscroll.inert="show"
         class="bg-panel-dark p-8 rounded-2xl w-full border border-border-subtle max-h-screen overflow-y-auto {{ $maxWidth }}"
         @click.away="show = false">
         {{ $slot }}
    </div>
</div>
