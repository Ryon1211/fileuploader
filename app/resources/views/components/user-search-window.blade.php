<div id="user_search_wrap" class="absolute inset-0 bg-gray-500 bg-opacity-50 overflow-hidden invisible">
    <span class="rounded-md px-6 py-5 bg-gray-100 fixed top-2/4 left-2/4 transform -translate-y-1/2 -translate-x-1/2 w-10/12">
        <div class="flex justify-end">
            <button id="user_select_close_btn">
                <svg class="h-5 w-5 text-gray-500"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <p class="block w-full text-center text-xl mb-2">

        </p>
        <p>ユーザーリストからユーザーを選択してください。</p>
        <div class="flex justify-center">
            <div class="w-full relative">
                <x-input id="user_search" class="block w-full" type="text" />
            </div>
            <x-button type="button" id="search-btn" class="mt-1 ml-4 w-1/12 flex justify-center">
                検索
            </x-button>
        </div>
        <div id="user_selector" class="mt-2 z-1 inset-x-0 bg-white invisible border rounded-md">
            <div id="user_list"></div>
    </div>
    </span>
</div>
