@props(['name', 'size' => 20, 'class' => ''])
<svg class="icon {{ $class }}" width="{{ $size }}" height="{{ $size }}" aria-hidden="true" focusable="false"><use href="#i-{{ $name }}"></use></svg>
