@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@php
    $logo = \App\Models\Setting::getValue('site_logo');
    $title = \App\Models\Setting::getValue('site_title', 'umutMed');
@endphp

@if($logo)
    <img src="{{ config('app.url') . $logo }}" class="logo" alt="{{ $title }}">
@else
    <span style="font-size: 24px; font-weight: 900; color: #0f172a; text-decoration: none; font-style: italic;">
        umut<span style="color: #f27a1a;">Med</span>
    </span>
@endif
</a>
</td>
</tr>
