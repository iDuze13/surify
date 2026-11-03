@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; font-size: 26px; font-weight: bold; color: #28628f;">
{{ $slot }}
</a>
</td>
</tr>
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
