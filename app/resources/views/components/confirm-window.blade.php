<div id="confirm_wrap" class="absolute z-10 inset-0 bg-gray-500 bg-opacity-50 overflow-hidden invisible">
  <span class="rounded-md px-6 py-5 bg-gray-100 fixed top-2/4 left-2/4 transform -translate-y-1/2 -translate-x-1/2">
      <div class="flex justify-end">
          <button id="confirm_close_btn">
              <svg class="h-5 w-5 text-gray-500"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
          </button>
      </div>
      <p class="block w-full text-center text-xl mb-2">ファイルを削除してもよろしいですか？</p>
      <p>ファイルをすべて削除した場合、アップロードリンク自体も削除されます。</p>
      <div class="flex items-center justify-center mt-4">
          <x-button id="confirm-btn" class="ml-4 bg-red-700 hover:bg-red-800">
              <svg class="h-5 w-5 mr-2 text-gray-100 hover:text-gray-300 disabled:opacity-25 transition ease-in-out duration-150"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <line x1="4" y1="7" x2="20" y2="7" />  <line x1="10" y1="11" x2="10" y2="17" />  <line x1="14" y1="11" x2="14" y2="17" />  <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />  <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
              削除する
          </x-button>
          <x-button id="cancel-btn" class="ml-4">
              <svg class="h-5 w-5 text-gray-100"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <line x1="18" y1="6" x2="6" y2="18" />  <line x1="6" y1="6" x2="18" y2="18" /></svg>
              キャンセル
          </x-button>
      </div>
  </span>
</div>
