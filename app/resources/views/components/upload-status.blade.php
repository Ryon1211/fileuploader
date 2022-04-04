@props(['uploadLink'])

@php
$status = null;
$id = $uploadLink->upload_id;
$path = $uploadLink->path;
$expireDate = $uploadLink->file_expire_date ?? '';

if($id !== null) {
    $status = \ExpireDateUtil::checkExpireDate($expireDate);
}

$route = $status === null ? 'user.upload' : 'user.show.files';
$href = route($route, ['key' => $path]);

@endphp

<a href={{ $href }} {{ $attributes->merge(['class' => 'hover:bg-gray-100 cursor-pointer transition duration-150 ease-in-out sm:rounded-md']) }}>
@if($status === true)
        <svg class="inline-block align-bottom h-6 w-6 text-green-500"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <circle cx="12" cy="12" r="9" />  <path d="M9 12l2 2l4 -4" /></svg>
        <span class="pr-1 text-gray-800 text-sm">アップロード完了</span>
@elseif($status === false)
        <svg class="inline-block align-bottom h-6 w-6 text-red-500"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <circle cx="12" cy="12" r="9" />  <path d="M10 10l4 4m0 -4l-4 4" /></svg>
        <span class="pr-1 text-gray-800 text-sm">利用できません</span>
@else
    <svg class="inline-block align-bottom h-6 w-6 text-gray-500"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round">  <circle cx="12" cy="12" r="10" /></svg>
    <span class="pr-1 text-gray-800 text-sm">未アップロード</span>
@endif
</a>
