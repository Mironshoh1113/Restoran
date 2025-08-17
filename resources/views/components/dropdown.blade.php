@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white'])

@php
$alignmentClasses = match ($align) {
	'left' => 'start-0',
	'top' => '',
	default => 'end-0',
};
@endphp

<div class="position-relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
	<div @click="open = ! open">
		{{ $trigger }}
	</div>

	<div x-show="open" class="dropdown-menu show position-absolute {{ $alignmentClasses }} mt-2" style="display:none;">
		<div class="bg-white border rounded shadow-sm {{ $contentClasses }}">
			{{ $content }}
		</div>
	</div>
</div>
