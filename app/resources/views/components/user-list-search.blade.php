    <x-label for="user_search" value="User" />
    <div id="selected_user" class="hidden">
        <div class="mb-3 w-11/12">
            <p id="selected_user_name" class="pl-2 font-semibold"></p>
            <p id="selected_user_email" class="pl-2 font-semibold"></p>
        </div>
        <div class="mb-3 pr-2 w-1/12 flex justify-end">
            <x-link-delete-button type="button" id="user_delete_btn"></x-link-delete-button>
        </div>
    </div>
    <input type="hidden" id="user" value="" name="user">
    <div id="message_area" class="hidden">
        <x-label for="message" :value="__('Message')" />
        <x-textarea id="message" class="block mt-1 w-full" name="message" :value="old('message')" rows="5" />
    </div>
    <x-button type="button" id="open_search_btn" class="mt-2  w-full flex justify-center">
        ユーザーを検索
    </x-button>
