@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
{{-- @if (trim($slot) === 'Laravel') --}}
<img src="https://i.imghippo.com/files/QFzf5428R.png" class="logo" alt="Laravel Logo">
{{-- @else
{{ $slot }}
@endif --}}
</a>
</td>
</tr>
