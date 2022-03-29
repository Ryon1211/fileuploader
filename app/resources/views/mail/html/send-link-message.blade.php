@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot

# {{ $head }}

{{-- Body --}}
## こんにちは　{{ $toUser }}さん
{!! nl2br($message) !!}

{{-- Message from sender --}}
@isset($userMessage)
@component('mail::panel')
### {{ $fromName }}さんからのメッセージです。
{!! nl2br(e($userMessage)) !!}
@endcomponent
@endisset

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset


{{-- button --}}
@component('mail::button', ['url' => $link])
{{ $btnText ?? 'Link' }}
@endcomponent

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent
