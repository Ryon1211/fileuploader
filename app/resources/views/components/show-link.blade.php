@props(['linkUrl', 'title', 'message', 'userMessage'])

@if(!empty($linkUrl))
<div id="show_link_wrap" class="absolute inset-0">
    <div class="sm:px-6 lg:px-8 mb-5 absolute inset-0 bg-gray-200	bg-opacity-75 transition duration-150 ease-in-out">
        <div class="max-w-7xl mx-auto absolute top-2/4 left-2/4 transform -translate-y-1/2 -translate-x-1/2">
            <x-copy-message></x-copy-message>
            <div class="w-full pt-4 pb-6 px-6 bg-white border-b border-gray-200 rounded-lg">
                <div class="flex justify-end">
                    <button id="show_link_close_btn">
                        <svg class="h-5 w-5 text-gray-500"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="pb-5 font-semibold text-xl text-gray-800 leading-tight">
                    {{ $title }}
                </div>
                <div class="pb-6">{{ $message }}</div>
                @if(!empty($userMessage))
                <div class="mb-6 p-3 border rounded-sm">
                    <p class="font-semibold	mb-2">
                        送信されたメッセージ
                    </p>
                    {!! nl2br(e($userMessage)) !!}
                </div>
                @endif
                <span id="copy_text">{{ $linkUrl }}</span>
                <x-button class="ml-4" id="copy_btn">
                    コピー
                </x-button>
            </div>
        </div>
    </div>
</div>
@endif;
