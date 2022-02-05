@props(['file'])

@php
$expireDate = $file->upload->expire_date;
$status = \ExpireDateUtil::checkExpireDate($expireDate) ?? null;
@endphp

@if($status === true)
    <a {{ $attributes->merge(['class' => 'hover:bg-gray-100 cursor-pointer transition duration-150 ease-in-out sm:rounded-md']) }}>
        <svg class="inline-block align-bottom h-6 w-6 text-green-500"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <circle cx="12" cy="12" r="9" />  <path d="M9 12l2 2l4 -4" /></svg>
        <span class="pr-1 text-gray-800 text-sm">利用できます</span>
    </a>
@else
    <a {{ $attributes->merge(['class' => 'hover:bg-gray-100 cursor-pointer transition duration-150 ease-in-out sm:rounded-md']) }}>
        <svg class="inline-block align-bottom h-6 w-6 text-red-500"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <circle cx="12" cy="12" r="9" />  <path d="M10 10l4 4m0 -4l-4 4" /></svg>
        <span class="pr-1 text-gray-800 text-sm">利用できません</span>
    </a>
@endif
