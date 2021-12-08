<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            アップロードリンクの作成
        </h2>
    </x-slot>
    @if(session('uploadUrl'))
    <div class="absolute inset-0" x-data="{ modalOpen: true }" x-show="modalOpen">
        <div class="sm:px-6 lg:px-8 mb-5 absolute inset-0 bg-gray-200	bg-opacity-75 transition duration-150 ease-in-out">
            <div class="max-w-7xl mx-auto bg-white shadow-sm sm:rounded-lg absolute top-2/4 left-2/4 transform -translate-y-1/2 -translate-x-1/2">
                <span id="copy-message" class="invisible px-3 py-2 bg-blue-200 absolute top-2/4 left-2/4 transform -translate-y-1/2 -translate-x-1/2">
                  コピーしました！
                </span>
                <div class="w-full p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-end">
                        <button @click="modalOpen = !modalOpen">
                            <svg class="h-5 w-5 text-gray-500"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div>アップロードリンクを新規作成しました。</div>
                    <span id="copy-text">{{ session('uploadUrl') }}</span>
                    <x-button class="ml-4" id="copy-btn">
                        コピー
                    </x-button>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    <form method="POST" action="{{ route('user.create.upload') }}">
                        @csrf

                        <!-- Message -->
                        <div>
                            <x-label for="message" :value="__('Message')" />

                            <x-textarea id="message" class="block mt-1 w-full" name="message" :value="old('message')" rows="5" required autofocus />
                        </div>

                        <!-- Email Address -->
                        <div class="mt-4">
                            <x-label for="expire_date" :value="__('Expired date')" />

                            <x-select id="expire_date" class="block mt-1 w-full" name="expire_date" :value="old('expire_date')" :options="$options" required />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('Register') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
      let btn = document.querySelector('#copy-btn');
      let text = document.querySelector('#copy-text');
      let message = document.querySelector('#copy-message');
      if(btn){
        btn.addEventListener('click', () => {
          let innerText = text.innerText;

            if(navigator.clipboard){
              navigator.clipboard.writeText(innerText);
              message.classList.remove('invisible');
              setTimeout(() =>{
                    message.classList.add('invisible');
              },3000);
            }
        });
      }
    </script>
    <style>

    </style>
</x-app-layout>
